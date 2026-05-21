<?php

namespace App\Http\Controllers;

use App\Models\Ledger;
use App\Models\LedgerGroup;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $entityId = session('active_entity_id');

        $allowedSorts = ['transaction_date', 'amount', 'type', 'status', 'description'];
        $sortColumn = in_array($request->get('sort'), $allowedSorts) ? $request->get('sort') : 'transaction_date';
        $sortDirection = $request->get('direction', 'desc') === 'asc' ? 'asc' : 'desc';

        $transactions = Transaction::with('ledger')
            ->whereHas('ledger', fn($q) => $q->where('entity_id', $entityId))
            ->orderBy($sortColumn, $sortDirection)
            ->paginate(15);

        return view('transactions.index', compact('transactions', 'sortColumn', 'sortDirection'));
    }

    public function create()
    {
        $entityId = session('active_entity_id');
        $ledgers = Ledger::where('entity_id', $entityId)->orderBy('name')->get();
        return view('transactions.create', compact('ledgers'));
    }

    public function store(Request $request)
    {
        $entityId = session('active_entity_id');

        $request->validate([
            'transaction_date' => 'required|date',
            'description' => 'required|string|max:500',
            'entries' => 'required|array|min:2',
            'entries.*.ledger_id' => 'required|exists:ledgers,id',
            'entries.*.amount' => 'required|numeric|min:0.01',
            'entries.*.type' => 'required|in:debit,credit',
        ]);

        // Double-entry validation: total debits must equal total credits
        $totalDebits = collect($request->entries)->where('type', 'debit')->sum('amount');
        $totalCredits = collect($request->entries)->where('type', 'credit')->sum('amount');

        if (abs($totalDebits - $totalCredits) > 0.01) {
            return back()->withInput()->withErrors(['entries' => 'Total debits must equal total credits. Debits: ' . $totalDebits . ', Credits: ' . $totalCredits]);
        }

        // Verify all ledgers belong to the active entity
        $ledgerIds = collect($request->entries)->pluck('ledger_id')->unique();
        $validCount = Ledger::where('entity_id', $entityId)->whereIn('id', $ledgerIds)->count();
        if ($validCount !== $ledgerIds->count()) {
            abort(403, 'One or more ledgers do not belong to your active entity.');
        }

        DB::beginTransaction();
        try {
            foreach ($request->entries as $entry) {
                Transaction::create([
                    'ledger_id' => $entry['ledger_id'],
                    'amount' => $entry['amount'],
                    'type' => $entry['type'],
                    'description' => $request->description,
                    'transaction_date' => $request->transaction_date,
                    'status' => 'completed',
                    'created_by' => Auth::id(),
                ]);
            }

            DB::commit();
            return redirect()->route('transactions.index')->with('success', 'Journal entry recorded successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Failed to save transaction: ' . $e->getMessage()]);
        }
    }
}
