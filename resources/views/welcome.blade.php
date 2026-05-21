@extends('layouts.app')

@section('title', 'Financial Dashboard')

@section('content')
    <div class="dashboard-header">
        <h1>Overview</h1>
        <h3>Welcome back! Here's what's happening with your accounts today.</h3>
    </div>

    @if(session('success'))
        <div style="background: hsla(150, 60%, 40%, 0.1); border-left: 4px solid #34d399; padding: 16px; border-radius: 0 8px 8px 0; margin-bottom: 24px;">
            <div style="font-weight: 600; color: #059669;">{{ session('success') }}</div>
        </div>
    @endif

    <!-- Metrics Grid -->
    <div class="dashboard-grid">
        <div class="glass-panel">
            <h3>Total Cash Flow</h3>
            <div class="metric">₹24,500.00</div>
            <p style="color: var(--brand-primary); margin-top: 8px;">+12.5% from last month</p>
        </div>
        
        <div class="glass-panel">
            <h3>Pending GST Liability</h3>
            <div class="metric" style="color: #ef4444;">₹4,250.00</div>
            <p style="color: #ef4444; margin-top: 8px;">Due in 4 days</p>
        </div>
        
        <div class="glass-panel">
            <h3>Outstanding Receivables</h3>
            <div class="metric">₹18,400.00</div>
            <p style="color: var(--text-secondary); margin-top: 8px;">3 invoices overdue</p>
        </div>
    </div>

    <!-- Recent Transactions & Action Items -->
    <div class="dashboard-grid" style="grid-template-columns: 2fr 1fr; margin-top: 16px;">
        <div class="glass-panel">
            <h2>Recent Transactions & Parsed Statements</h2>
            
            <div style="display: flex; flex-direction: column; gap: 16px; margin-top: 24px;">
                
                @if(isset($statements) && $statements->count() > 0)
                    @foreach($statements as $statement)
                        <div style="padding-bottom: 16px; border-bottom: 1px solid var(--glass-border);">
                            <div style="font-weight: 600;">Statement: {{ $statement->original_filename }} (Status: {{ $statement->status }})</div>
                            
                            @if($statement->status === 'completed' && isset($statement->extracted_data))
                                <div style="margin-top: 12px; display: flex; flex-direction: column; gap: 8px;">
                                @foreach($statement->extracted_data as $snippet)
                                    <div class="transaction-row" style="display: flex; justify-content: space-between; align-items: center; padding: 8px; border-radius: 8px; background: rgba(0,0,0,0.02); position: relative; cursor: pointer;">
                                        
                                        <div style="display: flex; align-items: center; gap: 12px;">
                                            <div style="width: 32px; height: 32px; border-radius: 50%; background: hsla(250, 60%, 40%, 0.1); display: flex; align-items: center; justify-content: center; color: var(--brand-primary);">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                                            </div>
                                            <div>
                                                <div style="font-weight: 500;">Extracted Transaction {{ $snippet['transaction_id'] }}</div>
                                                <div style="font-size: 0.8rem; color: var(--text-secondary);">Hover to view original PDF snippet</div>
                                            </div>
                                        </div>

                                        <!-- The Hover Snippet Container -->
                                        <div class="hover-snippet" style="display: none; position: absolute; left: 0; top: 100%; z-index: 50; margin-top: 8px; background: white; padding: 8px; border-radius: 8px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); border: 1px solid var(--glass-border);">
                                            <img src="{{ $snippet['snippet_url'] }}" alt="PDF Snippet" style="max-width: 600px; height: auto; border: 1px solid #eee;">
                                        </div>
                                    </div>
                                @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                @else
                    <p style="color: var(--text-secondary);">No transactions available.</p>
                @endif

            </div>
        </div>

        <div class="glass-panel">
            <h2>Actions</h2>
            
            <div style="margin-top: 24px; display: flex; flex-direction: column; gap: 16px;">
                <div style="background: hsla(250, 80%, 60%, 0.05); border-left: 4px solid var(--brand-primary); padding: 16px; border-radius: 0 8px 8px 0;">
                    <div style="font-weight: 600; margin-bottom: 8px;">Upload Bank Statement</div>
                    <form action="{{ route('statement.upload') }}" method="POST" enctype="multipart/form-data" style="display: flex; flex-direction: column; gap: 12px;">
                        @csrf
                        <input type="file" name="statement" accept="application/pdf" style="font-size: 0.9rem;" required>
                        <button type="submit" style="background: var(--brand-primary); color: white; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-weight: 500;">Upload & Parse</button>
                    </form>
                </div>
            </div>
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
