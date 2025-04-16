@extends('tenant::layouts.auth')

@section('content')
    <section class="" dir="{{ app()->getLocale() === 'arabic' ? 'rtl' : 'ltr' }}">
        <!-- Jumbotron -->
        <div class="px-4 py-5 px-md-5 " >
            <div class="container">
                <div class="row gx-lg-5 align-items-center">
                    <div class="col-lg-6 mb-5 mb-lg-0">
                        <h5 class="my-5 display-3 fw-bold ls-tight">
                            {{ __('Welcome to ISO Click') }} <br />
                            <span class="text-primary">{{ __('Document Management System') }}</span>
                        </h5>
                        <p style="color: hsl(217, 10%, 50.8%)">
                            {{ __('Streamline your ISO documentation') }}
                        </p>
                    </div>

                    <div class="col-lg-6 mb-5 mb-lg-0">
                        <div class="card">
                            <div class="card-body py-5 px-md-5">
                                <form method="POST" action="{{ route('tenant.login') }}" class="mb-4">
                                    @csrf
                                    <!-- 2 column grid layout with text inputs for the first and last names -->
                                    @if (session('error'))
                                        <div class="alert alert-danger" role="alert">{{ session('error') }}</div>
                                    @endif
                                    @if (session('success'))
                                        <div class="alert alert-success" role="alert">{{ session('success') }}</div>
                                    @endif

                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="company_name" name="company_name"
                                            placeholder="{{ __('Company Name') }}" value="{{ old('company_name') }}" />
                                        <label for="company_name">{{ __('Company Name') }}</label>
                                        @error('company_name')
                                            <span class="invalid-email text-danger" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="form-floating mb-3">
                                        <input type="email" class="form-control" id="email" name="email"
                                            placeholder="{{ __('Email address') }}" value="{{ old('email') }}" />
                                        <label for="email">{{ __('Email address') }}</label>
                                        @error('email')
                                            <span class="invalid-email text-danger" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="form-floating mb-3">
                                        <input type="password" class="form-control" id="password" name="password"
                                            placeholder="{{ __('Password') }}" />
                                        <label for="password">{{ __('Password') }}</label>
                                        @error('password')
                                            <span class="invalid-password text-danger" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="d-flex mt-1 justify-content-between">
                                        <div class="form-check">
                                            <input class="form-check-input input-primary" type="checkbox" id="agree"
                                                {{ old('remember') ? 'checked' : '' }} />
                                            <label class="form-check-label text-muted"
                                                for="agree">{{ __('Remember me') }}</label>
                                        </div>
                                        <a href="" class="text-secondary">{{ __('Forgot Password?') }}</a>
                                    </div>

                                    <div class="d-grid mt-4">
                                        <button type="submit" class="btn btn-secondary p-2">{{ __('Sign In') }}</button>
                                    </div>

                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Jumbotron -->
    </section>
@endsection

@push('css-page')
    <style>
        [dir="rtl"] .lg\:text-start {
            text-align: right;
        }

        [dir="rtl"] .ms-2 {
            margin-right: 0.5rem;
            margin-left: 0;
        }
    </style>
@endpush
