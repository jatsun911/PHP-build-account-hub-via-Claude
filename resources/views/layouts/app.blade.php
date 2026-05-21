<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AccountHub v2 - @yield('title', 'Dashboard')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="app-container">
        <!-- Sidebar Navigation -->
        <nav class="sidebar">
            <div class="logo-area">
                <div class="logo-icon"></div>
                AccountHub
            </div>
            
            <ul class="nav-links">
                <li><a href="#" class="nav-item active">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                    Dashboard
                </a></li>
                <!-- Section: Accounts -->
                <li style="margin-top: 24px; margin-bottom: 12px;">
                    <div style="font-size: 0.75rem; font-weight: 700; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.05em; display: flex; justify-content: space-between; align-items: center; padding: 0 12px;">
                        ACCOUNTS
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </div>
                </li>
                <li><a href="{{ route('ledgers.index') }}" class="nav-item">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                    Chart of Accounts
                </a></li>
                <li><a href="{{ route('ledgers.create') }}" class="nav-item">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path></svg>
                    Create New Ledger
                </a></li>
                <li><a href="#" class="nav-item">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                    Rename Ledgers
                </a></li>
                <li><a href="#" class="nav-item">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                    Remap Ledger Groups
                </a></li>
                <li><a href="{{ route('transactions.index') }}" class="nav-item">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                    Transactions
                </a></li>
                <li><a href="{{ route('transactions.create') }}" class="nav-item">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    New Journal Entry
                </a></li>

                <li style="margin-top: 24px; margin-bottom: 12px;">
                    <div style="font-size: 0.75rem; font-weight: 700; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.05em; padding: 0 12px;">
                        REPORTS
                    </div>
                </li>
                <li><a href="{{ route('reports.trial-balance') }}" class="nav-item">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                    Trial Balance
                </a></li>
                <li><a href="{{ route('reports.profit-loss') }}" class="nav-item">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline></svg>
                    Profit & Loss
                </a></li>
                <li><a href="{{ route('reports.balance-sheet') }}" class="nav-item">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                    Balance Sheet
                </a></li>
            </ul>

            <div style="margin-top: auto; padding: 24px; border-top: 1px solid var(--glass-border); display: flex; flex-direction: column; gap: 10px;">
                <div style="background: #ffe4e6; color: #be123c; padding: 10px 12px; text-align: center; border-radius: 8px; font-weight: 600; font-size: 0.85rem;">
                    {{ Auth::user()->name ?? 'System Owner' }}
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" style="width:100%;background:rgba(0,0,0,0.05);border:1px solid var(--glass-border);border-radius:8px;padding:8px;font-size:0.85rem;color:var(--text-secondary);cursor:pointer;">
                        Sign Out
                    </button>
                </form>
            </div>
        </nav>

        <!-- Main Workspace -->
        <main class="main-content">
            <header class="topbar" style="display: flex; justify-content: space-between; align-items: center; padding: 16px 32px;">
                <div style="background: white; padding: 10px 24px; border-radius: 8px; font-weight: 600; font-size: 1.1rem; box-shadow: 0 4px 6px rgba(0,0,0,0.02); color: var(--brand-primary); min-width: 200px; text-align: center;">
                    @php
                        $activeEntity = \App\Models\Entity::find(session('active_entity_id'));
                    @endphp
                    {{ $activeEntity ? $activeEntity->name : 'No Active Entity' }}
                </div>
                
                <input type="text" class="search-bar" placeholder="Search transactions, clients, or GST..." style="width: 300px; padding: 10px 16px; border-radius: 20px; border: 1px solid var(--border-color); background: #f8fafc;">
            </header>

            @yield('content')
            
        </main>
    </div>
</body>
</html>
