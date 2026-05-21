@extends('layouts.app')
@section('title', 'Trial Balance')
@section('content')
<div class="dashboard-header">
    <div>
        <h1>Trial Balance</h1>
        <h3>All ledger accounts with their debit and credit totals.</h3>
    </div>
    <div style="display:flex;gap:12px;">
        <a href="{{ route('reports.profit-loss') }}" class="glass-pill" style="text-decoration:none;color:var(--text-primary);">P&amp;L Statement</a>
        <a href="{{ route('reports.balance-sheet') }}" class="glass-pill" style="text-decoration:none;color:var(--text-primary);">Balance Sheet</a>
    </div>
</div>

@if(session('error') || $errors->any())
    <div style="background:#fee2e2;border:1px solid #fca5a5;color:#b91c1c;padding:12px 20px;border-radius:8px;margin-top:16px;">
        {{ session('error') ?? $errors->first() }}
    </div>
@endif

<div class="glass-panel" style="margin-top:24px;">
    <table style="width:100%;border-collapse:collapse;text-align:left;">
        <thead>
            <tr style="border-bottom:1px solid var(--glass-border);">
                <th style="padding:14px 8px;color:var(--text-secondary);font-weight:500;">Ledger</th>
                <th style="padding:14px 8px;color:var(--text-secondary);font-weight:500;">Group</th>
                <th style="padding:14px 8px;color:var(--text-secondary);font-weight:500;">Type</th>
                <th style="padding:14px 8px;color:var(--text-secondary);font-weight:500;text-align:right;">Debit (₹)</th>
                <th style="padding:14px 8px;color:var(--text-secondary);font-weight:500;text-align:right;">Credit (₹)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($ledgers as $ledger)
            <tr style="border-bottom:1px solid rgba(0,0,0,0.05);" onmouseover="this.style.background='rgba(0,0,0,0.02)'" onmouseout="this.style.background=''">
                <td style="padding:13px 8px;font-weight:500;">{{ $ledger->name }}</td>
                <td style="padding:13px 8px;color:var(--text-secondary);">{{ $ledger->ledgerGroup->name ?? '—' }}</td>
                <td style="padding:13px 8px;">
                    <span class="glass-pill" style="font-size:0.72rem;padding:4px 10px;">{{ ucfirst($ledger->type) }}</span>
                </td>
                <td style="padding:13px 8px;text-align:right;font-weight:600;color:#1d4ed8;">
                    {{ $ledger->total_debits > 0 ? number_format($ledger->total_debits, 2) : '—' }}
                </td>
                <td style="padding:13px 8px;text-align:right;font-weight:600;color:#059669;">
                    {{ $ledger->total_credits > 0 ? number_format($ledger->total_credits, 2) : '—' }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="padding:32px;text-align:center;color:var(--text-secondary);">
                    No ledgers found. <a href="{{ route('ledgers.create') }}" style="color:var(--brand-primary);">Create ledgers</a> first.
                </td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="border-top:2px solid var(--glass-border);background:rgba(0,0,0,0.02);">
                <td colspan="3" style="padding:14px 8px;font-weight:700;">Grand Total</td>
                <td style="padding:14px 8px;text-align:right;font-weight:700;color:#1d4ed8;">₹{{ number_format($grandDebit, 2) }}</td>
                <td style="padding:14px 8px;text-align:right;font-weight:700;color:#059669;">₹{{ number_format($grandCredit, 2) }}</td>
            </tr>
            @if(abs($grandDebit - $grandCredit) > 0.01)
            <tr>
                <td colspan="5" style="padding:10px 8px;color:#b91c1c;font-weight:600;text-align:center;">
                    ⚠ Trial balance is off by ₹{{ number_format(abs($grandDebit - $grandCredit), 2) }}. Check your opening entries.
                </td>
            </tr>
            @endif
        </tfoot>
    </table>
</div>
@endsection
