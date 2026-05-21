@extends('layouts.app')
@section('title', 'Profit & Loss')
@section('content')

<div class="dashboard-header">
    <div>
        <h1>Profit & Loss</h1>
        <h3>Revenue, expenses, and net profit for the period.</h3>
    </div>
    <div style="display:flex;gap:10px;">
        <a href="{{ route('reports.trial-balance') }}" class="btn btn-outline">Trial Balance</a>
        <a href="{{ route('reports.balance-sheet') }}" class="btn btn-outline">Balance Sheet</a>
    </div>
</div>

<!-- Net result banner -->
<div class="card metric-card {{ $netProfit >= 0 ? 'green' : 'red' }}" style="display:flex;align-items:center;justify-content:space-between;padding:20px 28px;">
    <div>
        <h3>Net {{ $netProfit >= 0 ? 'Profit' : 'Loss' }}</h3>
        <div class="metric" style="color:{{ $netProfit >= 0 ? 'var(--green-600)' : '#dc2626' }};">
            {{ $netProfit >= 0 ? '' : '−' }}₹{{ number_format(abs($netProfit), 2) }}
        </div>
    </div>
    <div style="text-align:right;">
        <div style="font-size:0.8rem;color:var(--text-muted);margin-bottom:2px;">Revenue</div>
        <div style="font-weight:700;color:var(--green-600);">₹{{ number_format($totalRevenue, 2) }}</div>
        <div style="font-size:0.8rem;color:var(--text-muted);margin-top:8px;margin-bottom:2px;">Expenses</div>
        <div style="font-weight:700;color:#dc2626;">₹{{ number_format($totalExpenses, 2) }}</div>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">

    <!-- Revenue -->
    <div class="card" style="padding:0;overflow:hidden;">
        <div style="padding:16px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:8px;">
            <div style="width:10px;height:10px;border-radius:50%;background:var(--green-500);"></div>
            <h2 style="margin:0;">Revenue</h2>
        </div>
        <table class="data-table">
            <thead><tr><th>Ledger</th><th style="text-align:right;">Amount (₹)</th></tr></thead>
            <tbody>
                @foreach($revenues as $ledger)
                @php $net = $ledger->total_credits - $ledger->total_debits; @endphp
                @if($net != 0)
                <tr>
                    <td>{{ $ledger->name }}</td>
                    <td style="text-align:right;font-weight:600;color:var(--green-600);">{{ number_format($net, 2) }}</td>
                </tr>
                @endif
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background:var(--gray-50);border-top:2px solid var(--border);">
                    <td style="padding:12px 14px;font-weight:700;">Total Revenue</td>
                    <td style="padding:12px 14px;text-align:right;font-weight:700;color:var(--green-600);">₹{{ number_format($totalRevenue, 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- Expenses -->
    <div class="card" style="padding:0;overflow:hidden;">
        <div style="padding:16px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:8px;">
            <div style="width:10px;height:10px;border-radius:50%;background:#dc2626;"></div>
            <h2 style="margin:0;">Expenses</h2>
        </div>
        <table class="data-table">
            <thead><tr><th>Ledger</th><th style="text-align:right;">Amount (₹)</th></tr></thead>
            <tbody>
                @foreach($expenses as $ledger)
                @php $net = $ledger->total_debits - $ledger->total_credits; @endphp
                @if($net != 0)
                <tr>
                    <td>{{ $ledger->name }}</td>
                    <td style="text-align:right;font-weight:600;color:#dc2626;">{{ number_format($net, 2) }}</td>
                </tr>
                @endif
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background:var(--gray-50);border-top:2px solid var(--border);">
                    <td style="padding:12px 14px;font-weight:700;">Total Expenses</td>
                    <td style="padding:12px 14px;text-align:right;font-weight:700;color:#dc2626;">₹{{ number_format($totalExpenses, 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

</div>
@endsection
