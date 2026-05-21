@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')

<div class="dashboard-header">
    <div>
        <h1>Dashboard</h1>
        <h3>Financial overview for {{ $activeEntity->name ?? 'your entity' }}</h3>
    </div>
    <div style="display:flex;gap:10px;">
        <a href="{{ route('transactions.create') }}" class="btn btn-green">+ Journal Entry</a>
        <a href="{{ route('ledgers.create') }}" class="btn btn-outline">+ Add Ledger</a>
    </div>
</div>

<!-- KPI Cards -->
<div class="dashboard-grid">
    <div class="card metric-card">
        <h3>Total Cash Flow</h3>
        <div class="metric">₹24,500</div>
        <p style="color:var(--green-600);font-size:0.82rem;margin-top:4px;font-weight:500;">↑ 12.5% from last month</p>
    </div>
    <div class="card metric-card red">
        <h3>Pending GST Liability</h3>
        <div class="metric" style="color:#dc2626;">₹4,250</div>
        <p style="color:#dc2626;font-size:0.82rem;margin-top:4px;font-weight:500;">Due in 4 days</p>
    </div>
    <div class="card metric-card green">
        <h3>Outstanding Receivables</h3>
        <div class="metric" style="color:var(--green-600);">₹18,400</div>
        <p style="color:var(--text-muted);font-size:0.82rem;margin-top:4px;">3 invoices overdue</p>
    </div>
</div>

<!-- Statements + Actions -->
<div style="display:grid;grid-template-columns:2fr 1fr;gap:20px;">

    <div class="card">
        <h2>Recent Bank Statements</h2>
        @forelse($statements as $statement)
            <div style="padding:14px 0;border-bottom:1px solid var(--gray-100);display:flex;align-items:center;gap:14px;">
                <div style="width:38px;height:38px;border-radius:var(--radius-sm);background:var(--blue-50);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--blue-600)" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                </div>
                <div style="flex:1;min-width:0;">
                    <div style="font-weight:500;font-size:0.875rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $statement->original_filename }}</div>
                    <div style="font-size:0.78rem;color:var(--text-muted);margin-top:2px;">{{ $statement->created_at->format('d M Y') }}</div>
                </div>
                <span class="badge {{ $statement->status === 'completed' ? 'badge-green' : 'badge-yellow' }}">
                    {{ ucfirst($statement->status) }}
                </span>
            </div>
        @empty
            <div style="text-align:center;padding:32px 0;color:var(--text-muted);">
                <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin:0 auto 12px;display:block;opacity:.4"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                No statements uploaded yet.
            </div>
        @endforelse
    </div>

    <div style="display:flex;flex-direction:column;gap:20px;">

        <div class="card">
            <h2>Upload Statement</h2>
            <form action="{{ route('statement.upload') }}" method="POST" enctype="multipart/form-data" style="display:flex;flex-direction:column;gap:12px;">
                @csrf
                <label style="display:block;cursor:pointer;border:2px dashed var(--border);border-radius:var(--radius-sm);padding:20px;text-align:center;background:var(--gray-50);transition:border-color .15s;"
                    onmouseover="this.style.borderColor='var(--blue-400)'" onmouseout="this.style.borderColor='var(--border)'">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--blue-600)" stroke-width="2" style="margin:0 auto 8px;display:block;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                    <span style="font-size:0.85rem;color:var(--text-muted);">Click to select PDF</span>
                    <input type="file" name="statement" accept="application/pdf" style="display:none;" required>
                </label>
                <button type="submit" class="btn" style="width:100%;justify-content:center;">Upload & Parse</button>
            </form>
        </div>

        <div class="card">
            <h2>Quick Actions</h2>
            <div style="display:flex;flex-direction:column;gap:8px;">
                <a href="{{ route('transactions.create') }}" class="btn btn-green" style="justify-content:center;">+ New Journal Entry</a>
                <a href="{{ route('reports.trial-balance') }}" class="btn btn-outline" style="justify-content:center;">View Trial Balance</a>
                <a href="{{ route('reports.profit-loss') }}" class="btn btn-outline" style="justify-content:center;">View P&amp;L</a>
            </div>
        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInputs = document.querySelectorAll('input[type=file]');
    fileInputs.forEach(input => {
        input.addEventListener('change', function() {
            const label = this.closest('label');
            const span = label?.querySelector('span');
            if(span) span.textContent = this.files[0]?.name ?? 'Click to select PDF';
        });
    });
});
</script>
@endsection
