<?php

namespace App\Services;

use App\Models\BankStatement;
use App\Models\Transaction;
use App\Models\Ledger;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Smalot\PdfParser\Parser;
use Spatie\PdfToImage\Pdf;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class StatementParserService
{
    public function parse(BankStatement $statement): void
    {
        try {
            $statement->update(['status' => 'processing']);
            
            $filePath = Storage::disk('local')->path($statement->file_path);
            
            $suspenseLedger = Ledger::where('name', 'Suspense A/c(System)')->first();
            if (!$suspenseLedger) {
                throw new \Exception("System ledger 'Suspense A/c(System)' not found. Please run seeders.");
            }
            
            $parser = new Parser();
            $pdf = $parser->parseFile($filePath);
            $pages = $pdf->getPages();
            
            $pdfImage = new Pdf($filePath);
            $pdfImage->resolution(150); // Image DPI
            
            $manager = new ImageManager(new Driver());
            $snippets = [];
            $transactionCount = 0;
            
            $dpiMultiplier = 150 / 72.0;
            
            foreach ($pages as $pageIndex => $page) {
                $pageNumber = $pageIndex + 1;
                
                // Get page details for dimensions
                $details = $page->getDetails();
                $pageHeightPts = isset($details['MediaBox'][3]) ? floatval($details['MediaBox'][3]) : 841.89; // Default A4 height
                
                // Extract structured text with coordinates
                $dataTm = $page->getDataTm();
                
                // Group by lines based on Y-coordinate (tolerance 3.0 pts)
                $lines = [];
                foreach ($dataTm as $item) {
                    if (!isset($item[0][5]) || !isset($item[0][4]) || !isset($item[1])) continue;
                    
                    $y = round(floatval($item[0][5]), 2);
                    $x = round(floatval($item[0][4]), 2);
                    $text = trim($item[1]);
                    
                    if (empty($text)) continue;
                    
                    $foundLine = false;
                    foreach ($lines as $lineY => &$lineItems) {
                        if (abs($lineY - $y) <= 3.0) {
                            $lineItems[] = ['x' => $x, 'text' => $text, 'y' => $y];
                            $foundLine = true;
                            break;
                        }
                    }
                    if (!$foundLine) {
                        $lines[(string)$y] = [ ['x' => $x, 'text' => $text, 'y' => $y] ];
                    }
                }
                
                // Sort lines from top of the page to bottom (Y decreases from bottom to top in PDF)
                krsort($lines);
                
                $transactions = [];
                $currentTxn = null;
                
                foreach ($lines as $lineY => $items) {
                    // Sort items in the line by X coordinate
                    usort($items, fn($a, $b) => $a['x'] <=> $b['x']);
                    
                    $lineStr = implode(" ", array_column($items, 'text'));
                    
                    // Identify Date (usually X between 35 and 55)
                    $dateText = null;
                    $narrationText = [];
                    $withdrawalAmt = null;
                    $depositAmt = null;
                    
                    foreach ($items as $item) {
                        if ($item['x'] > 30 && $item['x'] < 60 && preg_match('/^\d{2}\/\d{2}\/\d{2,4}$/', $item['text'])) {
                            $dateText = $item['text'];
                        } elseif ($item['x'] > 130 && $item['x'] < 280) {
                            $narrationText[] = $item['text'];
                        } elseif ($item['x'] > 390 && $item['x'] < 460) {
                            $withdrawalAmt = str_replace(',', '', $item['text']);
                        } elseif ($item['x'] > 470 && $item['x'] < 550) {
                            $depositAmt = str_replace(',', '', $item['text']);
                        }
                    }
                    
                    if ($dateText) {
                        // This is a new transaction row
                        if ($currentTxn) {
                            $transactions[] = $currentTxn;
                        }
                        
                        $amount = null;
                        $type = null;
                        
                        if (is_numeric($withdrawalAmt)) {
                            $amount = (float)$withdrawalAmt;
                            $type = 'debit';
                        } elseif (is_numeric($depositAmt)) {
                            $amount = (float)$depositAmt;
                            $type = 'credit';
                        }
                        
                        // Only add if we found a valid amount
                        if ($amount !== null) {
                            $currentTxn = [
                                'date' => \Carbon\Carbon::createFromFormat('d/m/y', $dateText)->format('Y-m-d'),
                                'description' => implode(" ", $narrationText),
                                'amount' => $amount,
                                'type' => $type,
                                'max_y' => (float)$lineY, // Highest Y coordinate (top of row)
                                'min_y' => (float)$lineY, // Lowest Y coordinate (bottom of row)
                            ];
                        }
                    } elseif ($currentTxn && !empty($narrationText)) {
                        // This is a continuation of the previous transaction's narration
                        $currentTxn['description'] .= ' ' . implode(" ", $narrationText);
                        
                        // Update the minimum Y bound for cropping
                        if ((float)$lineY < $currentTxn['min_y']) {
                            $currentTxn['min_y'] = (float)$lineY;
                        }
                    }
                }
                
                // Push the last transaction
                if ($currentTxn) {
                    $transactions[] = $currentTxn;
                }
                
                if (empty($transactions)) {
                    continue; // Skip image processing if no transactions on this page
                }
                
                // Process Image for this page
                $tempImagePath = storage_path("app/private/temp_page_{$statement->id}_{$pageNumber}.jpg");
                $pdfImage->selectPage($pageNumber)->save($tempImagePath);
                $image = $manager->decodePath($tempImagePath);
                $imgWidth = $image->width();
                $imgHeight = $image->height();
                
                foreach ($transactions as $txn) {
                    $transactionCount++;
                    
                    // Calculate pixel bounds for Intervention Crop
                    // Add padding: 10 pts above, 5 pts below
                    $pdfTopY = $txn['max_y'] + 10;
                    $pdfBottomY = $txn['min_y'] - 5;
                    
                    // Y from top in PDF pts
                    $pdfPtsFromTop = $pageHeightPts - $pdfTopY; 
                    
                    // Convert to pixels
                    $cropY = intval($pdfPtsFromTop * $dpiMultiplier);
                    $cropHeight = intval(($pdfTopY - $pdfBottomY) * $dpiMultiplier);
                    
                    // Safety bounds
                    if ($cropY < 0) $cropY = 0;
                    if ($cropY + $cropHeight > $imgHeight) $cropHeight = $imgHeight - $cropY;
                    
                    $snippetFilename = "snippets/stmt_{$statement->id}_txn_{$transactionCount}.webp";
                    $snippetPath = Storage::disk('public')->path($snippetFilename);
                    
                    if (!file_exists(dirname($snippetPath))) {
                        mkdir(dirname($snippetPath), 0755, true);
                    }
                    
                    // Crop: width, height, x, y
                    $imageCopy = $manager->decodePath($tempImagePath);
                    $imageCopy->crop($imgWidth, $cropHeight, 0, $cropY);
                    $imageCopy->save($snippetPath, 75);
                    
                    $snippetUrl = Storage::url($snippetFilename);
                    
                    // Save to DB
                    $transaction = Transaction::create([
                        'transaction_id' => Str::uuid(),
                        'ledger_id' => $suspenseLedger->id,
                        'amount' => $txn['amount'],
                        'type' => $txn['type'],
                        'description' => trim($txn['description']),
                        'transaction_date' => $txn['date'],
                        'attached_document_path' => $snippetUrl,
                        'status' => 'pending',
                        'created_by' => $statement->user_id,
                    ]);
                    
                    $snippets[] = [
                        'transaction_id' => $transaction->transaction_id,
                        'snippet_url' => $snippetUrl,
                        'amount' => $txn['amount'],
                        'type' => $txn['type'],
                        'date' => $txn['date'],
                        'description' => trim($txn['description']),
                    ];
                }
                
                @unlink($tempImagePath);
            }
            
            $statement->update([
                'status' => 'completed',
                'extracted_data' => $snippets,
                'transaction_count' => $transactionCount
            ]);
            
        } catch (\Exception $e) {
            $statement->update([
                'status' => 'failed',
                'extracted_data' => ['error' => $e->getMessage() . ' on line ' . $e->getLine()]
            ]);
            
            report($e);
        }
    }
}
