@extends('layouts.auth')

@section('title', 'Login')

@section('content')
    <div class="authentication-wrapper authentication-cover">
        <div class="authentication-inner row m-0 min-vh-100">
            <div
                class="d-none d-lg-flex col-lg-7 col-xl-8 align-items-center justify-content-center p-12 auth-hero-panel">
                <div class="hero-copy">
                    <p class="hero-kicker mb-2">Solusi Kopi</p>
                    <h2 class="hero-title mb-3">PLATFORM OPERASIONAL CAFE YANG CEPAT, RAPI, DAN TERUKUR</h2>
                </div>
            </div>

            <div class="d-flex col-12 col-lg-5 col-xl-4 align-items-center authentication-bg position-relative px-6 px-sm-12 py-8">
                <div class="w-100 mx-auto login-panel">
                    <a href="{{ route('dashboard') }}" class="app-brand d-flex align-items-center gap-2 mb-5">
                        <span class="app-brand-logo demo">
                            <i class="ri-cup-line text-primary fs-2"></i>
                        </span>
                        <span class="app-brand-text demo text-heading fw-semibold">Solusi Kopi</span>
                    </a>

                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible mb-4" role="alert">
                            <h6 class="alert-heading d-flex align-items-center mb-2">
                                <i class="ri-error-warning-line ri-20px me-2"></i>
                                Login gagal
                            </h6>
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <h4 class="mb-1 auth-heading">Masuk ke {{ config('app.name') }}</h4>
                    {{-- <p class="mb-4 text-muted">Gunakan akun sesuai peran untuk melanjutkan.</p> --}}

                    {{-- <div class="role-access-list mb-5">
                        <span class="role-pill"><i class="ri-user-line"></i> Costumer</span>
                        <span class="role-pill"><i class="ri-user-settings-line"></i> User</span>
                        <span class="role-pill"><i class="ri-cash-line"></i> Kasir</span>
                    </div> --}}

                    <form id="formAuthentication" class="mb-4" action="{{ route('login') }}" method="POST">
                        @csrf

                        <div class="form-floating form-floating-outline mb-4">
                            <input type="email"
                                class="form-control @error('email') is-invalid @enderror"
                                id="email" name="email" placeholder="name@example.com" autofocus
                                value="{{ old('email') }}" />
                            <label for="email">Email</label>

                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-floating form-floating-outline mb-4">
                            <div class="form-floating form-floating-outline password-field">
                                <input type="password" id="password"
                                    class="form-control password-input @error('password') is-invalid @enderror"
                                    name="password" placeholder="Masukkan password" autocomplete="current-password" />
                                <label for="password">Password</label>
                                <button class="password-toggle-btn cursor-pointer" id="toggle-password"
                                    type="button" aria-label="Tampilkan password">
                                    <i class="ri-eye-off-line" id="toggle-password-icon"></i>
                                </button>
                            </div>
                            @error('password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4 d-flex justify-content-between align-items-center">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="remember-me" name="remember"
                                    {{ old('remember') ? 'checked' : '' }} />
                                <label class="form-check-label" for="remember-me">Remember me</label>
                            </div>
                            <a href="{{ route('password.request') }}">Forgot Password?</a>
                        </div>

                        <button type="submit" class="btn btn-primary d-grid w-100">Sign in</button>
                    </form>

                    <p class="text-center mb-0">
                        <span>Belum punya akun?</span>
                        <a href="{{ route('register') }}" class="fw-medium">Daftar sekarang</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .authentication-wrapper {
            background: radial-gradient(circle at 8% 10%, rgba(198, 246, 226, 0.65), transparent 42%),
                radial-gradient(circle at 92% 14%, rgba(255, 229, 198, 0.65), transparent 44%),
                #f5f7fb;
        }

        .auth-hero-panel {
            background: linear-gradient(135deg, #093545 0%, #0f6f8a 100%);
            color: #fff;
        }

        .hero-copy {
            max-width: 480px;
        }

        .hero-title {
            color: #f5fbff;
            text-shadow: 0 2px 12px rgba(0, 0, 0, 0.18);
        }

        .hero-kicker {
            letter-spacing: 0.08em;
            text-transform: uppercase;
            font-weight: 600;
            color: #d6f6ff;
        }

        .auth-heading {
            font-weight: 700;
            letter-spacing: -0.2px;
        }

        .login-panel {
            max-width: 410px;
        }

        .role-access-list {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .role-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            font-size: 0.78rem;
            font-weight: 600;
            color: #0f3d57;
            background: #e2f2ff;
            border: 1px solid #c7e6ff;
            border-radius: 999px;
            padding: 0.35rem 0.7rem;
        }

        .password-field {
            position: relative;
        }

        .password-input {
            padding-right: 3rem;
        }

        .password-toggle-btn {
            position: absolute;
            right: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            border: 0;
            background: transparent;
            color: #6c757d;
            z-index: 5;
            width: 2rem;
            height: 2rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .password-toggle-btn:hover {
            color: var(--bs-primary);
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const passwordInput = document.getElementById('password');
            const toggleButton = document.getElementById('toggle-password');
            const toggleIcon = document.getElementById('toggle-password-icon');

            if (!passwordInput || !toggleButton || !toggleIcon) {
                return;
            }

            toggleButton.addEventListener('click', function() {
                const hidden = passwordInput.type === 'password';
                passwordInput.type = hidden ? 'text' : 'password';
                toggleIcon.classList.toggle('ri-eye-line', hidden);
                toggleIcon.classList.toggle('ri-eye-off-line', !hidden);
                toggleButton.setAttribute('aria-label', hidden ? 'Sembunyikan password' : 'Tampilkan password');
            });
        });
    </script>
@endpush
