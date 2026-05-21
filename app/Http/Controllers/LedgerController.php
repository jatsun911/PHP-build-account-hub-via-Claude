<?php

namespace App\Http\Controllers;

use App\Models\Ledger;
use App\Models\LedgerGroup;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class LedgerController extends Controller
{
    public function index()
    {
        $ledgers = Ledger::with('ledgerGroup')->orderBy('name')->paginate(20);
        $groups = LedgerGroup::orderBy('type')->orderBy('name')->get(); // for the group creation modal
        return view('ledgers.index', compact('ledgers', 'groups'));
    }

    public function create()
    {
        $groups = LedgerGroup::orderBy('type')->orderBy('name')->get();
        return view('ledgers.create', compact('groups'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'ledgers' => 'required|array',
            'ledgers.*.name' => 'required|string|max:255',
            'ledgers.*.ledger_group_id' => 'nullable|exists:ledger_groups,id',
            'ledgers.*.dr_balance' => 'nullable|numeric|min:0',
            'ledgers.*.cr_balance' => 'nullable|numeric|min:0',
            'ledgers.*.notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Use the hardcoded Opening Balance Differences A/c(System)
            $openingBalanceLedger = Ledger::where('name', 'Opening Balance Differences A/c(System)')->first();
            
            if (!$openingBalanceLedger) {
                 throw new \Exception("System ledger 'Opening Balance Differences A/c(System)' not found. Please run seeders.");
            }

            foreach ($request->ledgers as $ledgerData) {
                // Ensure default group is Suspense if null
                $groupId = $ledgerData['ledger_group_id'];
                if (!$groupId) {
                    $suspenseGroup = LedgerGroup::firstOrCreate(['name' => 'Suspense (default)'], ['type' => 'equity']);
                    $groupId = $suspenseGroup->id;
                }

                $group = LedgerGroup::find($groupId);

                // Create the Ledger
                $ledger = Ledger::create([
                    'name' => $ledgerData['name'],
                    'code' => Str::upper(Str::random(6)), // Simple auto-code
                    'type' => $group->type,
                    'ledger_group_id' => $groupId,
                    'description' => $ledgerData['notes'] ?? null,
                ]);

                // Handle Opening Balances Double-Entry
                $dr = floatval($ledgerData['dr_balance'] ?? 0);
                $cr = floatval($ledgerData['cr_balance'] ?? 0);

                if ($dr > 0 || $cr > 0) {
                    // Constant opening date as requested
                    $openingDate = '2024-04-01'; 
                    
                    // The Transaction for the new ledger
                    $txnId = Str::uuid();
                    $amount = $dr > 0 ? $dr : $cr;
                    $type = $dr > 0 ? 'debit' : 'credit';
                    
                    Transaction::create([
                        'transaction_id' => $txnId,
                        'ledger_id' => $ledger->id,
                        'amount' => $amount,
                        'type' => $type,
                        'description' => 'Opening Balance',
                        'transaction_date' => $openingDate,
                        'status' => 'completed',
                        'created_by' => 1, // System or current user
                    ]);

                    // The Counter-Transaction for Opening Balance Differences
                    $counterTxnId = Str::uuid();
                    $counterType = $type === 'debit' ? 'credit' : 'debit';
                    
                    Transaction::create([
                        'transaction_id' => $counterTxnId,
                        'ledger_id' => $openingBalanceLedger->id,
                        'amount' => $amount,
                        'type' => $counterType,
                        'description' => 'Opening Balance Counter-Entry for ' . $ledger->name,
                        'transaction_date' => $openingDate,
                        'status' => 'completed',
                        'created_by' => 1,
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('ledgers.create')->with('success', 'Ledgers created successfully with opening balances!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Failed to create ledgers: ' . $e->getMessage()]);
        }
    }

    public function storeGroup(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:ledger_groups,name',
            'type' => 'required|in:asset,liability,equity,revenue,expense',
        ]);

        LedgerGroup::create($request->only('name', 'type'));

        return back()->with('success', 'Ledger Group created successfully!');
    }
}
