@extends('layouts.app')
@section('title', 'Chart of Accounts')
@section('content')

<div class="dashboard-header">
    <div>
        <h1>Chart of Accounts</h1>
        <h3>All ledgers for the active entity.</h3>
    </div>
    <div style="display:flex;gap:10px;">
        <a href="{{ route('ledgers.create') }}" class="btn">+ New Ledger</a>
        <button onclick="document.getElementById('groupModal').style.display='flex'" class="btn btn-outline">+ Ledger Group</button>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if($errors->any())
    <div class="alert alert-error">
        @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
    </div>
@endif

<div class="card" style="padding:0;overflow:hidden;">
    <table class="data-table">
        <thead>
            <tr>
                <th>Ledger Name</th>
                <th>Code</th>
                <th>Group</th>
                <th>Type</th>
                <th>Description</th>
            </tr>
        </thead>
        <tbody>
            @forelse($ledgers as $ledger)
            <tr>
                <td style="font-weight:500;display:flex;align-items:center;gap:8px;">
                    @if(in_array($ledger->code, ['OB_DIFF_SYS', 'SUSPENSE_SYS']))
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    @endif
                    {{ $ledger->name }}
                </td>
                <td class="font-mono" style="color:var(--text-muted);font-size:0.8rem;">{{ $ledger->code }}</td>
                <td><span class="badge">{{ $ledger->ledgerGroup->name ?? '—' }}</span></td>
                <td>
                    @php $typeColors = ['asset'=>'','liability'=>'badge-yellow','equity'=>'badge-gray','revenue'=>'badge-green','expense'=>'badge-red']; @endphp
                    <span class="badge {{ $typeColors[$ledger->type] ?? '' }}">{{ ucfirst($ledger->type) }}</span>
                </td>
                <td style="color:var(--text-muted);font-size:0.82rem;">{{ Str::limit($ledger->description, 55) ?? '—' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align:center;padding:40px;color:var(--text-muted);">
                    No ledgers yet. <a href="{{ route('ledgers.create') }}" style="color:var(--blue-600);">Create your first ledger →</a>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div style="padding:14px 20px;border-top:1px solid var(--border);">
        {{ $ledgers->links() }}
    </div>
</div>

<!-- Create Group Modal -->
<div id="groupModal" style="display:none;position:fixed;inset:0;background:rgba(15,23,42,.45);z-index:200;align-items:center;justify-content:center;backdrop-filter:blur(3px);">
    <div class="card" style="width:420px;padding:28px;box-shadow:var(--shadow-lg);">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
            <h2 style="margin:0;">New Ledger Group</h2>
            <button onclick="document.getElementById('groupModal').style.display='none'" style="background:none;border:none;cursor:pointer;color:var(--text-muted);">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <form action="{{ route('ledger_groups.store') }}" method="POST" style="display:flex;flex-direction:column;gap:14px;">
            @csrf
            <div>
                <label class="form-label">Group Name</label>
                <input type="text" name="name" class="form-control" placeholder="e.g. Fixed Assets" required>
            </div>
            <div>
                <label class="form-label">Account Type</label>
                <select name="type" class="form-control" required>
                    <option value="asset">Asset</option>
                    <option value="liability">Liability</option>
                    <option value="equity">Equity</option>
                    <option value="revenue">Revenue</option>
                    <option value="expense">Expense</option>
                </select>
            </div>
            <button type="submit" class="btn" style="margin-top:4px;">Create Group</button>
        </form>
    </div>
</div>
@endsection
