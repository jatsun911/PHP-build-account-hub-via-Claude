@extends('layouts.app')
@section('title', 'Transactions')
@section('content')

<div class="dashboard-header">
    <div>
        <h1>Transactions</h1>
        <h3>All journal entries for the active entity.</h3>
    </div>
    <a href="{{ route('transactions.create') }}" class="btn">+ Journal Entry</a>
</div>

<div class="card" style="padding:0;overflow:hidden;">
    <table class="data-table">
        <thead>
            <tr>
                <th>
                    <a href="{{ route('transactions.index', ['sort'=>'transaction_date','direction'=>$sortColumn==='transaction_date'&&$sortDirection==='desc'?'asc':'desc']) }}"
                       style="color:inherit;text-decoration:none;">
                        Date @if($sortColumn==='transaction_date') {{ $sortDirection==='asc'?'↑':'↓' }} @endif
                    </a>
                </th>
                <th>Description</th>
                <th>Ledger</th>
                <th style="text-align:right;">
                    <a href="{{ route('transactions.index', ['sort'=>'amount','direction'=>$sortColumn==='amount'&&$sortDirection==='desc'?'asc':'desc']) }}"
                       style="color:inherit;text-decoration:none;">
                        Amount @if($sortColumn==='amount') {{ $sortDirection==='asc'?'↑':'↓' }} @endif
                    </a>
                </th>
                <th>Status</th>
                <th>Doc</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $txn)
            <tr class="txn-row">
                <td style="color:var(--text-muted);font-size:0.82rem;white-space:nowrap;">
                    {{ \Carbon\Carbon::parse($txn->transaction_date)->format('d M Y') }}
                </td>
                <td style="font-weight:500;max-width:240px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                    {{ $txn->description }}
                </td>
                <td style="color:var(--text-muted);font-size:0.85rem;">{{ $txn->ledger->name ?? '—' }}</td>
                <td style="text-align:right;font-weight:600;white-space:nowrap;color:{{ $txn->type==='credit'?'var(--green-600)':'#dc2626' }};">
                    {{ $txn->type==='credit' ? '+' : '−' }} ₹{{ number_format($txn->amount, 2) }}
                </td>
                <td>
                    <span class="badge {{ $txn->status==='completed' ? 'badge-green' : 'badge-yellow' }}">
                        {{ ucfirst($txn->status) }}
                    </span>
                </td>
                <td style="position:relative;">
                    @if($txn->attached_document_path)
                        <button style="background:none;border:none;cursor:pointer;color:var(--blue-600);display:flex;align-items:center;gap:4px;font-size:0.8rem;font-weight:500;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                            View
                        </button>
                        <div class="hover-snippet" style="display:none;position:absolute;right:100%;top:50%;transform:translateY(-50%);z-index:50;margin-right:12px;background:white;padding:8px;border-radius:8px;box-shadow:var(--shadow-lg);border:1px solid var(--border);width:480px;">
                            <img src="{{ $txn->attached_document_path }}" alt="Snippet" style="width:100%;height:auto;border-radius:4px;">
                        </div>
                    @else
                        <span style="color:var(--gray-300);font-size:0.8rem;">—</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align:center;padding:48px;color:var(--text-muted);">
                    No transactions found.
                    <a href="{{ route('transactions.create') }}" style="color:var(--blue-600);margin-left:4px;">Create a journal entry →</a>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div style="padding:14px 20px;border-top:1px solid var(--border);">
        {{ $transactions->links() }}
    </div>
</div>

<script>
document.querySelectorAll('.txn-row').forEach(row => {
    const snippet = row.querySelector('.hover-snippet');
    if (!snippet) return;
    row.addEventListener('mouseenter', () => snippet.style.display = 'block');
    row.addEventListener('mouseleave', () => snippet.style.display = 'none');
});
</script>
@endsection
