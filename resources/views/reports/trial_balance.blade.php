@extends('layouts.app')
@section('title', 'Trial Balance')
@section('content')

<div class="dashboard-header">
    <div>
        <h1>Trial Balance</h1>
        <h3>Aggregate debit and credit totals for all ledgers.</h3>
    </div>
    <div style="display:flex;gap:10px;">
        <a href="{{ route('reports.profit-loss') }}" class="btn btn-outline">P&amp;L Statement</a>
        <a href="{{ route('reports.balance-sheet') }}" class="btn btn-outline">Balance Sheet</a>
    </div>
</div>

<div class="card" style="padding:0;overflow:hidden;">
    <table class="data-table">
        <thead>
            <tr>
                <th>Ledger Account</th>
                <th>Group</th>
                <th>Type</th>
                <th style="text-align:right;">Debit (₹)</th>
                <th style="text-align:right;">Credit (₹)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($ledgers as $ledger)
            <tr>
                <td style="font-weight:500;">{{ $ledger->name }}</td>
                <td style="color:var(--text-muted);font-size:0.83rem;">{{ $ledger->ledgerGroup->name ?? '—' }}</td>
                <td>
                    @php $typeColors = ['asset'=>'','liability'=>'badge-yellow','equity'=>'badge-gray','revenue'=>'badge-green','expense'=>'badge-red']; @endphp
                    <span class="badge {{ $typeColors[$ledger->type] ?? '' }}">{{ ucfirst($ledger->type) }}</span>
                </td>
                <td style="text-align:right;font-weight:600;color:var(--blue-700);">
                    {{ $ledger->total_debits > 0 ? number_format($ledger->total_debits, 2) : '—' }}
                </td>
                <td style="text-align:right;font-weight:600;color:var(--green-600);">
                    {{ $ledger->total_credits > 0 ? number_format($ledger->total_credits, 2) : '—' }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align:center;padding:48px;color:var(--text-muted);">
                    No ledgers found. <a href="{{ route('ledgers.create') }}" style="color:var(--blue-600);">Create ledgers →</a>
                </td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background:var(--gray-50);border-top:2px solid var(--border);">
                <td colspan="3" style="padding:14px;font-weight:700;">Grand Total</td>
                <td style="padding:14px;text-align:right;font-weight:700;color:var(--blue-700);">₹{{ number_format($grandDebit, 2) }}</td>
                <td style="padding:14px;text-align:right;font-weight:700;color:var(--green-600);">₹{{ number_format($grandCredit, 2) }}</td>
            </tr>
            @if(abs($grandDebit - $grandCredit) > 0.01)
            <tr>
                <td colspan="5" style="padding:10px 14px;text-align:center;" class="alert alert-error">
                    ⚠ Trial balance is off by ₹{{ number_format(abs($grandDebit - $grandCredit), 2) }}. Review your opening entries.
                </td>
            </tr>
            @else
            <tr>
                <td colspan="5" style="padding:10px 14px;text-align:center;" class="alert alert-success">
                    ✓ Trial balance is balanced.
                </td>
            </tr>
            @endif
        </tfoot>
    </table>
</div>
@endsection
