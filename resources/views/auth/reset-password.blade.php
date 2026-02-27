@extends('layouts.auth')
@section('title', 'Reset Password')

@section('content')
    <div class="position-relative">
        <div class="authentication-wrapper authentication-cover">
            <!-- Logo -->
            <a href="{{ route('dashboard') }}" class="auth-cover-brand d-flex align-items-center gap-2">
                <span class="app-brand-logo demo">
                    <i class="ri-cup-line text-primary fs-2"></i>
                </span>
                <span class="app-brand-text demo text-heading fw-semibold">{{ config('app.name') }}</span>
            </a>
            <!-- /Logo -->
            <div class="authentication-inner row m-0">
                <!-- /Left Section -->
                <div class="d-none d-lg-flex col-lg-7 col-xl-8 align-items-center justify-content-center p-12 pb-2">
                    <img src="{{ asset('/materialize') }}/assets/img/illustrations/auth-reset-password-illustration-light.png"
                        class="auth-cover-illustration w-100" alt="auth-illustration"
                        data-app-light-img="illustrations/auth-reset-password-illustration-light.png"
                        data-app-dark-img="illustrations/auth-reset-password-illustration-dark.png" />
                    <img src="{{ asset('/materialize') }}/assets/img/illustrations/auth-cover-reset-password-mask-light.png"
                        class="authentication-image" alt="mask"
                        data-app-light-img="illustrations/auth-cover-reset-password-mask-light.png"
                        data-app-dark-img="illustrations/auth-cover-reset-password-mask-dark.png" />
                </div>
                <!-- /Left Section -->

                <!-- Reset Password -->
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
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        <h4 class="mb-1">Reset Password 🔒</h4>
                        <p class="mb-5">Your new password must be different from previously used passwords</p>

                        <form id="formAuthentication" class="mb-5" action="{{ route('password.store') }}" method="POST">
                            @csrf

                            <input type="hidden" name="token" value="{{ $request->route('token') }}">
                            <input type="hidden" name="email" value="{{ old('email', $request->email) }}">

                            <div class="mb-5 form-password-toggle">
                                <div class="input-group input-group-merge">
                                    <div class="form-floating form-floating-outline">
                                        <input type="password" id="password"
                                            class="form-control @error('password')
                                            is-invalid
                                        @enderror"
                                            name="password"
                                            placeholder="{!! passwordPlainText() !!}"
                                            aria-describedby="password" />
                                        <label for="password">New Password</label>
                                    </div>
                                    <span class="input-group-text cursor-pointer"><i class="ri-eye-off-line"></i></span>
                                </div>

                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-5 form-password-toggle">
                                <div class="input-group input-group-merge">
                                    <div class="form-floating form-floating-outline">
                                        <input type="password" id="password_confirmation"
                                            class="form-control @error('password_confirmation')
                                            is-invalid
                                        @enderror"
                                            name="password_confirmation"
                                            placeholder="{!! passwordPlainText() !!}"
                                            aria-describedby="password" />
                                        <label for="password_confirmation">Confirm Password</label>
                                    </div>
                                    <span class="input-group-text cursor-pointer"><i class="ri-eye-off-line"></i></span>
                                </div>

                                @error('password_confirmation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <button class="btn btn-primary d-grid w-100 mb-5">Set new password</button>

                            <div class="text-center">
                                <a href="{{ route('login') }}" class="d-flex align-items-center justify-content-center">
                                    <i class="ri-arrow-left-s-line scaleX-n1-rtl ri-20px me-1_5"></i>
                                    Back to login
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- /Reset Password -->
            </div>
        </div>
    </div>
@endsection

