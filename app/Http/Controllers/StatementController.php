<?php

namespace App\Http\Controllers;

use App\Models\BankStatement;
use App\Services\StatementParserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StatementController extends Controller
{
    public function upload(Request $request, StatementParserService $parser)
    {
        $request->validate([
            'statement' => 'required|file|mimes:pdf|max:10240', // 10MB Max
        ]);

        $file = $request->file('statement');
        $path = $file->store('statements', 'local');

        // Create DB Record
        $statement = BankStatement::create([
            'user_id' => 1, // Assuming logged in user ID 1 for now
            'file_path' => $path,
            'original_filename' => $file->getClientOriginalName(),
            'status' => 'pending'
        ]);

        // Run Parser
        $parser->parse($statement);

        return back()->with('success', 'Statement uploaded and parsed successfully.');
    }
}
