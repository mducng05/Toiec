@extends('layouts.app')

@section('title', 'Đăng nhập — TOEIC Practice')

@section('content')
<div style="min-height:calc(100vh - 180px);display:flex;align-items:center;justify-content:center;padding:3rem 1.5rem;">
    <div style="width:100%;max-width:440px;">

        {{-- Card --}}
        <div class="card" style="padding:0;overflow:hidden;">
            <div class="gold-bar"></div>

            <div style="padding:2.25rem 2rem;">
                {{-- Header --}}
                <div style="text-align:center;margin-bottom:2rem;">
                    <div style="width:48px;height:48px;background:var(--gold);border-radius:var(--r-md);display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;font-family:var(--font-display);font-size:1.5rem;font-weight:300;color:var(--dark-ink);">
                        T
                    </div>
                    <h1 style="font-size:1.375rem;font-weight:700;color:var(--text-bright);margin:0 0 0.35rem;">
                        Đăng nhập
                    </h1>
                    <p style="font-size:0.875rem;color:var(--text-faint);margin:0;">
                        Tiếp tục lộ trình luyện thi TOEIC của bạn
                    </p>
                </div>

                {{-- Form --}}
                <form id="login-form" method="POST" action="{{ route('login') }}" style="display:flex;flex-direction:column;gap:1.125rem;">
                    @csrf

                    {{-- Email --}}
                    <div>
                        <label for="email" class="label">
                            Email <span style="color:var(--error)">*</span>
                        </label>
                        <input id="email" type="email" name="email"
                               value="{{ old('email') }}"
                               required autocomplete="email" autofocus
                               placeholder="you@example.com"
                               class="input {{ $errors->has('email') ? 'input-error' : '' }}">
                        @error('email')
                            <p style="font-size:0.8125rem;color:var(--error);margin-top:0.375rem;">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div>
                        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0.375rem;">
                            <label for="password" class="label" style="margin:0;">
                                Mật khẩu <span style="color:var(--error)">*</span>
                            </label>
                        </div>
                        <input id="password" type="password" name="password"
                               required autocomplete="current-password"
                               placeholder="••••••••"
                               class="input {{ $errors->has('password') ? 'input-error' : '' }}">
                        @error('password')
                            <p style="font-size:0.8125rem;color:var(--error);margin-top:0.375rem;">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Remember me --}}
                    <div style="display:flex;align-items:center;gap:8px;margin-top:0.25rem;">
                        <input id="remember" type="checkbox" name="remember"
                               style="width:16px;height:16px;accent-color:var(--gold);cursor:pointer;">
                        <label for="remember" style="font-size:0.8125rem;color:var(--text-muted);cursor:pointer;margin:0;">
                            Ghi nhớ đăng nhập
                        </label>
                    </div>

                    {{-- Submit --}}
                    <button id="login-btn" type="submit" class="btn btn-primary btn-lg" style="width:100%;justify-content:center;margin-top:0.5rem;">
                        Đăng nhập
                        <svg style="width:15px;height:15px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </button>
                </form>

                {{-- Register Link --}}
                <div style="margin-top:1.5rem;text-align:center;font-size:0.875rem;color:var(--text-faint);">
                    Chưa có tài khoản?
                    <a href="{{ route('register') }}" style="color:var(--gold);text-decoration:none;font-weight:600;margin-left:4px;">
                        Đăng ký ngay
                    </a>
                </div>
            </div>

            {{-- Dev credentials banner --}}
            @if(app()->environment('local'))
                <div style="padding:1rem 1.5rem;background:var(--bg-deep);border-top:1px solid var(--border);font-size:0.75rem;color:var(--text-muted);">
                    <div style="font-weight:600;color:var(--gold);margin-bottom:4px;letter-spacing:0.04em;text-transform:uppercase;">
                        Tài khoản thử nghiệm (Seed):
                    </div>
                    <div style="display:flex;justify-content:space-between;gap:8px;">
                        <span>Admin: <code style="color:var(--text-warm);">admin@toeic.local</code> / <code style="color:var(--text-warm);">password</code></span>
                        <span>User: <code style="color:var(--text-warm);">user@toeic.local</code> / <code style="color:var(--text-warm);">password</code></span>
                    </div>
                </div>
            @endif

        </div>

    </div>
</div>
@endsection
