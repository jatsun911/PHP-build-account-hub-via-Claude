@extends('layouts.app')

@section('title', 'Transactions')

@section('content')
    <div class="dashboard-header" style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1>All Transactions</h1>
            <h3>Review and categorize extracted bank statements.</h3>
        </div>
    </div>

    <div class="glass-panel" style="margin-top: 24px;">
        
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="border-bottom: 1px solid var(--glass-border);">
                    <th style="padding: 16px 8px; font-weight: 500; color: var(--text-secondary);">
                        <a href="{{ route('transactions.index', ['sort' => 'transaction_date', 'direction' => $sortColumn === 'transaction_date' && $sortDirection === 'desc' ? 'asc' : 'desc']) }}" style="color: inherit; text-decoration: none;">
                            Date @if($sortColumn === 'transaction_date') {!! $sortDirection === 'asc' ? '↑' : '↓' !!} @endif
                        </a>
                    </th>
                    <th style="padding: 16px 8px; font-weight: 500; color: var(--text-secondary);">Description</th>
                    <th style="padding: 16px 8px; font-weight: 500; color: var(--text-secondary);">Ledger</th>
                    <th style="padding: 16px 8px; font-weight: 500; color: var(--text-secondary);">
                        <a href="{{ route('transactions.index', ['sort' => 'amount', 'direction' => $sortColumn === 'amount' && $sortDirection === 'desc' ? 'asc' : 'desc']) }}" style="color: inherit; text-decoration: none;">
                            Amount @if($sortColumn === 'amount') {!! $sortDirection === 'asc' ? '↑' : '↓' !!} @endif
                        </a>
                    </th>
                    <th style="padding: 16px 8px; font-weight: 500; color: var(--text-secondary);">Status</th>
                    <th style="padding: 16px 8px; font-weight: 500; color: var(--text-secondary);">Original Doc</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $transaction)
                    <tr style="border-bottom: 1px solid rgba(0,0,0,0.05); transition: background 0.2s;" onmouseover="this.style.background='rgba(0,0,0,0.02)'" onmouseout="this.style.background='transparent'">
                        <td style="padding: 16px 8px;">{{ \Carbon\Carbon::parse($transaction->transaction_date)->format('M d, Y') }}</td>
                        <td style="padding: 16px 8px; font-weight: 500;">{{ $transaction->description }}</td>
                        <td style="padding: 16px 8px; color: var(--text-secondary);">{{ $transaction->ledger->name ?? 'Uncategorized' }}</td>
                        <td style="padding: 16px 8px; font-weight: 600; color: {{ $transaction->type === 'credit' ? '#059669' : '#ef4444' }};">
                            {{ $transaction->type === 'credit' ? '+' : '-' }} ₹{{ number_format($transaction->amount, 2) }}
                        </td>
                        <td style="padding: 16px 8px;">
                            <span class="glass-pill" style="font-size: 0.75rem; background: hsla(45, 100%, 50%, 0.1); color: #d97706; border-color: #fbbf24;">
                                {{ ucfirst($transaction->status) }}
                            </span>
                        </td>
                        <td style="padding: 16px 8px; position: relative;" class="transaction-row">
                            @if($transaction->attached_document_path)
                                <button style="background: none; border: none; cursor: pointer; color: var(--brand-primary); display: flex; align-items: center; gap: 4px;">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                                    View Snippet
                                </button>
                                
                                <div class="hover-snippet" style="display: none; position: absolute; right: 100%; top: 50%; transform: translateY(-50%); z-index: 50; margin-right: 16px; background: white; padding: 8px; border-radius: 8px; box-shadow: 0 10px 25px rgba(0,0,0,0.15); border: 1px solid var(--glass-border); width: 600px;">
                                    <img src="{{ $transaction->attached_document_path }}" alt="PDF Snippet" style="width: 100%; height: auto; border: 1px solid #eee;">
                                </div>
                            @else
                                <span style="color: var(--text-secondary); font-size: 0.875rem;">No document</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="padding: 32px 8px; text-align: center; color: var(--text-secondary);">
                            No transactions found. Upload a bank statement to generate data.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div style="margin-top: 24px;">
            {{ $transactions->links() }}
        </div>
    </div>

    <!-- Inline script for hover effect -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const rows = document.querySelectorAll('.transaction-row');
            rows.forEach(row => {
                const snippet = row.querySelector('.hover-snippet');
                if(snippet) {
                    row.addEventListener('mouseenter', () => snippet.style.display = 'block');
                    row.addEventListener('mouseleave', () => snippet.style.display = 'none');
                }
            });
        });
    </script>
@endsection
