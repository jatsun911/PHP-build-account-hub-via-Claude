@extends('layouts.app')
@section('title', 'Profit & Loss')
@section('content')
<div class="dashboard-header">
    <div>
        <h1>Profit & Loss Statement</h1>
        <h3>Revenue, expenses, and net profit for the active period.</h3>
    </div>
    <div style="display:flex;gap:12px;">
        <a href="{{ route('reports.trial-balance') }}" class="glass-pill" style="text-decoration:none;color:var(--text-primary);">Trial Balance</a>
        <a href="{{ route('reports.balance-sheet') }}" class="glass-pill" style="text-decoration:none;color:var(--text-primary);">Balance Sheet</a>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;margin-top:24px;">

    {{-- Revenue --}}
    <div class="glass-panel">
        <h2 style="color:#059669;margin-bottom:16px;">Revenue</h2>
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="border-bottom:1px solid var(--glass-border);">
                    <th style="padding:10px 8px;color:var(--text-secondary);font-weight:500;text-align:left;">Ledger</th>
                    <th style="padding:10px 8px;color:var(--text-secondary);font-weight:500;text-align:right;">Amount (₹)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($revenues as $ledger)
                @php $net = $ledger->total_credits - $ledger->total_debits; @endphp
                @if($net != 0)
                <tr style="border-bottom:1px solid rgba(0,0,0,0.04);">
                    <td style="padding:10px 8px;">{{ $ledger->name }}</td>
                    <td style="padding:10px 8px;text-align:right;font-weight:600;color:#059669;">{{ number_format($net, 2) }}</td>
                </tr>
                @endif
                @endforeach
            </tbody>
            <tfoot>
                <tr style="border-top:2px solid var(--glass-border);">
                    <td style="padding:12px 8px;font-weight:700;">Total Revenue</td>
                    <td style="padding:12px 8px;text-align:right;font-weight:700;color:#059669;">₹{{ number_format($totalRevenue, 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    {{-- Expenses --}}
    <div class="glass-panel">
        <h2 style="color:#ef4444;margin-bottom:16px;">Expenses</h2>
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="border-bottom:1px solid var(--glass-border);">
                    <th style="padding:10px 8px;color:var(--text-secondary);font-weight:500;text-align:left;">Ledger</th>
                    <th style="padding:10px 8px;color:var(--text-secondary);font-weight:500;text-align:right;">Amount (₹)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($expenses as $ledger)
                @php $net = $ledger->total_debits - $ledger->total_credits; @endphp
                @if($net != 0)
                <tr style="border-bottom:1px solid rgba(0,0,0,0.04);">
                    <td style="padding:10px 8px;">{{ $ledger->name }}</td>
                    <td style="padding:10px 8px;text-align:right;font-weight:600;color:#ef4444;">{{ number_format($net, 2) }}</td>
                </tr>
                @endif
                @endforeach
            </tbody>
            <tfoot>
                <tr style="border-top:2px solid var(--glass-border);">
                    <td style="padding:12px 8px;font-weight:700;">Total Expenses</td>
                    <td style="padding:12px 8px;text-align:right;font-weight:700;color:#ef4444;">₹{{ number_format($totalExpenses, 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

{{-- Net Profit/Loss --}}
<div class="glass-panel" style="margin-top:24px;text-align:center;padding:32px;">
    <h2 style="margin-bottom:8px;color:var(--text-secondary);">Net {{ $netProfit >= 0 ? 'Profit' : 'Loss' }}</h2>
    <div class="metric" style="color:{{ $netProfit >= 0 ? '#059669' : '#ef4444' }};">
        {{ $netProfit >= 0 ? '' : '-' }}₹{{ number_format(abs($netProfit), 2) }}
    </div>
</div>
@endsection
