@extends('layouts.app')
@section('title', 'New Journal Entry')
@section('content')
<div class="dashboard-header">
    <div>
        <h1>New Journal Entry</h1>
        <h3>Double-entry: total debits must equal total credits.</h3>
    </div>
    <a href="{{ route('transactions.index') }}" class="glass-pill" style="text-decoration:none;color:var(--text-primary);">← All Transactions</a>
</div>

@if($errors->any())
<div style="background:#fee2e2;border:1px solid #fca5a5;color:#b91c1c;padding:12px 20px;border-radius:8px;margin-top:16px;">
    @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
</div>
@endif

<form method="POST" action="{{ route('transactions.store') }}" id="journalForm">
    @csrf
    <div class="glass-panel" style="margin-top:24px;display:flex;flex-direction:column;gap:20px;">

        <div style="display:grid;grid-template-columns:1fr 2fr;gap:16px;">
            <div>
                <label style="display:block;font-weight:500;margin-bottom:6px;color:var(--text-secondary);">Date *</label>
                <input type="date" name="transaction_date" value="{{ old('transaction_date', date('Y-m-d')) }}"
                    style="width:100%;padding:10px 14px;border:1px solid var(--glass-border);border-radius:8px;background:rgba(255,255,255,0.6);font-size:1rem;" required>
            </div>
            <div>
                <label style="display:block;font-weight:500;margin-bottom:6px;color:var(--text-secondary);">Description / Narration *</label>
                <input type="text" name="description" value="{{ old('description') }}" placeholder="e.g. Rent payment for May 2026"
                    style="width:100%;padding:10px 14px;border:1px solid var(--glass-border);border-radius:8px;background:rgba(255,255,255,0.6);font-size:1rem;" required>
            </div>
        </div>

        <div>
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
                <h2 style="margin:0;">Ledger Entries</h2>
                <div style="display:flex;gap:12px;align-items:center;">
                    <span id="balance-indicator" style="font-weight:600;font-size:0.9rem;color:#059669;">Balanced ✓</span>
                    <button type="button" onclick="addRow()" class="glass-pill" style="cursor:pointer;border:none;background:var(--brand-primary);color:white;padding:8px 18px;">+ Add Line</button>
                </div>
            </div>

            <table style="width:100%;border-collapse:collapse;" id="entriesTable">
                <thead>
                    <tr style="border-bottom:1px solid var(--glass-border);">
                        <th style="padding:10px 8px;color:var(--text-secondary);font-weight:500;text-align:left;">Ledger Account</th>
                        <th style="padding:10px 8px;color:var(--text-secondary);font-weight:500;text-align:right;width:160px;">Amount (₹)</th>
                        <th style="padding:10px 8px;color:var(--text-secondary);font-weight:500;text-align:center;width:120px;">Type</th>
                        <th style="width:40px;"></th>
                    </tr>
                </thead>
                <tbody id="entriesBody">
                    <tr class="entry-row" data-index="0">
                        <td style="padding:8px;">
                            <select name="entries[0][ledger_id]" style="width:100%;padding:10px 12px;border:1px solid var(--glass-border);border-radius:8px;background:rgba(255,255,255,0.6);" required>
                                <option value="">— Select ledger —</option>
                                @foreach($ledgers as $ledger)
                                <option value="{{ $ledger->id }}">{{ $ledger->name }} ({{ ucfirst($ledger->type) }})</option>
                                @endforeach
                            </select>
                        </td>
                        <td style="padding:8px;">
                            <input type="number" name="entries[0][amount]" placeholder="0.00" step="0.01" min="0.01"
                                style="width:100%;padding:10px 12px;border:1px solid var(--glass-border);border-radius:8px;background:rgba(255,255,255,0.6);text-align:right;"
                                oninput="updateBalance()" required>
                        </td>
                        <td style="padding:8px;text-align:center;">
                            <select name="entries[0][type]" style="padding:10px 12px;border:1px solid var(--glass-border);border-radius:8px;background:rgba(255,255,255,0.6);" onchange="updateBalance()">
                                <option value="debit">Debit</option>
                                <option value="credit">Credit</option>
                            </select>
                        </td>
                        <td></td>
                    </tr>
                    <tr class="entry-row" data-index="1">
                        <td style="padding:8px;">
                            <select name="entries[1][ledger_id]" style="width:100%;padding:10px 12px;border:1px solid var(--glass-border);border-radius:8px;background:rgba(255,255,255,0.6);" required>
                                <option value="">— Select ledger —</option>
                                @foreach($ledgers as $ledger)
                                <option value="{{ $ledger->id }}">{{ $ledger->name }} ({{ ucfirst($ledger->type) }})</option>
                                @endforeach
                            </select>
                        </td>
                        <td style="padding:8px;">
                            <input type="number" name="entries[1][amount]" placeholder="0.00" step="0.01" min="0.01"
                                style="width:100%;padding:10px 12px;border:1px solid var(--glass-border);border-radius:8px;background:rgba(255,255,255,0.6);text-align:right;"
                                oninput="updateBalance()" required>
                        </td>
                        <td style="padding:8px;text-align:center;">
                            <select name="entries[1][type]" style="padding:10px 12px;border:1px solid var(--glass-border);border-radius:8px;background:rgba(255,255,255,0.6);" onchange="updateBalance()">
                                <option value="debit">Debit</option>
                                <option value="credit" selected>Credit</option>
                            </select>
                        </td>
                        <td style="padding:8px;text-align:center;">
                            <button type="button" onclick="removeRow(this)" style="background:none;border:none;cursor:pointer;color:#ef4444;font-size:1.1rem;">✕</button>
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr style="border-top:2px solid var(--glass-border);">
                        <td style="padding:12px 8px;font-weight:600;color:var(--text-secondary);">Totals</td>
                        <td style="padding:12px 8px;text-align:right;font-weight:700;" id="totalDisplay">₹0.00 Dr / ₹0.00 Cr</td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div style="display:flex;gap:12px;justify-content:flex-end;">
            <a href="{{ route('transactions.index') }}" class="glass-pill" style="text-decoration:none;color:var(--text-secondary);padding:12px 24px;">Cancel</a>
            <button type="submit" style="background:var(--brand-primary);color:white;border:none;border-radius:12px;padding:12px 32px;font-size:1rem;font-weight:600;cursor:pointer;">
                Post Journal Entry
            </button>
        </div>
    </div>
</form>

<script>
let rowCount = 2;
const ledgerOptions = `@foreach($ledgers as $ledger)<option value="{{ $ledger->id }}">{{ $ledger->name }} ({{ ucfirst($ledger->type) }})</option>@endforeach`;

function addRow() {
    const tbody = document.getElementById('entriesBody');
    const i = rowCount++;
    const tr = document.createElement('tr');
    tr.className = 'entry-row';
    tr.dataset.index = i;
    tr.innerHTML = `
        <td style="padding:8px;">
            <select name="entries[${i}][ledger_id]" style="width:100%;padding:10px 12px;border:1px solid var(--glass-border);border-radius:8px;background:rgba(255,255,255,0.6);" required>
                <option value="">— Select ledger —</option>${ledgerOptions}
            </select>
        </td>
        <td style="padding:8px;">
            <input type="number" name="entries[${i}][amount]" placeholder="0.00" step="0.01" min="0.01"
                style="width:100%;padding:10px 12px;border:1px solid var(--glass-border);border-radius:8px;background:rgba(255,255,255,0.6);text-align:right;"
                oninput="updateBalance()" required>
        </td>
        <td style="padding:8px;text-align:center;">
            <select name="entries[${i}][type]" style="padding:10px 12px;border:1px solid var(--glass-border);border-radius:8px;background:rgba(255,255,255,0.6);" onchange="updateBalance()">
                <option value="debit">Debit</option>
                <option value="credit">Credit</option>
            </select>
        </td>
        <td style="padding:8px;text-align:center;">
            <button type="button" onclick="removeRow(this)" style="background:none;border:none;cursor:pointer;color:#ef4444;font-size:1.1rem;">✕</button>
        </td>`;
    tbody.appendChild(tr);
    updateBalance();
}

function removeRow(btn) {
    const rows = document.querySelectorAll('.entry-row');
    if (rows.length <= 2) return;
    btn.closest('tr').remove();
    updateBalance();
}

function updateBalance() {
    let totalDr = 0, totalCr = 0;
    document.querySelectorAll('.entry-row').forEach(row => {
        const amt = parseFloat(row.querySelector('input[type=number]')?.value) || 0;
        const type = row.querySelector('select[name*="[type]"]')?.value;
        if (type === 'debit') totalDr += amt;
        else totalCr += amt;
    });
    const indicator = document.getElementById('balance-indicator');
    const display = document.getElementById('totalDisplay');
    display.textContent = `₹${totalDr.toFixed(2)} Dr / ₹${totalCr.toFixed(2)} Cr`;
    const balanced = Math.abs(totalDr - totalCr) < 0.01 && totalDr > 0;
    indicator.textContent = balanced ? 'Balanced ✓' : `Off by ₹${Math.abs(totalDr - totalCr).toFixed(2)}`;
    indicator.style.color = balanced ? '#059669' : '#ef4444';
}

updateBalance();
</script>
@endsection
