@extends('layouts.auth')

@section('title', 'Register')

@section('content')
    <div class="authentication-wrapper authentication-cover">
        <a href="{{ url('/') }}" class="auth-cover-brand d-flex align-items-center gap-2">
            <span class="app-brand-logo demo">
                <i class="ri-cup-line text-primary fs-2"></i>
            </span>
            <span class="app-brand-text demo text-heading fw-semibold">Solusi Kopi</span>
        </a>

        <div class="authentication-inner row m-0">
            <div class="d-none d-lg-flex col-lg-7 col-xl-8 align-items-center justify-content-center p-12 pb-2">
                <img src="{{ asset('/materialize') }}/assets/img/illustrations/auth-register-illustration-light.png"
                    class="auth-cover-illustration w-100" alt="auth-illustration"
                    data-app-light-img="illustrations/auth-register-illustration-light.png"
                    data-app-dark-img="illustrations/auth-register-illustration-dark.png" />
                <img src="{{ asset('/materialize') }}/assets/img/illustrations/auth-cover-register-mask-light.png"
                    class="authentication-image" alt="mask"
                    data-app-light-img="illustrations/auth-cover-register-mask-light.png"
                    data-app-dark-img="illustrations/auth-cover-register-mask-dark.png" />
            </div>

            <div
                class="d-flex col-12 col-lg-5 col-xl-4 align-items-center authentication-bg position-relative py-sm-12 px-12 py-6">
                <div class="w-px-400 mx-auto pt-5 pt-lg-0">

                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible" role="alert">
                            <h4 class="alert-heading d-flex align-items-center">
                                <span class="alert-icon rounded">
                                    <i class="ri-error-warning-line ri-22px"></i>
                                </span>
                                Something went wrong!
                            </h4>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <h4 class="mb-1">Daftar Akun Solusi Kopi</h4>
                    <p class="mb-5">Registrasi ini khusus untuk customer.</p>

                    <form id="formAuthentication" class="mb-5" action="{{ route('register') }}" method="POST">
                        @csrf

                        <div class="form-floating form-floating-outline mb-5">
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                name="name" placeholder="Enter your name" autofocus value="{{ old('name') }}" />
                            <label for="name">Name</label>

                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-floating form-floating-outline mb-5">
                            <input type="text" class="form-control @error('email') is-invalid @enderror" id="email"
                                name="email" placeholder="Enter your email" value="{{ old('email') }}" />
                            <label for="email">Email</label>

                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-5">
                            <div class="input-group input-group-merge password-field-group">
                                <div class="form-floating form-floating-outline password-floating">
                                    <input type="password" id="password"
                                        class="form-control @error('password') is-invalid @enderror" name="password"
                                        placeholder="{!! passwordPlainText() !!}" aria-describedby="password" />
                                    <label for="password">Password</label>
                                </div>
                                <button class="input-group-text cursor-pointer password-toggle-btn" id="toggle-password"
                                    type="button" aria-label="Tampilkan password">
                                    <i class="ri-eye-off-line" id="toggle-password-icon"></i>
                                </button>
                            </div>

                            @error('password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-5">
                            <div class="input-group input-group-merge password-field-group">
                                <div class="form-floating form-floating-outline password-floating">
                                    <input type="password" id="password_confirmation"
                                        class="form-control @error('password_confirmation') is-invalid @enderror"
                                        name="password_confirmation" placeholder="{!! passwordPlainText() !!}"
                                        aria-describedby="password_confirmation" />
                                    <label for="password_confirmation">Password Confirmation</label>
                                </div>
                                <button class="input-group-text cursor-pointer password-toggle-btn"
                                    id="toggle-password-confirmation" type="button"
                                    aria-label="Tampilkan konfirmasi password">
                                    <i class="ri-eye-off-line" id="toggle-password-confirmation-icon"></i>
                                </button>
                            </div>

                            @error('password_confirmation')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary d-grid w-100">Sign up</button>
                    </form>

                    <p class="text-center">
                        <span>Already have an account?</span>
                        <a href="{{ route('login') }}">
                            <span>Sign in instead</span>
                        </a>
                    </p>

                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .password-toggle-btn {
            border: 1px solid var(--bs-border-color);
            border-left: 0;
            background-color: #fff;
            color: #6c757d;
            min-width: 46px;
        }

        .password-floating {
            flex: 1 1 auto;
        }

        .password-floating .form-control {
            border-right: 0;
        }

        .password-field-group:focus-within .form-control,
        .password-field-group:focus-within .password-toggle-btn {
            border-color: var(--bs-primary);
            box-shadow: none;
        }

        .password-toggle-btn:hover {
            background-color: #f8f9fa;
            color: var(--bs-primary);
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const setupPasswordToggle = (inputId, buttonId, iconId, shownLabel, hiddenLabel) => {
                const input = document.getElementById(inputId);
                const button = document.getElementById(buttonId);
                const icon = document.getElementById(iconId);

                if (!input || !button || !icon) {
                    return;
                }

                button.addEventListener('click', function() {
                    const hidden = input.type === 'password';
                    input.type = hidden ? 'text' : 'password';
                    icon.classList.toggle('ri-eye-line', hidden);
                    icon.classList.toggle('ri-eye-off-line', !hidden);
                    button.setAttribute('aria-label', hidden ? shownLabel : hiddenLabel);
                });
            };

            setupPasswordToggle(
                'password',
                'toggle-password',
                'toggle-password-icon',
                'Sembunyikan password',
                'Tampilkan password'
            );
            setupPasswordToggle(
                'password_confirmation',
                'toggle-password-confirmation',
                'toggle-password-confirmation-icon',
                'Sembunyikan konfirmasi password',
                'Tampilkan konfirmasi password'
            );
        });
    </script>
@endpush
