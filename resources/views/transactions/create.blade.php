@extends('layouts.app')
@section('title', 'New Journal Entry')
@section('content')

<div class="dashboard-header">
    <div>
        <h1>New Journal Entry</h1>
        <h3>Double-entry bookkeeping — total debits must equal total credits.</h3>
    </div>
    <a href="{{ route('transactions.index') }}" class="btn btn-outline">← Back</a>
</div>

@if($errors->any())
<div class="alert alert-error">
    @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
</div>
@endif

<form method="POST" action="{{ route('transactions.store') }}" id="journalForm">
@csrf

<div class="card">

    <!-- Header fields -->
    <div style="display:grid;grid-template-columns:200px 1fr;gap:16px;margin-bottom:24px;padding-bottom:20px;border-bottom:1px solid var(--border);">
        <div>
            <label class="form-label">Entry Date *</label>
            <input type="date" name="transaction_date" value="{{ old('transaction_date', date('Y-m-d')) }}" class="form-control" required>
        </div>
        <div>
            <label class="form-label">Narration / Description *</label>
            <input type="text" name="description" value="{{ old('description') }}"
                placeholder="e.g. Rent payment for May 2026" class="form-control" required>
        </div>
    </div>

    <!-- Entry lines -->
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
        <h2 style="margin:0;">Ledger Lines</h2>
        <div style="display:flex;align-items:center;gap:12px;">
            <span id="balance-indicator" style="font-size:0.82rem;font-weight:600;padding:4px 12px;border-radius:20px;background:var(--green-50);color:var(--green-700);">
                Balanced ✓
            </span>
            <button type="button" onclick="addRow()" class="btn btn-outline" style="padding:7px 14px;">+ Add Line</button>
        </div>
    </div>

    <table class="data-table" style="margin-bottom:0;">
        <thead>
            <tr>
                <th>Ledger Account</th>
                <th style="width:170px;text-align:right;">Amount (₹)</th>
                <th style="width:130px;text-align:center;">Dr / Cr</th>
                <th style="width:44px;"></th>
            </tr>
        </thead>
        <tbody id="entriesBody">
            <tr class="entry-row">
                <td style="padding:8px 14px;">
                    <select name="entries[0][ledger_id]" class="form-control" required>
                        <option value="">— Select ledger —</option>
                        @foreach($ledgers as $l)
                        <option value="{{ $l->id }}">{{ $l->name }} ({{ ucfirst($l->type) }})</option>
                        @endforeach
                    </select>
                </td>
                <td style="padding:8px 14px;">
                    <input type="number" name="entries[0][amount]" placeholder="0.00" step="0.01" min="0.01"
                        class="form-control" style="text-align:right;" oninput="updateBalance()" required>
                </td>
                <td style="padding:8px 14px;text-align:center;">
                    <select name="entries[0][type]" class="form-control" onchange="updateBalance()">
                        <option value="debit">Debit</option>
                        <option value="credit">Credit</option>
                    </select>
                </td>
                <td></td>
            </tr>
            <tr class="entry-row">
                <td style="padding:8px 14px;">
                    <select name="entries[1][ledger_id]" class="form-control" required>
                        <option value="">— Select ledger —</option>
                        @foreach($ledgers as $l)
                        <option value="{{ $l->id }}">{{ $l->name }} ({{ ucfirst($l->type) }})</option>
                        @endforeach
                    </select>
                </td>
                <td style="padding:8px 14px;">
                    <input type="number" name="entries[1][amount]" placeholder="0.00" step="0.01" min="0.01"
                        class="form-control" style="text-align:right;" oninput="updateBalance()" required>
                </td>
                <td style="padding:8px 14px;text-align:center;">
                    <select name="entries[1][type]" class="form-control" onchange="updateBalance()">
                        <option value="debit">Debit</option>
                        <option value="credit" selected>Credit</option>
                    </select>
                </td>
                <td style="padding:8px 14px;text-align:center;">
                    <button type="button" onclick="removeRow(this)" style="background:none;border:none;cursor:pointer;color:#dc2626;font-size:1.1rem;line-height:1;">✕</button>
                </td>
            </tr>
        </tbody>
        <tfoot>
            <tr style="background:var(--gray-50);border-top:2px solid var(--border);">
                <td style="padding:12px 14px;font-weight:600;color:var(--text-muted);">Totals</td>
                <td style="padding:12px 14px;text-align:right;font-weight:700;" id="totalDisplay">₹0.00 Dr / ₹0.00 Cr</td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
    </table>

    <div style="display:flex;justify-content:flex-end;gap:10px;margin-top:20px;padding-top:20px;border-top:1px solid var(--border);">
        <a href="{{ route('transactions.index') }}" class="btn btn-outline">Cancel</a>
        <button type="submit" class="btn">Post Journal Entry</button>
    </div>
</div>
</form>

<script>
let rowCount = 2;
const opts = `@foreach($ledgers as $l)<option value="{{ $l->id }}">{{ $l->name }} ({{ ucfirst($l->type) }})</option>@endforeach`;

function addRow() {
    const i = rowCount++;
    const tr = document.createElement('tr');
    tr.className = 'entry-row';
    tr.innerHTML = `
        <td style="padding:8px 14px;">
            <select name="entries[${i}][ledger_id]" class="form-control" required>
                <option value="">— Select ledger —</option>${opts}
            </select>
        </td>
        <td style="padding:8px 14px;">
            <input type="number" name="entries[${i}][amount]" placeholder="0.00" step="0.01" min="0.01"
                class="form-control" style="text-align:right;" oninput="updateBalance()" required>
        </td>
        <td style="padding:8px 14px;text-align:center;">
            <select name="entries[${i}][type]" class="form-control" onchange="updateBalance()">
                <option value="debit">Debit</option><option value="credit">Credit</option>
            </select>
        </td>
        <td style="padding:8px 14px;text-align:center;">
            <button type="button" onclick="removeRow(this)" style="background:none;border:none;cursor:pointer;color:#dc2626;font-size:1.1rem;line-height:1;">✕</button>
        </td>`;
    document.getElementById('entriesBody').appendChild(tr);
    updateBalance();
}

function removeRow(btn) {
    const rows = document.querySelectorAll('.entry-row');
    if (rows.length <= 2) return;
    btn.closest('tr').remove();
    updateBalance();
}

function updateBalance() {
    let dr = 0, cr = 0;
    document.querySelectorAll('.entry-row').forEach(row => {
        const amt = parseFloat(row.querySelector('input[type=number]')?.value) || 0;
        const type = row.querySelector('select[name*="[type]"]')?.value;
        if (type === 'debit') dr += amt; else cr += amt;
    });
    const ind = document.getElementById('balance-indicator');
    document.getElementById('totalDisplay').textContent = `₹${dr.toFixed(2)} Dr / ₹${cr.toFixed(2)} Cr`;
    const ok = Math.abs(dr - cr) < 0.01 && dr > 0;
    ind.textContent = ok ? 'Balanced ✓' : `Off by ₹${Math.abs(dr - cr).toFixed(2)}`;
    ind.style.background = ok ? 'var(--green-50)' : '#fef2f2';
    ind.style.color = ok ? 'var(--green-700)' : '#b91c1c';
}
updateBalance();
</script>
@endsection
