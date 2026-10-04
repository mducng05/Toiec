<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="@yield('meta-description', 'Luyện thi TOEIC hiệu quả — đề thi thực tế, audio chuẩn, kết quả tức thì')">
    <title>@yield('title', 'TOEIC Practice Platform')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
    <style>
        /* User-facing layout */
        .site-nav {
            position: sticky;
            top: 0;
            z-index: 50;
            height: 56px;
            display: flex;
            align-items: center;
            background: oklch(7% 0.006 95 / 0.92);
            backdrop-filter: blur(12px) saturate(1.4);
            border-bottom: 1px solid var(--border);
        }
        .site-nav-inner {
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
            padding: 0 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1.5rem;
        }
        .site-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }
        .site-logo-mark {
            width: 30px; height: 30px;
            background: var(--gold);
            border-radius: var(--r-xs);
            display: flex; align-items: center; justify-content: center;
            font-family: var(--font-display);
            font-weight: 300;
            font-size: 1.1rem;
            color: var(--dark-ink);
        }
        .site-logo-text {
            font-size: 0.9375rem;
            font-weight: 600;
            color: var(--text-bright);
            letter-spacing: 0.01em;
        }
        .site-logo-text em {
            font-style: normal;
            color: var(--gold);
        }
        .site-nav-links {
            display: flex;
            align-items: center;
            gap: 0.25rem;
            flex: 1;
        }
        .site-nav-link {
            padding: 0.4rem 0.75rem;
            border-radius: var(--r-md);
            font-size: 0.875rem;
            color: var(--text-muted);
            text-decoration: none;
            transition: all 0.12s;
        }
        .site-nav-link:hover { color: var(--text-warm); background: var(--bg-raised); }
        .site-nav-link.active { color: var(--gold); }

        .site-nav-auth { display: flex; align-items: center; gap: 8px; }

        /* Dropdown */
        .dropdown { position: relative; }
        .dropdown-menu {
            position: absolute;
            right: 0;
            top: calc(100% + 8px);
            min-width: 180px;
            background: var(--bg-raised);
            border: 1px solid var(--border);
            border-radius: var(--r-xl);
            padding: 0.375rem;
            box-shadow: 0 12px 40px oklch(0% 0 0 / 0.5);
            z-index: 100;
        }
        .dropdown-item {
            display: block;
            padding: 0.5rem 0.75rem;
            border-radius: var(--r-md);
            font-size: 0.875rem;
            color: var(--text-muted);
            text-decoration: none;
            transition: all 0.1s;
        }
        .dropdown-item:hover { background: var(--bg-graphite); color: var(--text-warm); }
        .dropdown-item.gold { color: var(--gold); }
        .dropdown-item.gold:hover { background: oklch(84% 0.19 80.46 / 0.08); }
        .dropdown-item.danger { color: var(--error); }
        .dropdown-item.danger:hover { background: oklch(60% 0.20 25 / 0.1); }
        .dropdown-sep { border: none; border-top: 1px solid var(--border); margin: 0.3rem 0; }

        .user-avatar {
            width: 30px; height: 30px;
            border-radius: var(--r-md);
            background: oklch(84% 0.19 80.46 / 0.12);
            border: 1px solid var(--border-gold);
            display: flex; align-items: center; justify-content: center;
            font-size: 0.8125rem;
            font-weight: 600;
            color: var(--gold);
            cursor: pointer;
        }

        /* Site footer */
        .site-footer {
            border-top: 1px solid var(--border);
            padding: 2rem 1.5rem;
            margin-top: 5rem;
        }
        .site-footer-inner {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }
        .site-footer-copy {
            font-size: 0.8125rem;
            color: var(--text-faint);
        }
        .site-footer-copy em { font-style: normal; color: var(--gold); }
    </style>
</head>
<body>

    {{-- ── Navigation ──────────────────────────────────────────────── --}}
    <nav class="site-nav">
        <div class="site-nav-inner">

            {{-- Logo --}}
            <a href="{{ route('home') }}" class="site-logo">
                <div class="site-logo-mark">T</div>
                <span class="site-logo-text">TOEIC<em>Practice</em></span>
            </a>

            {{-- Nav links --}}
            <div class="site-nav-links">
                <a href="{{ route('home') }}"
                   class="site-nav-link {{ request()->routeIs('home') ? 'active' : '' }}">
                    Đề thi
                </a>
                @auth
                    <a href="#" class="site-nav-link">Lịch sử</a>
                @endauth
            </div>

            {{-- Auth --}}
            <div class="site-nav-auth">
                @guest
                    <a href="{{ route('login') }}" class="btn btn-ghost btn-sm">Đăng nhập</a>
                    <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Đăng ký</a>
                @else
                    <div class="dropdown" x-data="{ open: false }">
                        <div class="user-avatar" @click="open = !open">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <div class="dropdown-menu" x-show="open" @click.away="open = false"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100">

                            <div style="padding: 0.5rem 0.75rem 0.25rem;">
                                <div style="font-size:0.8125rem;font-weight:600;color:var(--text-bright);">{{ auth()->user()->name }}</div>
                                <div style="font-size:0.75rem;color:var(--text-faint);">{{ auth()->user()->email }}</div>
                            </div>
                            <hr class="dropdown-sep">

                            @if(auth()->user()->isAdmin())
                                <a href="{{ route('admin.dashboard') }}" class="dropdown-item gold">
                                    ⬡ Admin Dashboard
                                </a>
                                <hr class="dropdown-sep">
                            @endif

                            <a href="#" class="dropdown-item">Tài khoản</a>
                            <a href="#" class="dropdown-item">Lịch sử thi</a>
                            <hr class="dropdown-sep">

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item danger" style="width:100%;text-align:left;background:none;border:none;cursor:pointer;font-family:inherit;">
                                    Đăng xuất
                                </button>
                            </form>
                        </div>
                    </div>
                @endguest
            </div>

        </div>
    </nav>

    {{-- Flash messages --}}
    @if(session('success'))
        <div style="max-width:1200px;margin:1rem auto;padding:0 1.5rem;">
            <div class="alert alert-success animate-in">
                <svg style="width:16px;height:16px;flex-shrink:0;margin-top:1px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ session('success') }}
            </div>
        </div>
    @endif
    @if(session('error'))
        <div style="max-width:1200px;margin:1rem auto;padding:0 1.5rem;">
            <div class="alert alert-error animate-in">
                <svg style="width:16px;height:16px;flex-shrink:0;margin-top:1px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ session('error') }}
            </div>
        </div>
    @endif

    {{-- Main content --}}
    <main>@yield('content')</main>

    {{-- Footer --}}
    <footer class="site-footer">
        <div class="site-footer-inner">
            <p class="site-footer-copy">© {{ date('Y') }} <em>TOEIC Practice</em>. Luyện thi TOEIC hiệu quả.</p>
            <div style="display:flex;gap:1rem;">
                <a href="#" style="font-size:0.8125rem;color:var(--text-faint);">Điều khoản</a>
                <a href="#" style="font-size:0.8125rem;color:var(--text-faint);">Liên hệ</a>
            </div>
        </div>
    </footer>

    @stack('scripts')
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>
