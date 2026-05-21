<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AccountHub — @yield('title', 'Dashboard')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<div class="app-container">

    <!-- ── Sidebar ─────────────────────────────────────────────────────── -->
    <nav class="sidebar">
        <div class="logo-area">
            <div class="logo-icon"></div>
            AccountHub
        </div>

        <p class="nav-section-label">Main</p>
        <ul class="nav-links">
            <li>
                <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                    Dashboard
                </a>
            </li>
        </ul>

        <p class="nav-section-label">Accounts</p>
        <ul class="nav-links">
            <li>
                <a href="{{ route('ledgers.index') }}" class="nav-item {{ request()->routeIs('ledgers.index') ? 'active' : '' }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
                    Chart of Accounts
                </a>
            </li>
            <li>
                <a href="{{ route('ledgers.create') }}" class="nav-item {{ request()->routeIs('ledgers.create') ? 'active' : '' }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Add Ledger
                </a>
            </li>
        </ul>

        <p class="nav-section-label">Transactions</p>
        <ul class="nav-links">
            <li>
                <a href="{{ route('transactions.index') }}" class="nav-item {{ request()->routeIs('transactions.index') ? 'active' : '' }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                    All Transactions
                </a>
            </li>
            <li>
                <a href="{{ route('transactions.create') }}" class="nav-item {{ request()->routeIs('transactions.create') ? 'active' : '' }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    Journal Entry
                </a>
            </li>
        </ul>

        <p class="nav-section-label">Reports</p>
        <ul class="nav-links">
            <li>
                <a href="{{ route('reports.trial-balance') }}" class="nav-item {{ request()->routeIs('reports.trial-balance') ? 'active' : '' }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                    Trial Balance
                </a>
            </li>
            <li>
                <a href="{{ route('reports.profit-loss') }}" class="nav-item {{ request()->routeIs('reports.profit-loss') ? 'active' : '' }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                    Profit & Loss
                </a>
            </li>
            <li>
                <a href="{{ route('reports.balance-sheet') }}" class="nav-item {{ request()->routeIs('reports.balance-sheet') ? 'active' : '' }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                    Balance Sheet
                </a>
            </li>
        </ul>

        <p class="nav-section-label">Company</p>
        <ul class="nav-links">
            <li>
                <a href="{{ route('entities.create') }}" class="nav-item {{ request()->routeIs('entities.create') ? 'active' : '' }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    Add Entity
                </a>
            </li>
        </ul>

        <!-- Footer -->
        <div class="sidebar-footer">
            <div class="sidebar-user">
                <div class="sidebar-avatar">
                    {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                </div>
                <div class="sidebar-user-name">{{ Auth::user()->name ?? 'User' }}</div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn-signout">Sign Out</button>
            </form>
        </div>
    </nav>

    <!-- ── Main ────────────────────────────────────────────────────────── -->
    <main class="main-content">

        <!-- Topbar -->
        <header class="topbar">
            @php $activeEntity = \App\Models\Entity::find(session('active_entity_id')); @endphp
            <div class="topbar-entity">
                <div class="topbar-entity-dot"></div>
                {{ $activeEntity ? $activeEntity->name : 'No entity selected' }}
                @if($activeEntity)
                    <span style="font-weight:400;color:var(--text-muted);font-size:0.8rem;">&mdash; {{ $activeEntity->constitution ?? '' }}</span>
                @endif
            </div>
            <input type="text" class="search-bar" placeholder="Search transactions, ledgers…">
        </header>

        <!-- Alerts (global) -->
        @if(session('success'))
            <div style="margin:20px 32px -4px;" class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error') || $errors->any())
            <div style="margin:20px 32px -4px;" class="alert alert-error">
                {{ session('error') ?? $errors->first() }}
            </div>
        @endif

        <!-- Page content -->
        <div class="page-body">
            @yield('content')
        </div>

    </main>
</div>
</body>
</html>
