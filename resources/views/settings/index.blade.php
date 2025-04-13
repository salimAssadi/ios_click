@extends('layouts.admin-app')
@section('page-title')
    {{ __('System Settings') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('iso_dic.home') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item" aria-current="page"> {{ __('System Settings') }}</li>
@endsection
@php
    $admin_logo = getSettingsValByName('company_logo');
    $profile = asset(Storage::url('upload/profile'));
    $activeTab = session('tab', 'user_profile_settings');
@endphp
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-4">
                            <ul class="nav flex-column nav-tabs account-tabs mb-3" id="myTab" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link {{ empty($activeTab) || $activeTab == 'user_profile_settings' ? ' active ' : '' }}"
                                            id="profile-tab-1" data-bs-toggle="tab" href="#user_profile_settings"
                                            role="tab" aria-selected="true">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0">
                                                    <i class="ti ti-user-check me-2 f-20"></i>
                                                </div>
                                                <div class="flex-grow-1 ms-2">
                                                    <h5 class="mb-0">{{ __('User Profile') }}</h5>
                                                    <small class="text-muted">{{ __('User Account Profile Settings') }}</small>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link  {{ empty($activeTab) || $activeTab == 'password_settings' ? ' active ' : '' }}"
                                            id="profile-tab-2" data-bs-toggle="tab" href="#password_settings" role="tab"
                                            aria-selected="true">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0">
                                                    <i class="ti ti-key me-2 f-20"></i>
                                                </div>
                                                <div class="flex-grow-1 ms-2">
                                                    <h5 class="mb-0">{{ __('Password') }}</h5>
                                                    <small class="text-muted">{{ __('Password Settings') }}</small>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ empty($activeTab) || $activeTab == 'general_settings' ? ' active ' : '' }}"
                                            id="profile-tab-3" data-bs-toggle="tab" href="#general_settings" role="tab"
                                            aria-selected="true">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0">
                                                    <i class="ti ti-settings me-2 f-20"></i>
                                                </div>
                                                <div class="flex-grow-1 ms-2">
                                                    <h5 class="mb-0">{{ __('General') }}</h5>
                                                    <small class="text-muted">{{ __('General Settings') }}</small>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                {{-- @if (Gate::check('manage company settings'))
                                    <li class="nav-item">
                                        <a class="nav-link {{ empty($activeTab) || $activeTab == 'company_settings' ? ' active ' : '' }}"
                                            id="profile-tab-4" data-bs-toggle="tab" href="#company_settings" role="tab"
                                            aria-selected="true">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0">
                                                    <i class="ti ti-building me-2 f-20"></i>
                                                </div>
                                                <div class="flex-grow-1 ms-2">
                                                    <h5 class="mb-0">{{ __('Company') }}</h5>
                                                    <small class="text-muted">{{ __('Company Settings') }}</small>
                                                </div>
                                            </div>
                                        </a>
                                    </li> --}}
                                    {{-- <li class="nav-item">
                                        <a class="nav-link {{ empty($activeTab) || $activeTab == 'email_SMTP_settings' ? ' active ' : '' }} "
                                            id="profile-tab-5" data-bs-toggle="tab" href="#email_SMTP_settings"
                                            role="tab" aria-selected="true">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0">
                                                    <i class="ti ti-mail me-2 f-20"></i>
                                                </div>
                                                <div class="flex-grow-1 ms-2">
                                                    <h5 class="mb-0">{{ __('Email') }}</h5>
                                                    <small class="text-muted">{{ __('Email SMTP Settings') }}</small>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ empty($activeTab) || $activeTab == 'payment_settings' ? ' active ' : '' }}"
                                            id="profile-tab-6" data-bs-toggle="tab" href="#payment_settings" role="tab"
                                            aria-selected="true">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0">
                                                    <i class="ti ti-credit-card me-2 f-20"></i>
                                                </div>
                                                <div class="flex-grow-1 ms-2">
                                                    <h5 class="mb-0">{{ __('Payment') }}</h5>
                                                    <small class="text-muted">{{ __('Payment Settings') }}</small>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                @if (Gate::check('manage seo settings'))
                                    <li class="nav-item">
                                        <a class="nav-link {{ empty($activeTab) || $activeTab == 'site_SEO_settings' ? ' active ' : '' }} "
                                            id="profile-tab-7" data-bs-toggle="tab" href="#site_SEO_settings" role="tab"
                                            aria-selected="true">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0">
                                                    <i class="ti ti-sitemap me-2 f-20"></i>
                                                </div>
                                                <div class="flex-grow-1 ms-2">
                                                    <h5 class="mb-0">{{ __('Site SEO') }}</h5>
                                                    <small class="text-muted">{{ __('Site SEO Settings') }}</small>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                @endif
                                @if (Gate::check('manage google recaptcha settings'))
                                    <li class="nav-item">
                                        <a class="nav-link {{ empty($activeTab) || $activeTab == 'google_recaptcha_settings' ? ' active ' : '' }} "
                                            id="profile-tab-8" data-bs-toggle="tab" href="#google_recaptcha_settings"
                                            role="tab" aria-selected="true">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0">
                                                    <i class="ti ti-brand-google me-2 f-20"></i>
                                                </div>
                                                <div class="flex-grow-1 ms-2">
                                                    <h5 class="mb-0">{{ __('Google Recaptcha') }}</h5>
                                                    <small
                                                        class="text-muted">{{ __('Google Recaptcha Settings') }}</small>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                @endif
                                @if (Gate::check('manage 2FA settings'))
                                    <li class="nav-item">
                                        <a class="nav-link {{ empty($activeTab) || $activeTab == '2FA' ? ' active ' : '' }} "
                                            id="profile-tab-9" data-bs-toggle="tab" href="#2FA" role="tab"
                                            aria-selected="true">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0">
                                                    <i class="ti ti-barcode me-2 f-20"></i>
                                                </div>
                                                <div class="flex-grow-1 ms-2">
                                                    <h5 class="mb-0">{{ __('2 Factors Authentication') }}</h5>
                                                    <small
                                                        class="text-muted">{{ __('2 Factors Authentication Settings') }}</small>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                @endif --}}
                            </ul>
                        </div>
                        <div class="col-lg-8">
                            <div class="tab-content">
                                    <div class="tab-pane {{ empty($activeTab) || $activeTab == 'user_profile_settings' ? ' active show ' : '' }}"
                                        id="user_profile_settings" role="tabpanel"
                                        aria-labelledby="user_profile_settings">
                                        {{ Form::model($loginUser, ['route' => ['iso_dic.setting.account'], 'method' => 'post', 'enctype' => 'multipart/form-data']) }}
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="flex-shrink-0">
                                                <img src="{{ !empty($users->profile) ? $profile . '/' . $users->profile : $profile . '/avatar.png' }}"
                                                    alt="user-image" class="img-fluid rounded-circle wid-80" />
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    {{ Form::label('name', __('Name'), ['class' => 'form-label']) }}
                                                    {{ Form::text('name', null, ['class' => 'form-control', 'placeholder' => __('Enter your name'), 'required' => 'required']) }}
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    {{ Form::label('email', __('Email Address'), ['class' => 'form-label']) }}
                                                    {{ Form::text('email', null, ['class' => 'form-control', 'placeholder' => __('Enter your email'), 'required' => 'required']) }}
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    {{ Form::label('phone_number', __('Phone Number'), ['class' => 'form-label']) }}
                                                    {{ Form::number('phone_number', null, ['class' => 'form-control', 'placeholder' => __('Enter your Phone Number')]) }}
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    {{ Form::label('profile', __('Profile'), ['class' => 'form-label']) }}
                                                    {{ Form::file('profile', ['class' => 'form-control']) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-6"></div>
                                            <div class="col-6 text-end">
                                                {{ Form::submit(__('Save'), ['class' => 'btn btn-secondary btn-rounded']) }}
                                            </div>
                                        </div>
                                        {{ Form::close() }}
                                    </div>
                                    <div class="tab-pane {{ !empty($activeTab) && $activeTab == 'password_settings' ? ' active show ' : '' }}"
                                        id="password_settings" role="tabpanel" aria-labelledby="password_settings">
                                        {{ Form::model($loginUser, ['route' => ['iso_dic.setting.password'], 'method' => 'post']) }}
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    {{ Form::label('current_password', __('Current Password'), ['class' => 'form-label']) }}
                                                    {{ Form::password('current_password', ['class' => 'form-control', 'placeholder' => __('Enter your current password'), 'required' => 'required']) }}
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    {{ Form::label('new_password', __('New Password'), ['class' => 'form-label']) }}
                                                    {{ Form::password('new_password', ['class' => 'form-control', 'placeholder' => __('Enter your new password'), 'required' => 'required']) }}
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    {{ Form::label('confirm_password', __('Confirm New Password'), ['class' => 'form-label']) }}
                                                    {{ Form::password('confirm_password', ['class' => 'form-control', 'placeholder' => __('Enter your confirm new password'), 'required' => 'required']) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-6"></div>
                                            <div class="col-6 text-end">
                                                {{ Form::submit(__('Save'), ['class' => 'btn btn-secondary btn-rounded']) }}
                                            </div>
                                        </div>
                                        {{ Form::close() }}
                                    </div>
                                    <div class="tab-pane {{ !empty($activeTab) && $activeTab == 'general_settings' ? ' active show ' : '' }}"
                                        id="general_settings" role="tabpanel" aria-labelledby="general_settings">
                                        {{ Form::model($settings, ['route' => ['iso_dic.setting.general'], 'method' => 'post', 'enctype' => 'multipart/form-data']) }}
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    {{ Form::label('application_name', __('Application Name'), ['class' => 'form-label']) }}
                                                    {{ Form::text('application_name', !empty($settings['app_name']) ? $settings['app_name'] : env('APP_NAME'), ['class' => 'form-control', 'placeholder' => __('Enter your application name'), 'required' => 'required']) }}
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    {{ Form::label('logo', __('Logo'), ['class' => 'form-label']) }}
                                                    <a href="{{ asset(Storage::url('upload/logo/')) . '/' . (isset($admin_logo) && !empty($admin_logo) ? $admin_logo : 'logo.png') }}"
                                                        target="_blank"><i class="ti ti-eye ms-2 f-15"></i></a>
                                                    {{ Form::file('logo', ['class' => 'form-control']) }}
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    {{ Form::label('favicon', __('Favicon'), ['class' => 'form-label']) }}
                                                    <a href="{{ asset(Storage::url('upload/logo')) . '/' . $settings['company_favicon'] }}"
                                                        target="_blank"><i class="ti ti-eye ms-2 f-15"></i></a>
                                                    {{ Form::file('favicon', ['class' => 'form-control']) }}
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    {{ Form::label('light_logo', __('Light Logo'), ['class' => 'form-label']) }}
                                                    <a href="{{ asset(Storage::url('upload/logo')) . '/' . $settings['light_logo'] }}"
                                                        target="_blank"><i class="ti ti-eye ms-2 f-15"></i></a>
                                                    {{ Form::file('light_logo', ['class' => 'form-control']) }}
                                                </div>
                                            </div>
                                            @if (\Auth::user()->type == 'super admin')
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        {{ Form::label('landing_logo', __('Landing Page Logo'), ['class' => 'form-label']) }}
                                                        <a href="{{ asset(Storage::url('upload/logo/landing_logo.png')) }}"
                                                            target="_blank"><i class="ti ti-eye ms-2 f-15"></i></a>
                                                        {{ Form::file('landing_logo', ['class' => 'form-control']) }}
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        {{ Form::label('landing_logo', __('Owner Email Verification'), ['class' => 'form-label']) }}
                                                        <div class="flex-shrink-0">
                                                            <div class="form-check form-switch">
                                                                <input class="form-check-input" type="checkbox"
                                                                    id="owner_email_verification"
                                                                    name="owner_email_verification"
                                                                    {{ $settings['owner_email_verification'] == 'on' ? 'checked' : '' }}>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        {{ Form::label('landing_logo', __('Registration Page'), ['class' => 'form-label']) }}
                                                        <div class="flex-shrink-0">
                                                            <div class="form-check form-switch">
                                                                <input class="form-check-input" type="checkbox"
                                                                    id="register_page" name="register_page"
                                                                    {{ $settings['register_page'] == 'on' ? 'checked' : '' }}>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        {{ Form::label('landing_logo', __('Landing Page'), ['class' => 'form-label']) }}
                                                        <div class="flex-shrink-0">
                                                            <div class="form-check form-switch">
                                                                <input class="form-check-input" type="checkbox"
                                                                    id="landing_page" name="landing_page"
                                                                    {{ $settings['landing_page'] == 'on' ? 'checked' : '' }}>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        {{ Form::label('pricing_feature', __('Pricing Feature'), ['class' => 'form-label']) }}
                                                        <div class="flex-shrink-0">
                                                            <div class="form-check form-switch">
                                                                <input type="hidden" name="pricing_feature"
                                                                    value="off">
                                                                <input class="form-check-input" type="checkbox"
                                                                    id="pricing_feature" name="pricing_feature"
                                                                    value="on"
                                                                    {{ $settings['pricing_feature'] == 'on' ? 'checked' : '' }}>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-6"></div>
                                            <div class="col-6 text-end">
                                                {{ Form::submit(__('Save'), ['class' => 'btn btn-secondary btn-rounded']) }}
                                            </div>
                                        </div>
                                        {{ Form::close() }}
                                    </div>
                               
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
