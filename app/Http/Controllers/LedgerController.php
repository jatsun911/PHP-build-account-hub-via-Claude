<?php

namespace App\Http\Controllers;

use App\Models\Ledger;
use App\Models\LedgerGroup;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LedgerController extends Controller
{
    public function index()
    {
        $entityId = session('active_entity_id');
        $ledgers = Ledger::with('ledgerGroup')
            ->where('entity_id', $entityId)
            ->orderBy('name')
            ->paginate(20);
        $groups = LedgerGroup::where(function ($q) use ($entityId) {
            $q->where('entity_id', $entityId)->orWhereNull('entity_id');
        })->orderBy('type')->orderBy('name')->get();
        return view('ledgers.index', compact('ledgers', 'groups'));
    }

    public function create()
    {
        $entityId = session('active_entity_id');
        $groups = LedgerGroup::where(function ($q) use ($entityId) {
            $q->where('entity_id', $entityId)->orWhereNull('entity_id');
        })->orderBy('type')->orderBy('name')->get();
        return view('ledgers.create', compact('groups'));
    }

    public function store(Request $request)
    {
        $entityId = session('active_entity_id');

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
            $openingBalanceLedger = Ledger::where('name', 'Opening Balance Differences A/c(System)')
                ->where('entity_id', $entityId)
                ->first();

            if (!$openingBalanceLedger) {
                throw new \Exception("System ledger 'Opening Balance Differences A/c(System)' not found. Please run seeders.");
            }

            foreach ($request->ledgers as $ledgerData) {
                $groupId = $ledgerData['ledger_group_id'] ?? null;
                if (!$groupId) {
                    $suspenseGroup = LedgerGroup::firstOrCreate(
                        ['name' => 'Suspense (default)', 'entity_id' => null],
                        ['type' => 'equity']
                    );
                    $groupId = $suspenseGroup->id;
                }

                $group = LedgerGroup::find($groupId);

                $ledger = Ledger::create([
                    'entity_id' => $entityId,
                    'name' => $ledgerData['name'],
                    'code' => Str::upper(Str::random(6)),
                    'type' => $group->type,
                    'ledger_group_id' => $groupId,
                    'description' => $ledgerData['notes'] ?? null,
                ]);

                $dr = floatval($ledgerData['dr_balance'] ?? 0);
                $cr = floatval($ledgerData['cr_balance'] ?? 0);

                if ($dr > 0 || $cr > 0) {
                    $openingDate = now()->startOfYear()->format('Y-04-01');
                    $amount = $dr > 0 ? $dr : $cr;
                    $type = $dr > 0 ? 'debit' : 'credit';

                    Transaction::create([
                        'ledger_id' => $ledger->id,
                        'amount' => $amount,
                        'type' => $type,
                        'description' => 'Opening Balance',
                        'transaction_date' => $openingDate,
                        'status' => 'completed',
                        'created_by' => Auth::id(),
                    ]);

                    Transaction::create([
                        'ledger_id' => $openingBalanceLedger->id,
                        'amount' => $amount,
                        'type' => $type === 'debit' ? 'credit' : 'debit',
                        'description' => 'Opening Balance Counter-Entry for ' . $ledger->name,
                        'transaction_date' => $openingDate,
                        'status' => 'completed',
                        'created_by' => Auth::id(),
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
        $entityId = session('active_entity_id');

        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:asset,liability,equity,revenue,expense',
        ]);

        LedgerGroup::firstOrCreate(
            ['name' => $request->name, 'entity_id' => $entityId],
            ['type' => $request->type]
        );

        return back()->with('success', 'Ledger Group created successfully!');
    }
}
