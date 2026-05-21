@extends('layouts.app')

@section('title', 'Ledgers')

@section('content')
    <div class="dashboard-header" style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px;">
        <div>
            <h1>Chart of Accounts</h1>
            <h3 style="color: var(--text-secondary); margin-top: 4px;">Manage all ledgers and groups.</h3>
        </div>
        <div style="display: flex; gap: 12px;">
            <a href="{{ route('ledgers.create') }}" class="btn" style="background: white; color: var(--text-primary); border: 1px solid var(--glass-border); text-decoration: none;">
                Create Ledger
            </a>
            <button onclick="document.getElementById('groupModal').style.display='flex'" class="btn">
                Create Ledger Group
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

    <div class="glass-panel" style="padding: 0; overflow: hidden;">
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead style="background: rgba(0,0,0,0.02);">
                <tr style="border-bottom: 1px solid var(--glass-border); font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-secondary);">
                    <th style="padding: 16px;">Ledger Name</th>
                    <th style="padding: 16px;">Code</th>
                    <th style="padding: 16px;">Group</th>
                    <th style="padding: 16px;">Type</th>
                    <th style="padding: 16px;">Description</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ledgers as $ledger)
                    <tr style="border-bottom: 1px solid var(--glass-border); transition: background 0.2s;" onmouseover="this.style.background='rgba(0,0,0,0.02)'" onmouseout="this.style.background='transparent'">
                        <td style="padding: 16px; font-weight: 500; display: flex; align-items: center; gap: 8px;">
                            @if(in_array($ledger->code, ['OB_DIFF_SYS', 'SUSPENSE_SYS']))
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                            @endif
                            {{ $ledger->name }}
                        </td>
                        <td style="padding: 16px; color: var(--text-secondary); font-family: monospace;">{{ $ledger->code }}</td>
                        <td style="padding: 16px;">
                            <span class="glass-pill" style="font-size: 0.75rem;">{{ $ledger->ledgerGroup->name ?? 'None' }}</span>
                        </td>
                        <td style="padding: 16px; text-transform: capitalize;">{{ $ledger->type }}</td>
                        <td style="padding: 16px; color: var(--text-secondary); font-size: 0.875rem;">{{ Str::limit($ledger->description, 50) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        
        <div style="padding: 16px; border-top: 1px solid var(--glass-border);">
            {{ $ledgers->links() }}
        </div>
    </div>

    <!-- Create Group Modal -->
    <div id="groupModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 100; align-items: center; justify-content: center; backdrop-filter: blur(4px);">
        <div class="glass-panel" style="width: 400px; padding: 24px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                <h3 style="margin: 0;">Create Ledger Group</h3>
                <button onclick="document.getElementById('groupModal').style.display='none'" style="background: none; border: none; cursor: pointer; color: var(--text-secondary);">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>
            
            <form action="{{ route('ledger_groups.store') }}" method="POST">
                @csrf
                <div style="margin-bottom: 16px;">
                    <label style="display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 8px;">Group Name</label>
                    <input type="text" name="name" required style="width: 100%; border: 1px solid #e5e7eb; border-radius: 6px; padding: 10px; outline: none;">
                </div>
                
                <div style="margin-bottom: 24px;">
                    <label style="display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 8px;">Account Type</label>
                    <select name="type" required style="width: 100%; border: 1px solid #e5e7eb; border-radius: 6px; padding: 10px; outline: none; background: white;">
                        <option value="asset">Asset</option>
                        <option value="liability">Liability</option>
                        <option value="equity">Equity</option>
                        <option value="revenue">Revenue</option>
                        <option value="expense">Expense</option>
                    </select>
                </div>
                
                <button type="submit" class="btn" style="width: 100%; justify-content: center;">Create Group</button>
            </form>
        </div>
    </div>
@endsection
