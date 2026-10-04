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
            height: 60px;
            display: flex;
            align-items: center;
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(16px) saturate(1.5);
            border-bottom: 1px solid var(--border);
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.03);
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
            width: 32px; height: 32px;
            background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%);
            border-radius: var(--r-md);
            display: flex; align-items: center; justify-content: center;
            font-family: var(--font-display);
            font-weight: 700;
            font-size: 1.15rem;
            color: #ffffff;
            box-shadow: 0 2px 6px rgba(79, 70, 229, 0.3);
        }
        .site-logo-text {
            font-size: 1rem;
            font-weight: 700;
            color: var(--text-bright);
            letter-spacing: -0.01em;
        }
        .site-logo-text em {
            font-style: normal;
            color: var(--gold);
        }
        .site-nav-links {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            flex: 1;
        }
        .site-nav-link {
            padding: 0.45rem 0.85rem;
            border-radius: var(--r-md);
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--text-muted);
            text-decoration: none;
            transition: all 0.15s ease;
        }
        .site-nav-link:hover { color: var(--text-bright); background: var(--bg-hover); }
        .site-nav-link.active { color: var(--gold); font-weight: 600; background: oklch(55% 0.22 265 / 0.08); }

        .site-nav-auth { display: flex; align-items: center; gap: 10px; }

        /* Dropdown */
        .dropdown { position: relative; }
        .dropdown-menu {
            position: absolute;
            right: 0;
            top: calc(100% + 8px);
            min-width: 200px;
            background: #ffffff;
            border: 1px solid var(--border);
            border-radius: var(--r-xl);
            padding: 0.5rem;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.06);
            z-index: 100;
        }
        .dropdown-item {
            display: block;
            padding: 0.5rem 0.75rem;
            border-radius: var(--r-md);
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--text-warm);
            text-decoration: none;
            transition: all 0.12s;
        }
        .dropdown-item:hover { background: var(--bg-hover); color: var(--gold); }
        .dropdown-item.gold { color: var(--gold); font-weight: 600; }
        .dropdown-item.gold:hover { background: oklch(55% 0.22 265 / 0.08); }
        .dropdown-item.danger { color: var(--error); }
        .dropdown-item.danger:hover { background: #fee2e2; }
        .dropdown-sep { border: none; border-top: 1px solid var(--border); margin: 0.35rem 0; }

        .user-avatar {
            width: 34px; height: 34px;
            border-radius: var(--r-full);
            background: linear-gradient(135deg, oklch(55% 0.22 265 / 0.15) 0%, oklch(55% 0.22 265 / 0.25) 100%);
            border: 1.5px solid var(--border-gold);
            display: flex; align-items: center; justify-content: center;
            font-size: 0.875rem;
            font-weight: 700;
            color: var(--gold);
            cursor: pointer;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }
        .user-avatar:hover {
            transform: scale(1.05);
            box-shadow: 0 2px 8px rgba(79, 70, 229, 0.25);
        }

        /* Site footer */
        .site-footer {
            border-top: 1px solid var(--border);
            padding: 2.5rem 1.5rem;
            margin-top: 6rem;
            background: #ffffff;
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
            font-size: 0.875rem;
            color: var(--text-muted);
        }
        .site-footer-copy em { font-style: normal; color: var(--gold); font-weight: 600; }
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
                    Thư viện đề
                </a>
                @auth
                    <a href="{{ route('user.my-exams.index') }}"
                       class="site-nav-link {{ request()->routeIs('user.my-exams.*') ? 'active' : '' }}">
                        Đề của tôi
                    </a>
                    <a href="{{ route('user.history.index') }}"
                       class="site-nav-link {{ request()->routeIs('user.history.*') ? 'active' : '' }}">
                        Lịch sử thi
                    </a>
                @endauth
            </div>

            {{-- Auth & CTA --}}
            <div class="site-nav-auth">
                @auth
                    <a href="{{ route('user.my-exams.create') }}" class="btn btn-primary btn-sm" style="display:inline-flex;align-items:center;gap:6px;">
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                        </svg>
                        Tạo đề riêng
                    </a>

                    <div class="dropdown" x-data="{ open: false }">
                        <div class="user-avatar" @click="open = !open">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <div class="dropdown-menu" x-show="open" @click.away="open = false"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100" style="display: none;">

                            <div style="padding: 0.5rem 0.75rem 0.35rem;">
                                <div style="font-size:0.875rem;font-weight:600;color:var(--text-bright);">{{ auth()->user()->name }}</div>
                                <div style="font-size:0.75rem;color:var(--text-muted);">{{ auth()->user()->email }}</div>
                            </div>
                            <hr class="dropdown-sep">

                            <a href="{{ route('user.my-exams.index') }}" class="dropdown-item">
                                📚 Đề thi của tôi
                            </a>
                            <a href="{{ route('user.history.index') }}" class="dropdown-item">
                                📊 Lịch sử làm bài
                            </a>
                            <a href="{{ route('user.my-exams.create') }}" class="dropdown-item gold">
                                ➕ Upload đề mới
                            </a>
                            <hr class="dropdown-sep">

                            @if(auth()->user()->isAdmin())
                                <a href="{{ route('admin.dashboard') }}" class="dropdown-item gold">
                                    ⚙️ Quản trị Admin
                                </a>
                                <hr class="dropdown-sep">
                            @endif

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item danger" style="width:100%;text-align:left;background:none;border:none;cursor:pointer;font-family:inherit;">
                                    Đăng xuất
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="btn btn-ghost btn-sm">Đăng nhập</a>
                    <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Đăng ký</a>
                @endauth
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
