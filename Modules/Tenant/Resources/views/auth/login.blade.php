@extends('tenant::layouts.auth')

@section('content')
    <section class="login-section d-flex align-items-center justify-content-center" dir="{{ app()->getLocale() === 'arabic' ? 'rtl' : 'ltr' }}">
        <div class="container-fluid h-100">
            <div class="row h-100 g-0 position-relative">
                <div id="radius-shape-1" class="position-absolute rounded-circle shadow-5-strong"></div>
                <div id="radius-shape-2" class="position-absolute shadow-5-strong"></div>
                <!-- Left Column: Welcome -->
                <div class="col-lg-6 d-flex flex-column justify-content-center align-items-center p-5 left-column-bg">
                    <div class="w-100 text-center text-lg-start">
                        <h1 class="display-3 fw-bold mb-4" style="color:#506ab2">
                            {{ __('Welcome to ISO Click') }}
                        </h1>
                        
                    </div>
                </div>
                <!-- Right Column: Login Form -->
                <div class="col-lg-6 d-flex align-items-center justify-content-center right-column-bg">
                    <div class="w-100" style="max-width: 420px;">
                        <div class="auth-glass-form p-4">
                            <div class="text-center mb-4">
                                {{-- <div class="mb-2 ">
                                    <img src="{{ asset('assets/images/logo.png') }}" alt="Logo" style="width: 180px; height: 54px; border-radius: 10px;">
                                </div> --}}
                                <div class="d-flex align-items-center justify-content-center">
                                    <img src="{{ asset('assets/images/favicon.png') }}" alt="Logo" style="width: 60px; height: 54px;">
                                    <h3 class="fw-bold mb-1" style="color:#506ab2">{{ __('Sign in to ISO Click') }}</h3>
                                </div>
                                <p class="text-muted mb-0 small">{{ __('Access your workspace securely') }}</p>
                            </div>
                            <form method="POST" action="{{ route('tenant.login') }}" class="mb-0">
                                @csrf
                                @if (session('error'))
                                    <div class="alert alert-danger" role="alert">{{ session('error') }}</div>
                                @endif
                                @if (session('success'))
                                    <div class="alert alert-success" role="alert">{{ session('success') }}</div>
                                @endif

                                <div class="form-floating mb-3 position-relative">
                                    <input type="text" class="form-control ps-5" id="company_name" name="company_name"
                                        placeholder="{{ __('Company Name') }}" value="{{ old('company_name') }}" required />
                                    <label for="company_name">{{ __('Company Name') }}</label>
                                    @error('company_name')
                                        <span class="invalid-email text-danger" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                    <div class="form-floating mb-3">
                                        <input type="email" class="form-control" id="email" name="email"
                                            placeholder="{{ __('Email address') }}" value="{{ old('email') }}" required />
                                        <label for="email">{{ __('Email address') }}</label>
                                        @error('email')
                                            <span class="invalid-email text-danger" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="form-floating mb-3">
                                        <input type="password" class="form-control" id="password" name="password"
                                            placeholder="{{ __('Password') }}" required />
                                        <label for="password">{{ __('Password') }}</label>
                                        @error('password')
                                            <span class="invalid-password text-danger" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="d-flex mt-1 justify-content-between align-items-center">
                                        <div class="form-check">
                                            <input class="form-check-input input-primary" type="checkbox" id="agree" name="remember"
                                                {{ old('remember') ? 'checked' : '' }} />
                                            <label class="form-check-label " for="agree">{{ __('Remember me') }}</label>
                                        </div>
                                        <a href="#" class="small">{{ __('Forgot Password?') }}</a>
                                    </div>

                                    <div class="d-grid mt-4">
                                        <button type="submit" class="btn btn-light text-primary fw-bold py-2" style="background-color: #fff; color:#506ab2;">
                                            {{ __('Sign In') }}
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('css-page')
    <style>
        .login-section {
            min-height: 100vh;
            background: linear-gradient(90deg, #f8fafc 0%, #e3eafc 100%);
        }
        .left-column-bg {
            background: #fff;
        }
        .right-column-bg {
            background: #506ab2;
        }
        .card {
            box-shadow: 0 4px 24px rgba(80, 106, 178, 0.15);
        }
        .form-control {
            border-radius: 0.75rem;
            min-height: 48px;
        }
        .form-control:focus {
            border-color: #506ab2;
            box-shadow: 0 0 0 0.2rem rgba(80, 106, 178, 0.15);
        }
        .btn-light.text-primary {
            transition: background 0.2s, color 0.2s;
        }
        .btn-light.text-primary:hover {
            background: #506ab2;
            color: #fff;
        }
        @media (max-width: 991.98px) {
            .left-column-bg, .right-column-bg {
                min-height: 50vh;
            }
        }
        @media (max-width: 767.98px) {
            .login-section > .container-fluid > .row {
                flex-direction: column;
            }
            .left-column-bg, .right-column-bg {
                min-height: 40vh;
                padding: 2rem 1rem;
            }
            .card {
                padding: 2rem 1rem;
            }
        }
        [dir="rtl"] .text-lg-start {
            text-align: right !important;
        }
        [dir="rtl"] .form-check-label {
            margin-right: 0.5rem;
            margin-left: 0;
        }
       
    
    </style>
@endpush
