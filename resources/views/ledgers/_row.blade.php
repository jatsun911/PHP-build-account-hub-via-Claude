<tr style="border-bottom: 1px solid var(--glass-border);">
    <td style="padding: 12px 16px; color: var(--text-secondary);">{{ $index + 1 }}</td>
    <td style="padding: 12px 16px;">
        <input type="text" name="ledgers[{{ $index }}][name]" placeholder="e.g. HDFC Bank Current A/c" {{ $index === 0 ? 'required' : '' }} style="width: 100%; border: none; background: transparent; outline: none; font-size: 0.875rem;">
    </td>
    <td style="padding: 12px 16px;">
        <select name="ledgers[{{ $index }}][ledger_group_id]" style="width: 100%; border: 1px solid #e5e7eb; border-radius: 6px; padding: 6px 8px; background: white; outline: none; font-size: 0.875rem;">
            <option value="">Suspense (default)</option>
            @foreach($groups as $group)
                <option value="{{ $group->id }}">{{ $group->name }}</option>
            @endforeach
        </select>
    </td>
    <td style="padding: 12px 16px;">
        <input type="number" name="ledgers[{{ $index }}][dr_balance]" step="0.01" min="0" placeholder="0.00" oninput="toggleCr(this, {{ $index }})" style="width: 100%; text-align: right; border: none; background: transparent; outline: none; font-size: 0.875rem;">
    </td>
    <td style="padding: 12px 16px;">
        <input type="number" name="ledgers[{{ $index }}][cr_balance]" step="0.01" min="0" placeholder="0.00" oninput="toggleDr(this, {{ $index }})" style="width: 100%; text-align: right; border: none; background: transparent; outline: none; font-size: 0.875rem;">
    </td>
    <td style="padding: 12px 16px;">
        <input type="text" name="ledgers[{{ $index }}][notes]" placeholder="Optional notes" style="width: 100%; border: none; background: transparent; outline: none; font-size: 0.875rem;">
    </td>
    <td style="padding: 12px 16px; text-align: center;">
        <button type="button" onclick="this.closest('tr').remove()" style="background: none; border: none; color: #9ca3af; cursor: pointer; padding: 4px;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
        </button>
    </td>
</tr>
