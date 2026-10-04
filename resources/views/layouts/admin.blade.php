<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="TOEIC Practice Platform — Luyện thi TOEIC hiệu quả">
    <title>@yield('title', 'Admin') — TOEIC Practice</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
    <style>
        /* Admin-specific layout */
        .admin-shell { display: flex; min-height: 100vh; }

        /* Sidebar */
        .sidebar {
            width: 240px;
            flex-shrink: 0;
            background: #ffffff;
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0; left: 0; bottom: 0;
            z-index: 40;
            overflow-y: auto;
        }
        .sidebar-brand {
            height: 60px;
            display: flex;
            align-items: center;
            padding: 0 1.25rem;
            border-bottom: 1px solid var(--border);
            gap: 10px;
            flex-shrink: 0;
            background: #ffffff;
        }
        .sidebar-logo {
            width: 30px; height: 30px;
            background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%);
            border-radius: var(--r-md);
            display: flex; align-items: center; justify-content: center;
            font-family: var(--font-display);
            font-weight: 700;
            font-size: 1rem;
            color: #ffffff;
            box-shadow: 0 2px 5px rgba(79, 70, 229, 0.3);
        }
        .sidebar-brand-text {
            font-size: 0.9375rem;
            font-weight: 700;
            color: var(--text-bright);
            letter-spacing: -0.01em;
        }
        .sidebar-brand-text span { color: var(--gold); font-weight: 600; }

        .sidebar-nav { flex: 1; padding: 1rem 0.75rem; }
        .sidebar-section {
            font-size: 0.6875rem;
            font-weight: 600;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--text-muted);
            padding: 0.5rem 0.5rem 0.25rem;
            margin-top: 1rem;
        }
        .sidebar-section:first-child { margin-top: 0; }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 9px;
            padding: 0.55rem 0.75rem;
            border-radius: var(--r-md);
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--text-muted);
            text-decoration: none;
            transition: all 0.12s;
            margin-bottom: 2px;
        }
        .nav-item:hover { background: var(--bg-hover); color: var(--text-bright); }
        .nav-item.active {
            background: oklch(55% 0.22 265 / 0.1);
            color: var(--gold);
            border: 1px solid oklch(55% 0.22 265 / 0.2);
            font-weight: 600;
        }
        .nav-item svg { width: 16px; height: 16px; flex-shrink: 0; opacity: 0.7; }
        .nav-item.active svg { opacity: 1; }

        .sidebar-footer {
            border-top: 1px solid var(--border);
            padding: 0.875rem 1rem;
            flex-shrink: 0;
        }
        .sidebar-user {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 0.75rem;
        }
        .sidebar-avatar {
            width: 30px; height: 30px;
            border-radius: var(--r-md);
            background: oklch(84% 0.19 80.46 / 0.15);
            border: 1px solid var(--border-gold);
            display: flex; align-items: center; justify-content: center;
            font-size: 0.8125rem;
            font-weight: 600;
            color: var(--gold);
        }
        .sidebar-user-info { min-width: 0; }
        .sidebar-user-name {
            font-size: 0.8125rem;
            font-weight: 500;
            color: var(--text-bright);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .sidebar-user-role {
            font-size: 0.6875rem;
            color: var(--gold);
            letter-spacing: 0.04em;
        }

        /* Main area */
        .admin-main {
            margin-left: 240px;
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .admin-topbar {
            height: 56px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.75rem;
            border-bottom: 1px solid var(--border);
            background: var(--bg-ground);
            position: sticky;
            top: 0;
            z-index: 30;
        }
        .admin-topbar-title {
            font-size: 0.9375rem;
            font-weight: 600;
            color: var(--text-bright);
        }
        .admin-content { padding: 1.75rem; flex: 1; }
    </style>
</head>
<body>
<div class="admin-shell">

    {{-- ── Sidebar ─────────────────────────────────────────────────── --}}
    <aside class="sidebar">
        {{-- Brand --}}
        <div class="sidebar-brand">
            <div class="sidebar-logo">T</div>
            <span class="sidebar-brand-text">TOEIC <span>Admin</span></span>
        </div>

        {{-- Navigation --}}
        <nav class="sidebar-nav">

            <div class="sidebar-section">Tổng quan</div>
            <a href="{{ route('admin.dashboard') }}"
               class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <rect x="3" y="3" width="7" height="7" rx="1" stroke-width="1.5"/>
                    <rect x="14" y="3" width="7" height="7" rx="1" stroke-width="1.5"/>
                    <rect x="3" y="14" width="7" height="7" rx="1" stroke-width="1.5"/>
                    <rect x="14" y="14" width="7" height="7" rx="1" stroke-width="1.5"/>
                </svg>
                Dashboard
            </a>

            <div class="sidebar-section">Nội dung</div>
            <a href="{{ route('admin.exams.index') }}"
               class="nav-item {{ request()->routeIs('admin.exams.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0121 9.586V19a2 2 0 01-2 2z"/>
                </svg>
                Đề thi
            </a>
            <a href="#"
               class="nav-item {{ request()->routeIs('admin.questions.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Câu hỏi
            </a>

            <div class="sidebar-section">Hệ thống</div>
            <a href="#"
               class="nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                Người dùng
            </a>
            <a href="#" class="nav-item">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Lịch sử thi
            </a>

        </nav>

        {{-- User footer --}}
        <div class="sidebar-footer">
            <div class="sidebar-user">
                <div class="sidebar-avatar">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div class="sidebar-user-info">
                    <div class="sidebar-user-name">{{ auth()->user()->name }}</div>
                    <div class="sidebar-user-role">Admin</div>
                </div>
            </div>
            <div style="display:flex; gap:6px;">
                <a href="{{ route('home') }}" target="_blank"
                   class="btn btn-ghost btn-sm" style="flex:1; font-size:0.75rem;">
                    Xem web
                </a>
                <form method="POST" action="{{ route('logout') }}" style="flex:1;">
                    @csrf
                    <button type="submit" class="btn btn-danger btn-sm" style="width:100%; font-size:0.75rem;">
                        Đăng xuất
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- ── Main ─────────────────────────────────────────────────────── --}}
    <div class="admin-main">

        {{-- Top bar --}}
        <header class="admin-topbar">
            <h1 class="admin-topbar-title">@yield('page-title', 'Dashboard')</h1>
            <div style="display:flex; align-items:center; gap:0.75rem;">
                @yield('topbar-actions')
                <span class="badge badge-gold">Admin</span>
            </div>
        </header>

        {{-- Flash messages --}}
        @if(session('success'))
            <div style="padding: 0 1.75rem; margin-top: 1.25rem;">
                <div class="alert alert-success animate-in">
                    <svg style="width:16px;height:16px;flex-shrink:0;margin-top:1px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ session('success') }}
                </div>
            </div>
        @endif
        @if(session('error'))
            <div style="padding: 0 1.75rem; margin-top: 1.25rem;">
                <div class="alert alert-error animate-in">
                    <svg style="width:16px;height:16px;flex-shrink:0;margin-top:1px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ session('error') }}
                </div>
            </div>
        @endif

        {{-- Content --}}
        <main class="admin-content">
            @yield('content')
        </main>

    </div>
</div>

@stack('scripts')
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>
