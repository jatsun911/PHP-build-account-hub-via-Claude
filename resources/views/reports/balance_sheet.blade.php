@extends('layouts.app')
@section('title', 'Balance Sheet')
@section('content')

<div class="dashboard-header">
    <div>
        <h1>Balance Sheet</h1>
        <h3>Assets, liabilities and equity as of today.</h3>
    </div>
    <div style="display:flex;gap:10px;">
        <a href="{{ route('reports.trial-balance') }}" class="btn btn-outline">Trial Balance</a>
        <a href="{{ route('reports.profit-loss') }}" class="btn btn-outline">P&amp;L</a>
    </div>
</div>

<!-- Balance status banner -->
@php $diff = abs($totalAssets - ($totalLiabilities + $totalEquity)); @endphp
<div class="alert {{ $diff < 0.01 ? 'alert-success' : 'alert-error' }}">
    @if($diff < 0.01)
        ✓ Balance sheet is in balance — Assets = Liabilities + Equity = ₹{{ number_format($totalAssets, 2) }}
    @else
        ⚠ Out of balance by ₹{{ number_format($diff, 2) }}. Check your ledger entries.
    @endif
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">

    <!-- Assets -->
    <div class="card" style="padding:0;overflow:hidden;">
        <div style="padding:16px 20px;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;">
            <h2 style="margin:0;">Assets</h2>
            <span style="font-weight:700;color:var(--blue-700);">₹{{ number_format($totalAssets, 2) }}</span>
        </div>
        <table class="data-table">
            <thead><tr><th>Ledger</th><th>Group</th><th style="text-align:right;">Balance (₹)</th></tr></thead>
            <tbody>
                @foreach($assets as $ledger)
                @php $net = $ledger->total_debits - $ledger->total_credits; @endphp
                <tr>
                    <td style="font-weight:500;">{{ $ledger->name }}</td>
                    <td style="color:var(--text-muted);font-size:0.82rem;">{{ $ledger->ledgerGroup->name ?? '—' }}</td>
                    <td style="text-align:right;font-weight:600;">{{ number_format($net, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background:var(--blue-50);border-top:2px solid var(--blue-100);">
                    <td colspan="2" style="padding:12px 14px;font-weight:700;color:var(--blue-800);">Total Assets</td>
                    <td style="padding:12px 14px;text-align:right;font-weight:700;color:var(--blue-700);">₹{{ number_format($totalAssets, 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- Liabilities + Equity -->
    <div style="display:flex;flex-direction:column;gap:20px;">

        <div class="card" style="padding:0;overflow:hidden;">
            <div style="padding:16px 20px;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;">
                <h2 style="margin:0;">Liabilities</h2>
                <span style="font-weight:700;color:#7c3aed;">₹{{ number_format($totalLiabilities, 2) }}</span>
            </div>
            <table class="data-table">
                <tbody>
                    @foreach($liabilities as $ledger)
                    @php $net = $ledger->total_credits - $ledger->total_debits; @endphp
                    <tr>
                        <td style="font-weight:500;">{{ $ledger->name }}</td>
                        <td style="text-align:right;font-weight:600;">{{ number_format($net, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="background:var(--gray-50);border-top:2px solid var(--border);">
                        <td style="padding:12px 14px;font-weight:700;">Total Liabilities</td>
                        <td style="padding:12px 14px;text-align:right;font-weight:700;color:#7c3aed;">₹{{ number_format($totalLiabilities, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="card" style="padding:0;overflow:hidden;">
            <div style="padding:16px 20px;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;">
                <h2 style="margin:0;">Equity</h2>
                <span style="font-weight:700;color:var(--green-600);">₹{{ number_format($totalEquity, 2) }}</span>
            </div>
            <table class="data-table">
                <tbody>
                    @foreach($equity as $ledger)
                    @php $net = $ledger->total_credits - $ledger->total_debits; @endphp
                    <tr>
                        <td style="font-weight:500;">{{ $ledger->name }}</td>
                        <td style="text-align:right;font-weight:600;">{{ number_format($net, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="background:var(--green-50);border-top:2px solid var(--green-100);">
                        <td style="padding:12px 14px;font-weight:700;color:var(--green-700);">Total Equity</td>
                        <td style="padding:12px 14px;text-align:right;font-weight:700;color:var(--green-600);">₹{{ number_format($totalEquity, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

    </div>
</div>
@endsection
