<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="TOEIC Practice Platform - Luyện thi TOEIC hiệu quả">
    <title>@yield('title', 'TOEIC Practice Platform')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="bg-gray-50 min-h-screen">

    {{-- ── Navbar ──────────────────────────────────────────────────── --}}
    <nav class="bg-white border-b border-gray-200 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">

                {{-- Brand --}}
                <a href="{{ route('home') }}" class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-indigo-600 flex items-center justify-center">
                        <span class="text-white text-sm font-bold">T</span>
                    </div>
                    <span class="font-bold text-gray-900 text-lg">TOEIC<span class="text-indigo-600">Practice</span></span>
                </a>

                {{-- Desktop Nav --}}
                <div class="hidden md:flex items-center gap-6">
                    <a href="{{ route('home') }}"
                       class="text-sm font-medium {{ request()->routeIs('home') ? 'text-indigo-600' : 'text-gray-600 hover:text-gray-900' }} transition-colors">
                        Đề thi
                    </a>
                    @auth
                        <a href="#"
                           class="text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors">
                            Lịch sử
                        </a>
                    @endauth
                </div>

                {{-- Auth area --}}
                <div class="flex items-center gap-3">
                    @guest
                        <a href="{{ route('login') }}"
                           class="text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors">
                            Đăng nhập
                        </a>
                        <a href="{{ route('register') }}"
                           class="text-sm font-medium bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors">
                            Đăng ký
                        </a>
                    @else
                        {{-- User dropdown --}}
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open"
                                    class="flex items-center gap-2 text-sm text-gray-700 hover:text-gray-900">
                                <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-sm">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </div>
                                <span class="hidden sm:block font-medium">{{ auth()->user()->name }}</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div x-show="open" @click.away="open = false"
                                 x-transition
                                 class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-100 py-1 z-50">
                                @if(auth()->user()->isAdmin())
                                    <a href="{{ route('admin.dashboard') }}"
                                       class="block px-4 py-2 text-sm text-indigo-600 hover:bg-indigo-50 font-medium">
                                        Admin Dashboard
                                    </a>
                                    <hr class="my-1 border-gray-100">
                                @endif
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Tài khoản</a>
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Lịch sử thi</a>
                                <hr class="my-1 border-gray-100">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"
                                            class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                        Đăng xuất
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endguest
                </div>

            </div>
        </div>
    </nav>

    {{-- Flash messages --}}
    @if (session('success'))
        <div class="max-w-7xl mx-auto px-4 mt-4">
            <div class="px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">
                {{ session('success') }}
            </div>
        </div>
    @endif
    @if (session('error'))
        <div class="max-w-7xl mx-auto px-4 mt-4">
            <div class="px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
                {{ session('error') }}
            </div>
        </div>
    @endif

    {{-- Main content --}}
    <main>
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="bg-white border-t border-gray-100 mt-16">
        <div class="max-w-7xl mx-auto px-4 py-8 text-center text-sm text-gray-400">
            © {{ date('Y') }} TOEIC Practice Platform. Luyện thi TOEIC hiệu quả.
        </div>
    </footer>

    @stack('scripts')

    {{-- Simple Alpine.js for dropdown (no extra install needed, CDN) --}}
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>
