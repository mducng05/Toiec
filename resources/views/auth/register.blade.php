@extends('layouts.app')

@section('title', 'Đăng ký tài khoản — TOEIC Practice')

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
                        Tạo tài khoản mới
                    </h1>
                    <p style="font-size:0.875rem;color:var(--text-faint);margin:0;">
                        Tham gia luyện thi TOEIC hoàn toàn miễn phí
                    </p>
                </div>

                {{-- Form --}}
                <form id="register-form" method="POST" action="{{ route('register') }}" style="display:flex;flex-direction:column;gap:1.125rem;">
                    @csrf

                    {{-- Name --}}
                    <div>
                        <label for="name" class="label">
                            Họ và tên <span style="color:var(--error)">*</span>
                        </label>
                        <input id="name" type="text" name="name"
                               value="{{ old('name') }}"
                               required autocomplete="name" autofocus
                               placeholder="Nguyễn Văn A"
                               class="input {{ $errors->has('name') ? 'input-error' : '' }}">
                        @error('name')
                            <p style="font-size:0.8125rem;color:var(--error);margin-top:0.375rem;">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label for="email" class="label">
                            Email <span style="color:var(--error)">*</span>
                        </label>
                        <input id="email" type="email" name="email"
                               value="{{ old('email') }}"
                               required autocomplete="email"
                               placeholder="you@example.com"
                               class="input {{ $errors->has('email') ? 'input-error' : '' }}">
                        @error('email')
                            <p style="font-size:0.8125rem;color:var(--error);margin-top:0.375rem;">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div>
                        <label for="password" class="label">
                            Mật khẩu <span style="color:var(--error)">*</span>
                        </label>
                        <input id="password" type="password" name="password"
                               required autocomplete="new-password"
                               placeholder="Tối thiểu 8 ký tự"
                               class="input {{ $errors->has('password') ? 'input-error' : '' }}">
                        @error('password')
                            <p style="font-size:0.8125rem;color:var(--error);margin-top:0.375rem;">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Password Confirmation --}}
                    <div>
                        <label for="password_confirmation" class="label">
                            Xác nhận mật khẩu <span style="color:var(--error)">*</span>
                        </label>
                        <input id="password_confirmation" type="password" name="password_confirmation"
                               required autocomplete="new-password"
                               placeholder="Nhập lại mật khẩu"
                               class="input">
                    </div>

                    {{-- Submit --}}
                    <button id="register-btn" type="submit" class="btn btn-primary btn-lg" style="width:100%;justify-content:center;margin-top:0.5rem;">
                        Tạo tài khoản
                        <svg style="width:15px;height:15px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </button>
                </form>

                {{-- Login Link --}}
                <div style="margin-top:1.5rem;text-align:center;font-size:0.875rem;color:var(--text-faint);">
                    Đã có tài khoản?
                    <a href="{{ route('login') }}" style="color:var(--gold);text-decoration:none;font-weight:600;margin-left:4px;">
                        Đăng nhập
                    </a>
                </div>
            </div>

        </div>

    </div>
</div>
@endsection
