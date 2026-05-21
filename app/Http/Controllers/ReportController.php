<?php

namespace App\Http\Controllers;

use App\Models\Ledger;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    private function getEntityId(): string
    {
        return session('active_entity_id');
    }

    private function ledgerBalances(array $types): \Illuminate\Support\Collection
    {
        $entityId = $this->getEntityId();

        return Ledger::with('ledgerGroup')
            ->where('entity_id', $entityId)
            ->whereIn('type', $types)
            ->withSum(['transactions as total_debits' => fn($q) => $q->where('type', 'debit')], 'amount')
            ->withSum(['transactions as total_credits' => fn($q) => $q->where('type', 'credit')], 'amount')
            ->orderBy('name')
            ->get()
            ->map(function ($ledger) {
                $ledger->total_debits = (float) ($ledger->total_debits ?? 0);
                $ledger->total_credits = (float) ($ledger->total_credits ?? 0);
                $ledger->balance = $ledger->total_debits - $ledger->total_credits;
                return $ledger;
            });
    }

    public function trialBalance()
    {
        $entityId = $this->getEntityId();

        $ledgers = Ledger::with('ledgerGroup')
            ->where('entity_id', $entityId)
            ->withSum(['transactions as total_debits' => fn($q) => $q->where('type', 'debit')], 'amount')
            ->withSum(['transactions as total_credits' => fn($q) => $q->where('type', 'credit')], 'amount')
            ->orderBy('type')
            ->orderBy('name')
            ->get()
            ->map(function ($ledger) {
                $ledger->total_debits = (float) ($ledger->total_debits ?? 0);
                $ledger->total_credits = (float) ($ledger->total_credits ?? 0);
                return $ledger;
            });

        $grandDebit = $ledgers->sum('total_debits');
        $grandCredit = $ledgers->sum('total_credits');

        return view('reports.trial_balance', compact('ledgers', 'grandDebit', 'grandCredit'));
    }

    public function profitLoss()
    {
        $revenues = $this->ledgerBalances(['revenue']);
        $expenses = $this->ledgerBalances(['expense']);

        // Revenue: net = credits - debits (income is credit-normal)
        $totalRevenue = $revenues->sum(fn($l) => $l->total_credits - $l->total_debits);
        // Expense: net = debits - credits (expense is debit-normal)
        $totalExpenses = $expenses->sum(fn($l) => $l->total_debits - $l->total_credits);
        $netProfit = $totalRevenue - $totalExpenses;

        return view('reports.profit_loss', compact('revenues', 'expenses', 'totalRevenue', 'totalExpenses', 'netProfit'));
    }

    public function balanceSheet()
    {
        $assets = $this->ledgerBalances(['asset']);
        $liabilities = $this->ledgerBalances(['liability']);
        $equity = $this->ledgerBalances(['equity']);

        // Assets: debit-normal
        $totalAssets = $assets->sum(fn($l) => $l->total_debits - $l->total_credits);
        // Liabilities: credit-normal
        $totalLiabilities = $liabilities->sum(fn($l) => $l->total_credits - $l->total_debits);
        // Equity: credit-normal
        $totalEquity = $equity->sum(fn($l) => $l->total_credits - $l->total_debits);

        return view('reports.balance_sheet', compact('assets', 'liabilities', 'equity', 'totalAssets', 'totalLiabilities', 'totalEquity'));
    }
}
