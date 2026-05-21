@extends('layouts.app')

@section('title', 'Create Ledgers')

@section('content')
    <div class="dashboard-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <div>
            <h1>Master Creation</h1>
            <h3 style="color: var(--text-secondary); margin-top: 4px;">Create multiple ledgers at once. Opening balances are posted as double-entry journals automatically.</h3>
        </div>
        <div style="display: flex; gap: 12px;">
            <button type="button" class="btn" style="background: white; color: var(--text-primary); border: 1px solid var(--glass-border);" onclick="document.getElementById('ledgerForm').reset();">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21.5 2v6h-6M2.13 15.57a10 10 0 1 0 3.86-9.69L2 8"></path></svg>
                Clear All
            </button>
            <button type="submit" form="ledgerForm" class="btn">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
                Save
            </button>
        </div>
    </div>

    @if(session('success'))
        <div style="padding: 16px; background: #d1fae5; color: #065f46; border-radius: 8px; margin-bottom: 24px; border: 1px solid #a7f3d0;">
            {{ session('success') }}
        </div>
    @endif
    
    @if($errors->any())
        <div style="padding: 16px; background: #fee2e2; color: #991b1b; border-radius: 8px; margin-bottom: 24px; border: 1px solid #fecaca;">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div style="background: rgba(59, 130, 246, 0.05); border: 1px solid rgba(59, 130, 246, 0.2); border-radius: 12px; padding: 16px; margin-bottom: 24px; display: flex; gap: 12px; color: #1d4ed8; font-size: 0.875rem;">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink: 0;"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
        <div>
            If no Ledger Group is selected, the ledger will be placed under <strong>Suspense (default)</strong> by default. Every non-zero opening balance creates a double-entry journal — the counter-entry is posted to <strong>Opening Balance Differences A/c(System)</strong> (Equity group) to keep the books balanced.
        </div>
    </div>

    <form id="ledgerForm" action="{{ route('ledgers.store') }}" method="POST">
        @csrf
        <div class="glass-panel" style="padding: 0; overflow: hidden;">
            <table style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead style="background: rgba(0,0,0,0.02);">
                    <tr style="border-bottom: 1px solid var(--glass-border); font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-secondary);">
                        <th style="padding: 16px;">#</th>
                        <th style="padding: 16px;">Ledger Name *</th>
                        <th style="padding: 16px;">Ledger Group</th>
                        <th style="padding: 16px;">Dr Balance</th>
                        <th style="padding: 16px;">Cr Balance</th>
                        <th style="padding: 16px;">Notes</th>
                        <th style="padding: 16px;"></th>
                    </tr>
                </thead>
                <tbody id="ledgerTableBody">
                    <!-- Default 3 rows -->
                    @for($i = 0; $i < 3; $i++)
                        @include('ledgers._row', ['index' => $i])
                    @endfor
                </tbody>
            </table>
            
            <div style="padding: 16px; border-top: 1px solid var(--glass-border);">
                <button type="button" id="addRowBtn" style="background: none; border: none; color: var(--brand-primary); font-weight: 500; display: flex; align-items: center; gap: 8px; cursor: pointer;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    Add Row
                </button>
            </div>
        </div>
    </form>

    <script>
        let rowCount = 3;
        
        document.getElementById('addRowBtn').addEventListener('click', function() {
            const tbody = document.getElementById('ledgerTableBody');
            const newRow = document.createElement('tr');
            newRow.style.borderBottom = '1px solid var(--glass-border)';
            newRow.innerHTML = `
                <td style="padding: 12px 16px; color: var(--text-secondary);">${rowCount + 1}</td>
                <td style="padding: 12px 16px;">
                    <input type="text" name="ledgers[${rowCount}][name]" placeholder="e.g. HDFC Bank Current A/c" required style="width: 100%; border: none; background: transparent; outline: none; font-size: 0.875rem;">
                </td>
                <td style="padding: 12px 16px;">
                    <select name="ledgers[${rowCount}][ledger_group_id]" style="width: 100%; border: 1px solid #e5e7eb; border-radius: 6px; padding: 6px 8px; background: white; outline: none; font-size: 0.875rem;">
                        <option value="">Suspense (default)</option>
                        @foreach($groups as $group)
                            <option value="{{ $group->id }}">{{ $group->name }}</option>
                        @endforeach
                    </select>
                </td>
                <td style="padding: 12px 16px;">
                    <input type="number" name="ledgers[${rowCount}][dr_balance]" step="0.01" min="0" placeholder="0.00" oninput="toggleCr(this, ${rowCount})" style="width: 100%; text-align: right; border: none; background: transparent; outline: none; font-size: 0.875rem;">
                </td>
                <td style="padding: 12px 16px;">
                    <input type="number" name="ledgers[${rowCount}][cr_balance]" step="0.01" min="0" placeholder="0.00" oninput="toggleDr(this, ${rowCount})" style="width: 100%; text-align: right; border: none; background: transparent; outline: none; font-size: 0.875rem;">
                </td>
                <td style="padding: 12px 16px;">
                    <input type="text" name="ledgers[${rowCount}][notes]" placeholder="Optional notes" style="width: 100%; border: none; background: transparent; outline: none; font-size: 0.875rem;">
                </td>
                <td style="padding: 12px 16px; text-align: center;">
                    <button type="button" onclick="this.closest('tr').remove()" style="background: none; border: none; color: #9ca3af; cursor: pointer; padding: 4px;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                    </button>
                </td>
            `;
            tbody.appendChild(newRow);
            rowCount++;
        });

        function toggleCr(drInput, index) {
            const crInput = document.querySelector(`input[name="ledgers[${index}][cr_balance]"]`);
            if (drInput.value > 0) {
                crInput.value = '';
                crInput.disabled = true;
            } else {
                crInput.disabled = false;
            }
        }

        function toggleDr(crInput, index) {
            const drInput = document.querySelector(`input[name="ledgers[${index}][dr_balance]"]`);
            if (crInput.value > 0) {
                drInput.value = '';
                drInput.disabled = true;
            } else {
                drInput.disabled = false;
            }
        }
    </script>
@endsection
