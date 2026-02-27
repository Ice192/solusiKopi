@extends('layouts.auth')

@section('title', 'Forgot Password')

@section('content')
    <div class="authentication-wrapper authentication-cover">
        <!-- Logo -->
        <a href="{{ url('/') }}" class="auth-cover-brand d-flex align-items-center gap-2">
            <span class="app-brand-logo demo">
                <i class="ri-cup-line text-primary fs-2"></i>
            </span>
            <span class="app-brand-text demo text-heading fw-semibold">{{ config('app.name') }}</span>
        </a>
        <!-- /Logo -->
        <div class="authentication-inner row m-0">
            <!-- /Left Section -->
            <div class="d-none d-lg-flex col-lg-7 col-xl-8 align-items-center justify-content-center p-12 pb-2">
                <img src="{{ asset('/materialize') }}/assets/img/illustrations/auth-forgot-password-illustration-light.png"
                    class="auth-cover-illustration w-100" alt="auth-illustration"
                    data-app-light-img="illustrations/auth-forgot-password-illustration-light.png"
                    data-app-dark-img="illustrations/auth-forgot-password-illustration-dark.png" />
                <img src="{{ asset('/materialize') }}/assets/img/illustrations/auth-cover-forgot-password-mask-light.png"
                    class="authentication-image" alt="mask"
                    data-app-light-img="illustrations/auth-cover-forgot-password-mask-light.png"
                    data-app-dark-img="illustrations/auth-cover-forgot-password-mask-dark.png" />
            </div>
            <!-- /Left Section -->

            <!-- Forgot Password -->
            <div class="d-flex col-12 col-lg-5 col-xl-4 align-items-center authentication-bg p-sm-12 p-6">
                <div class="w-px-400 mx-auto">

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

                    <h4 class="mb-1">Forgot Password? 🔒</h4>
                    <p class="mb-5">Enter your email and we'll send you instructions to reset your password</p>

                    <form id="formAuthentication" class="mb-5" action="{{ route('password.email') }}" method="POST">
                        @csrf

                        <div class="form-floating form-floating-outline mb-5">
                            <input type="text" class="form-control @error('email')
                                is-invalid
                            @enderror" id="email" name="email"
                                placeholder="Enter your email" autofocus value="{{ old('email') }}" />
                            <label for="email">Email</label>

                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button class="btn btn-primary d-grid w-100">Send Reset Link</button>
                    </form>

                    <div class="text-center">
                        <a href="{{ route('login') }}" class="d-flex align-items-center justify-content-center">
                            <i class="ri-arrow-left-s-line scaleX-n1-rtl ri-20px me-1_5"></i>
                            Back to login
                        </a>
                    </div>


                </div>
            </div>
            <!-- /Forgot Password -->
        </div>
    </div>
@endsection

