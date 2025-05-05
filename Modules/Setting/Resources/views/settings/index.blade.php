@extends('tenant::layouts.app')
@section('page-title')
    {{ __('System Settings') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('tenant.dashboard') }}">{{ __('Dashboard') }}</a></li>
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
                        <div class="col-12 mb-4">
                            <!-- Horizontal Tabs at the top -->
                            <ul class="nav nav-tabs mb-3" id="myTab" role="tablist">
                                <li class="nav-item d-none">
                                    <a class="nav-link {{ empty($activeTab) || $activeTab == 'user_profile_settings' ? 'active' : '' }}"
                                        id="profile-tab-1" data-bs-toggle="tab" href="#user_profile_settings"
                                        role="tab" aria-selected="true">
                                        <i class="ti ti-user-check me-2"></i>{{ __('User Profile') }}
                                    </a>
                                </li>
                                <li class="nav-item d-none">
                                    <a class="nav-link {{ !empty($activeTab) && $activeTab == 'password_settings' ? 'active' : '' }}"
                                        id="profile-tab-2" data-bs-toggle="tab" href="#password_settings" role="tab"
                                        aria-selected="false">
                                        <i class="ti ti-key me-2"></i>{{ __('Password') }}
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ !empty($activeTab) && $activeTab == 'general_settings' ? 'active' : '' }}"
                                        id="profile-tab-3" data-bs-toggle="tab" href="#general_settings" role="tab"
                                        aria-selected="false">
                                        <i class="ti ti-settings me-2"></i>{{ __('System Settings') }}
                                    </a>
                                </li>
                                @if (!Gate::check('manage company settings'))
                                <li class="nav-item">
                                    <a class="nav-link {{ !empty($activeTab) && $activeTab == 'company_settings' ? 'active' : '' }}"
                                        id="profile-tab-4" data-bs-toggle="tab" href="#company_settings" role="tab"
                                        aria-selected="false">
                                        <i class="ti ti-building me-2"></i>{{ __('Company') }}
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ !empty($activeTab) && $activeTab == 'email_SMTP_settings' ? 'active' : '' }}"
                                        id="profile-tab-5" data-bs-toggle="tab" href="#email_SMTP_settings"
                                        role="tab" aria-selected="false">
                                        <i class="ti ti-mail me-2"></i>{{ __('Email') }}
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ !empty($activeTab) && $activeTab == 'signature_settings' ? 'active' : '' }}"
                                        id="profile-tab-6" data-bs-toggle="tab" href="#signature_settings"
                                        role="tab" aria-selected="false">
                                        <i class="ti ti-signature me-2"></i>{{ __('Signature') }}
                                    </a>
                                </li>
                                @endif
                          
                            </ul>
                        </div>
                        <div class="col-12">
                            <div class="tab-content">
                                <div class="tab-pane d-none {{ empty($activeTab) || $activeTab == 'user_profile_settings' ? ' active show ' : ''  }}"
                                    id="user_profile_settings" role="tabpanel" aria-labelledby="user_profile_settings">
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
                                <div class="tab-pane d-none {{ !empty($activeTab) && $activeTab == 'password_settings' ? ' active show ' : '' }}"
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
                                    {{ Form::model($settings, ['route' => ['tenant.setting.general'], 'method' => 'post', 'enctype' => 'multipart/form-data']) }}
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
                                                <a href="{{ route('tenant.setting.file', $settings['company_logo']) }}"
                                                    target="_blank"><i class="ti ti-eye ms-2 f-15"></i></a>
                                                {{ Form::file('company_logo', ['class' => 'form-control']) }}
                                                @if(!empty($settings['company_logo']))
                                                    <img src="{{ route('tenant.setting.file', $settings['company_logo']) }}" class="img-responsive mt-2" width="150">
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                {{ Form::label('favicon', __('Favicon'), ['class' => 'form-label']) }}
                                                <a href="{{ route('tenant.setting.file', $settings['company_favicon']) }}"
                                                    target="_blank"><i class="ti ti-eye ms-2 f-15"></i></a>
                                                {{ Form::file('company_favicon', ['class' => 'form-control']) }}
                                                @if(!empty($settings['company_favicon']))
                                                    <img src="{{ route('tenant.setting.file', $settings['company_favicon']) }}" class="img-responsive mt-2" width="150">
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                {{ Form::label('light_logo', __('Light Logo'), ['class' => 'form-label']) }}
                                                <a href="{{ route('tenant.setting.file', $settings['light_logo']) }}"
                                                    target="_blank"><i class="ti ti-eye ms-2 f-15"></i></a>
                                                {{ Form::file('light_logo', ['class' => 'form-control']) }}
                                                @if(!empty($settings['light_logo']))
                                                    <img src="{{ route('tenant.setting.file', $settings['light_logo']) }}" class="img-responsive mt-2" width="150">
                                                @endif
                                            </div>
                                        </div>
                                      
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            {{ Form::label('timezone', __('Timezone'), ['class' => 'form-label text-dark']) }}
                                            <select type="text" name="timezone" class="form-control basic-select" id="timezone">
                                                <option value="">{{ __('Select Timezone') }}</option>
                                                @foreach ($timezones as $k => $timezone)
                                                    <option value="{{ $k }}" {{ $settings['timezone'] == $k ? 'selected' : '' }}>
                                                        {{ $timezone }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group col-md-4">
                                            {{ Form::label('default_language', __('Default Language'), ['class' => 'form-label text-dark']) }}
                                            <select name="default_language" id="default_language" class="form-control basic-select">
                                                @foreach(\App\Models\Custom::languages() as $language)
                                                    <option value="{{$language}}" @if($settings['default_language'] == $language) selected @endif>
                                                        {{Str::upper($language)}}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                       
                                        
                                        <div class="form-group col-md-4 mt-3">
                                            {{ Form::label('company_symbol', __('Company Symbol'), ['class' => 'form-label']) }}
                                            {{ Form::text('company_symbol', $settings['company_symbol'], ['class' => 'form-control', 'placeholder' => __('Enter company symbol')]) }}
                                        </div>
                                        
                                        <div class="form-group col-md-4 mt-3">
                                            {{ Form::label('document_number_prefix', __('Document Number Prefix'), ['class' => 'form-label']) }}
                                            {{ Form::text('document_number_prefix', isset($settings['document_number_prefix']) ? $settings['document_number_prefix'] : 'DOC-', ['class' => 'form-control', 'placeholder' => __('Enter document number prefix')]) }}
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
                                <div class="tab-pane {{ !empty($activeTab) && $activeTab == 'company_settings' ? ' active show ' : '' }}"
                                    id="company_settings" role="tabpanel" aria-labelledby="company_settings">
                                    {{ Form::model($settings, ['route' => ['tenant.setting.company'], 'method' => 'post', 'enctype' => 'multipart/form-data']) }}
                                    
                                    <h5 class="mb-3">{{ __('Company Profile Information') }}</h5>
                                    <div class="row">
                                       
                                        {{-- B. Company Name (Arabic) --}}
                                        <div class="form-group col-md-6">
                                            {{ Form::label('company_name_ar', 'Company Name (Arabic)', ['class' => 'form-label']) }}
                                            {{ Form::text('company_name_ar', $settings['company_name_ar'] ?? '', ['class' => 'form-control']) }}
                                        </div>

                                        {{-- C. Company Name (English) --}}
                                        <div class="form-group col-md-6">
                                            {{ Form::label('company_name_en', 'Company Name (English)', ['class' => 'form-label']) }}
                                            {{ Form::text('company_name_en', $settings['company_name_en'] ?? '', ['class' => 'form-control']) }}
                                        </div>

                                        {{-- D. Company Logo --}}
                                        <div class="form-group col-md-6">
                                            {{ Form::label('CountOfUsers', 'Count Of Users', ['class' => 'form-label']) }}
                                            {{ Form::text('CountOfUsers', $settings['CountOfUsers'] ?? '', ['class' => 'form-control' , 'readonly' => true]) }}
                                        </div>

                                        {{-- E. Created At --}}
                                        <div class="form-group col-md-6">
                                            {{ Form::label('created_at', 'Created At', ['class' => 'form-label']) }}
                                            {{ Form::date('created_at', $settings['created_at'] ?? \Carbon\Carbon::now(), ['class' => 'form-control' , 'readonly' => true] ) }}
                                        </div>

                                        {{-- F. Default Country --}}
                                        <div class="form-group col-md-6">
                                            {{ Form::label('is_default_country', 'Is Default Country?', ['class' => 'form-label']) }}
                                            {{ Form::select('is_default_country', ['1' => 'Yes', '0' => 'No'], $settings['is_default_country'] ?? 0, ['class' => 'form-control']) }}
                                        </div>

                                        <div class="form-group col-md-6">
                                            {{ Form::label('company_email', 'Email', ['class' => 'form-label']) }}
                                            {{ Form::text('company_email', $settings['company_email'] ?? '', ['class' => 'form-control']) }}
                                        </div>
                                        <div class="form-group col-md-6">
                                            {{ Form::label('company_phone', 'Phone', ['class' => 'form-label']) }}
                                            {{ Form::text('company_phone', $settings['company_phone'] ?? '', ['class' => 'form-control']) }}
                                        </div>

                                       

                                      

                                        <div class="form-group col-md-6">
                                            {{ Form::label('company_website', 'Website', ['class' => 'form-label']) }}
                                            {{ Form::text('company_website', $settings['company_website'] ?? '', ['class' => 'form-control']) }}
                                        </div>

                                        {{-- Optional: Address and Website if needed --}}
                                        <div class="form-group col-md-12">
                                            {{ Form::label('company_address', 'Address', ['class' => 'form-label']) }}
                                            {{ Form::textarea('company_address', $settings['company_address'] ?? '', ['class' => 'form-control', 'rows' => '2']) }}
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

                                <div class="tab-pane {{ !empty($activeTab) && $activeTab == 'signature_settings' ? ' active show ' : '' }}"
                                    id="signature_settings" role="tabpanel" aria-labelledby="signature_settings">
                                    {{ Form::model($settings, ['route' => ['tenant.setting.signature'], 'method' => 'post', 'enctype' => 'multipart/form-data']) }}
                                    <div class="row gap-2">
                                        <!-- Company Signature -->
                                    <div class="col-md-3 mb-4">
                                        <div class="card shadow-sm border-0 h-100">
                                            <div class="card-body text-center">
                                                <h6 class="card-title">{{ __('Company Signature') }}</h6>

                                                <label for="company_signature" class="d-block">
                                                    <div class="btn btn-outline-secondary w-100">
                                                        <i class="fas fa-upload me-2"></i>{{ __('Choose File') }}
                                                    </div>
                                                    <input type="file" name="company_signature" id="company_signature" class="d-none file" data-filename="company_signature">
                                                </label>

                                                @if(!empty($settings['company_signature']))
                                                    <div class="mt-3">
                                                        <img src="{{ route('tenant.setting.file', $settings['company_signature']) }}" class="" style="max-width: 200px;">
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Company Stamp -->
                                    <div class="col-md-3 mb-4">
                                        <div class="card shadow-sm border-0 h-100">
                                            <div class="card-body text-center">
                                                <h6 class="card-title">{{ __('Company Stamp') }}</h6>

                                                <label for="company_stamp" class="d-block">
                                                    <div class="btn btn-outline-secondary w-100">
                                                        <i class="fas fa-upload me-2"></i>{{ __('Choose File') }}
                                                    </div>
                                                    <input type="file" name="company_stamp" id="company_stamp" class="d-none file" data-filename="company_stamp">
                                                </label>

                                                @if(!empty($settings['company_stamp']))
                                                    <div class="mt-3">
                                                        <img src="{{ route('tenant.setting.file', $settings['company_stamp']) }}" class="" style="max-width: 200px;">
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-5 mb-4 shadow-sm ">
                                        <div class="form-group col-md-12 mt-3">
                                            <div class="form-check form-switch custom-switch-v1">
                                                <input type="checkbox" name="signature_pad_enable" id="signature_pad_enable" class="form-check-input" 
                                                    {{ isset($settings['signature_pad_enable']) && $settings['signature_pad_enable'] == 'on' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="signature_pad_enable">{{ __('Enable Signature Pad') }}</label>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group col-md-12 signature-pad-container {{ isset($settings['signature_pad_enable']) && $settings['signature_pad_enable'] == 'on' ? '' : 'd-none' }}">
                                            {{ Form::label('signature_pad', __('Create Signature'), ['class' => 'form-label']) }}
                                            <div class="signature-pad border rounded mb-2">
                                                <canvas id="signature-pad" style="width: 100%; height: 200px; border: 1px solid #ccc; background-color: #fff;"></canvas>
                                            </div>
                                            <div class="d-flex mt-1">
                                                <button type="button" class="btn btn-sm btn-danger signature-clear-btn me-2">{{ __('Clear') }}</button>
                                                <button type="button" class="btn btn-sm btn-success signature-save-btn">{{ __('Save Signature') }}</button>
                                                <input type="hidden" name="signature_pad_data" id="signature_pad_data">
                                            </div>
                                            @if(!empty($settings['signature_pad_data']))
                                                <div class="mt-3">
                                                    <strong>{{ __('Current Signature:') }}</strong><br>
                                                    <img src="{{ $settings['signature_pad_data'] }}" class="img-responsive mt-2" width="200">
                                                </div>
                                            @endif
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

                                <div class="tab-pane {{ !empty($activeTab) && $activeTab == 'email_SMTP_settings' ? ' active show ' : '' }}"
                                    id="email_SMTP_settings" role="tabpanel" aria-labelledby="email_SMTP_settings">
                                    {{ Form::model($settings, ['route' => ['tenant.setting.smtp'], 'method' => 'post']) }}
                                    <div class="row">
                                        <div class="form-group col-md-6">
                                            {{ Form::label('sender_name', __('Sender Name'), ['class' => 'form-label']) }}
                                            {{ Form::text('sender_name', $settings['FROM_NAME'], ['class' => 'form-control', 'placeholder' => __('Enter sender name')]) }}
                                        </div>
                                        <div class="form-group col-md-6">
                                            {{ Form::label('sender_email', __('Sender Email'), ['class' => 'form-label']) }}
                                            {{ Form::text('sender_email', $settings['FROM_EMAIL'], ['class' => 'form-control', 'placeholder' => __('Enter sender email')]) }}
                                        </div>
                                        <div class="form-group col-md-6">
                                            {{ Form::label('server_driver', __('SMTP Driver'), ['class' => 'form-label']) }}
                                            {{ Form::text('server_driver', $settings['SERVER_DRIVER'], ['class' => 'form-control', 'placeholder' => __('Enter smtp driver')]) }}
                                        </div>
                                        <div class="form-group col-md-6">
                                            {{ Form::label('server_host', __('SMTP Host'), ['class' => 'form-label']) }}
                                            {{ Form::text('server_host', $settings['SERVER_HOST'], ['class' => 'form-control ', 'placeholder' => __('Enter smtp host')]) }}
                                        </div>
                                        <div class="form-group col-md-6">
                                            {{ Form::label('server_username', __('SMTP Username'), ['class' => 'form-label']) }}
                                            {{ Form::text('server_username', $settings['SERVER_USERNAME'], ['class' => 'form-control', 'placeholder' => __('Enter smtp username')]) }}
                                        </div>
                                        <div class="form-group col-md-6">
                                            {{ Form::label('server_password', __('SMTP Password'), ['class' => 'form-label']) }}
                                            {{ Form::text('server_password', $settings['SERVER_PASSWORD'], ['class' => 'form-control', 'placeholder' => __('Enter smtp password')]) }}
                                        </div>
                                        <div class="form-group col-md-6">
                                            {{ Form::label('server_encryption', __('SMTP Encryption'), ['class' => 'form-label']) }}
                                            {{ Form::text('server_encryption', $settings['SERVER_ENCRYPTION'], ['class' => 'form-control', 'placeholder' => __('Enter smtp encryption')]) }}
                                        </div>
                                        <div class="form-group col-md-6">
                                            {{ Form::label('server_port', __('SMTP Port'), ['class' => 'form-label']) }}
                                            {{ Form::text('server_port', $settings['SERVER_PORT'], ['class' => 'form-control', 'placeholder' => __('Enter smtp port')]) }}
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-6"></div>
                                        <div class="col-6  text-end">
                                            <a href="#" data-size="md"
                                                data-url="{{ route('tenant.setting.smtp.test') }}"
                                                data-title="{{ __('Add Email') }}"
                                                class='btn btn-secondary btn-rounded customModal me-1'>
                                                {{ __('Test Mail') }} </a>
                                            {{ Form::submit(__('Save'), ['class' => 'btn btn-secondary btn-rounded']) }}
                                        </div>
                                    </div>
                                    {{ Form::close() }}
                                </div>
                          
                          
                                
                                <!-- Add other tab content panes here -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script-page')
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialize signature pad
        var signaturePad = null;
        
        function initSignaturePad() {
            if (document.getElementById('signature-pad')) {
                const canvas = document.getElementById('signature-pad');
                signaturePad = new SignaturePad(canvas, {
                    backgroundColor: 'rgb(255, 255, 255)'
                });
                
                // Adjust canvas size
                function resizeCanvas() {
                    const ratio = Math.max(window.devicePixelRatio || 1, 1);
                    canvas.width = canvas.offsetWidth * ratio;
                    canvas.height = canvas.offsetHeight * ratio;
                    canvas.getContext("2d").scale(ratio, ratio);
                    signaturePad.clear(); // Otherwise isEmpty() might return incorrect value
                }
                
                window.addEventListener("resize", resizeCanvas);
                resizeCanvas();
            }
        }
        
        // Initialize on page load
        initSignaturePad();
        
        // Toggle signature pad container
        $('#signature_pad_enable').on('change', function() {
            if ($(this).is(':checked')) {
                $('.signature-pad-container').removeClass('d-none');
                // Re-initialize signature pad when it becomes visible
                setTimeout(function() {
                    initSignaturePad();
                }, 100);
            } else {
                $('.signature-pad-container').addClass('d-none');
            }
        });
        
        // Clear signature pad
        $('.signature-clear-btn').on('click', function() {
            if (signaturePad) {
                signaturePad.clear();
            }
        });
        
        // Save signature
        $('.signature-save-btn').on('click', function() {
            if (signaturePad && !signaturePad.isEmpty()) {
                var signatureData = signaturePad.toDataURL();
                $('#signature_pad_data').val(signatureData);
                
                // Show preview
                if ($('.signature-pad-container .mt-3').length) {
                    $('.signature-pad-container .mt-3 img').attr('src', signatureData);
                } else {
                    $('.signature-pad-container').append(
                        '<div class="mt-3"><strong>{{ __("Current Signature:") }}</strong><br>' +
                        '<img src="' + signatureData + '" class="img-responsive mt-2" width="500"></div>'
                    );
                }
                
                toastr.success('{{ __("Signature saved successfully") }}');
            } else {
                toastr.error('{{ __("Please provide signature first") }}');
            }
        });
        
        // Handle file uploads
        $('.file').change(function() {
            var file = $(this)[0].files[0];
            var fileType = file.type;
            var validImageTypes = ['image/jpeg', 'image/jpg', 'image/png'];
            
            if ($.inArray(fileType, validImageTypes) < 0) {
                toastr.error('{{ __("Please upload a valid image file (JPEG, JPG, PNG)") }}');
                $(this).val('');
                return false;
            }
            
            if (file.size > 2097152) { // 2MB in bytes
                toastr.error('{{ __("File size should not exceed 2MB") }}');
                $(this).val('');
                return false;
            }
        });
    });
</script>
@endpush
