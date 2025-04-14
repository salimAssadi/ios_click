@extends('tenant::layouts.auth')

@section('content')
<!-- Section: Design Block -->
<section class="min-h-screen flex" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
    <div class="flex-1 px-4 py-5 text-center lg:text-start bg-gray-50">
        <div class="container mx-auto h-full flex flex-col justify-center">
            <div class="lg:w-1/2 mb-5 lg:mb-0">
                <h1 class="text-4xl font-bold mb-5">
                    {{ __('Welcome to ISO Tracker') }} <br />
                    <span class="text-indigo-600">{{ __('Document Management System') }}</span>
                </h1>
                <p class="text-gray-600 mb-8">
                    {{ __('Streamline your ISO documentation') }}
                </p>

                <!-- Features Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-8">
                    <div class="p-4 bg-white rounded-lg shadow-sm">
                        <h3 class="font-semibold text-lg mb-2">{{ __('Document Control') }}</h3>
                        <p class="text-gray-600">{{ __('Version Tracking') }}</p>
                    </div>
                    <div class="p-4 bg-white rounded-lg shadow-sm">
                        <h3 class="font-semibold text-lg mb-2">{{ __('Approval Workflow') }}</h3>
                        <p class="text-gray-600">{{ __('Google Docs Integration') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="flex-1 flex items-center justify-center p-8">
        <div class="w-full max-w-md">
            <div class="text-center mb-8">
                <a href="/" class="inline-block">
                    <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
                </a>
                <h2 class="mt-6 text-3xl font-bold text-gray-900">
                    {{ __('Sign In') }}
                </h2>
            </div>

            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('tenant.login') }}" class="bg-white shadow-md rounded-lg p-8">
                @csrf

                <!-- Company Name -->
                <div class="mb-4">
                    <label for="company_name" class="block text-gray-700 text-sm font-bold mb-2">
                        {{ __('Company Name') }}
                    </label>
                    <input id="company_name" type="text" 
                        class="form-input w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200"
                        name="company_name" value="{{ old('company_name') }}" required autofocus />
                    @error('company_name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email Address -->
                <div class="mb-4">
                    <label for="email" class="block text-gray-700 text-sm font-bold mb-2">
                        {{ __('Email Address') }}
                    </label>
                    <input id="email" type="email" 
                        class="form-input w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200"
                        name="email" value="{{ old('email') }}" required />
                    @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div class="mb-6">
                    <label for="password" class="block text-gray-700 text-sm font-bold mb-2">
                        {{ __('Password') }}
                    </label>
                    <input id="password" type="password" 
                        class="form-input w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200"
                        name="password" required />
                    @error('password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="mb-6">
                    <label class="inline-flex items-center">
                        <input type="checkbox" class="form-checkbox rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200" name="remember">
                        <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                    </label>
                </div>

                <div class="flex items-center justify-between mb-6">
                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        {{ __('Sign In') }}
                    </button>

                    @if (Route::has('tenant.password.request'))
                        <a class="text-sm text-indigo-600 hover:text-indigo-900" href="{{ route('tenant.password.request') }}">
                            {{ __('Forgot your password?') }}
                        </a>
                    @endif
                </div>

                <div class="text-center text-sm text-gray-600">
                    {{ __("Don't have an account?") }}
                    <a href="#" class="font-medium text-indigo-600 hover:text-indigo-500">
                        {{ __('Contact your administrator') }}
                    </a>
                </div>
            </form>

            <!-- Language Switcher -->
            <div class="mt-6 text-center">
                <a href="{{ route('tenant.locale', ['locale' => 'en']) }}" class="text-sm text-gray-600 hover:text-gray-900 {{ app()->getLocale() === 'en' ? 'font-bold' : '' }}">English</a>
                <span class="mx-2">|</span>
                <a href="{{ route('tenant.locale', ['locale' => 'ar']) }}" class="text-sm text-gray-600 hover:text-gray-900 {{ app()->getLocale() === 'ar' ? 'font-bold' : '' }}">العربية</a>
            </div>
        </div>
    </div>
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
