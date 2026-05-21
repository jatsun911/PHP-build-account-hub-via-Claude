@extends('layouts.app')
@section('title', 'Balance Sheet')
@section('content')
<div class="dashboard-header">
    <div>
        <h1>Balance Sheet</h1>
        <h3>Assets, liabilities and equity as of today.</h3>
    </div>
    <div style="display:flex;gap:12px;">
        <a href="{{ route('reports.trial-balance') }}" class="glass-pill" style="text-decoration:none;color:var(--text-primary);">Trial Balance</a>
        <a href="{{ route('reports.profit-loss') }}" class="glass-pill" style="text-decoration:none;color:var(--text-primary);">P&amp;L Statement</a>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;margin-top:24px;">

    {{-- Assets --}}
    <div class="glass-panel">
        <h2 style="color:#1d4ed8;margin-bottom:16px;">Assets</h2>
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="border-bottom:1px solid var(--glass-border);">
                    <th style="padding:10px 8px;color:var(--text-secondary);font-weight:500;text-align:left;">Ledger</th>
                    <th style="padding:10px 8px;color:var(--text-secondary);font-weight:500;text-align:right;">Balance (₹)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($assets as $ledger)
                @php $net = $ledger->total_debits - $ledger->total_credits; @endphp
                <tr style="border-bottom:1px solid rgba(0,0,0,0.04);">
                    <td style="padding:10px 8px;">{{ $ledger->name }}<br><small style="color:var(--text-secondary);">{{ $ledger->ledgerGroup->name ?? '' }}</small></td>
                    <td style="padding:10px 8px;text-align:right;font-weight:600;">{{ number_format($net, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="border-top:2px solid var(--glass-border);">
                    <td style="padding:12px 8px;font-weight:700;">Total Assets</td>
                    <td style="padding:12px 8px;text-align:right;font-weight:700;color:#1d4ed8;">₹{{ number_format($totalAssets, 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    {{-- Liabilities + Equity --}}
    <div style="display:flex;flex-direction:column;gap:24px;">
        <div class="glass-panel">
            <h2 style="color:#7c3aed;margin-bottom:16px;">Liabilities</h2>
            <table style="width:100%;border-collapse:collapse;">
                <tbody>
                    @foreach($liabilities as $ledger)
                    @php $net = $ledger->total_credits - $ledger->total_debits; @endphp
                    <tr style="border-bottom:1px solid rgba(0,0,0,0.04);">
                        <td style="padding:10px 8px;">{{ $ledger->name }}</td>
                        <td style="padding:10px 8px;text-align:right;font-weight:600;">{{ number_format($net, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="border-top:2px solid var(--glass-border);">
                        <td style="padding:12px 8px;font-weight:700;">Total Liabilities</td>
                        <td style="padding:12px 8px;text-align:right;font-weight:700;color:#7c3aed;">₹{{ number_format($totalLiabilities, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="glass-panel">
            <h2 style="color:#0891b2;margin-bottom:16px;">Equity</h2>
            <table style="width:100%;border-collapse:collapse;">
                <tbody>
                    @foreach($equity as $ledger)
                    @php $net = $ledger->total_credits - $ledger->total_debits; @endphp
                    <tr style="border-bottom:1px solid rgba(0,0,0,0.04);">
                        <td style="padding:10px 8px;">{{ $ledger->name }}</td>
                        <td style="padding:10px 8px;text-align:right;font-weight:600;">{{ number_format($net, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="border-top:2px solid var(--glass-border);">
                        <td style="padding:12px 8px;font-weight:700;">Total Equity</td>
                        <td style="padding:12px 8px;text-align:right;font-weight:700;color:#0891b2;">₹{{ number_format($totalEquity, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="glass-panel" style="text-align:center;padding:20px;">
            <div style="color:var(--text-secondary);margin-bottom:4px;">Liabilities + Equity</div>
            <div style="font-size:1.8rem;font-weight:700;color:var(--brand-primary);">₹{{ number_format($totalLiabilities + $totalEquity, 2) }}</div>
            @if(abs($totalAssets - ($totalLiabilities + $totalEquity)) > 0.01)
            <div style="color:#b91c1c;font-size:0.85rem;margin-top:8px;">⚠ Balance sheet does not balance (diff: ₹{{ number_format(abs($totalAssets - $totalLiabilities - $totalEquity), 2) }})</div>
            @else
            <div style="color:#059669;font-size:0.85rem;margin-top:8px;">✓ Balance sheet balances</div>
            @endif
        </div>
    </div>
</div>
@endsection
