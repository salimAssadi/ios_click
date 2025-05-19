@extends('tenant::layouts.app')

@section('page-title')
    {{ __('Create Procedure') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="">{{ __('Dashboard') }}</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('tenant.document.procedures.private') }}">{{ __('Private Procedures') }}</a>
    </li>
    <li class="breadcrumb-item" aria-current="page">
        {{ __('Create Procedure')}}
    </li>
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>{{ __('Create New Procedure') }}</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning">
                        {{ __('This action will be applied to the system you have specified') }}
                        <span class="text-danger">{{ getIsoSystem(currentISOSystem())->name }}</span>
                    </div>
                    {{ Form::open(['route' => 'tenant.document.procedures.store', 'method' => 'post', 'files' => true, 'id' => 'procedure-form']) }}
                    <div class="form-group col-md-12">
                        {{ Form::label('category_id', __('Category'), ['class' => 'form-label']) }}
                        {{ Form::select('category_id', $categories, old('category_id'), ['class' => 'form-control hidesearch'  ]) }}
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6">
                            {{ Form::label('procedure_name_ar', __('Procedure Name (arabic)') . ' <span class="text-danger">*</span>', ['class' => 'form-label'], false) }}
                            {{ Form::text('procedure_name_ar', old('procedure_name_ar',''), ['class' => 'form-control', 'placeholder' => __('Enter Procedure Name (arabic)')]) }}
                        </div>
                        <div class="form-group col-md-6">
                            {{ Form::label('procedure_code', __('Procedure Code') . ' <span class="text-danger">*</span>', ['class' => 'form-label'], false) }}
                            {{ Form::text('procedure_code', old('procedure_code', $procedureCodeing), ['class' => 'form-control', 'placeholder' => __('Enter Procedure Code')]) }}
                        </div>
                        <div class="form-group col-md-6">
                            {{ Form::label('procedure_name_en', __('Procedure Name (english)') . ' <span class="text-danger">*</span>', ['class' => 'form-label'], false) }}
                            {{ Form::text('procedure_name_en', old('procedure_name_en',''), ['class' => 'form-control', 'placeholder' => __('Enter Procedure Name (english)')]) }}
                        </div>
                        <div class="form-group col-md-6">
                            {{ Form::label('procedure_description_ar', __('Procedure Description (arabic)'), ['class' => 'form-label']) }}
                            {{ Form::textarea('procedure_description_ar', old('procedure_description_ar',''), ['class' => 'form-control', 'placeholder' => __('Enter Procedure Description (arabic)'), 'rows' => 2]) }}
                        </div>

                        <div class="form-group col-md-6">
                            {{ Form::label('procedure_description_en', __('Procedure Description (english)'), ['class' => 'form-label']) }}
                            {{ Form::textarea('procedure_description_en', old('procedure_description_en',''), ['class' => 'form-control', 'placeholder' => __('Enter Procedure Description (english)'), 'rows' => 2]) }}
                        </div>

                        <div class="form-group col-md-6 mt-3">
                            {{ Form::label('prepared_by', __('Preparer Name') . ' <span class="text-danger">*</span>', ['class' => 'form-label'], false) }}
                            {{ Form::select('prepared_by', $users ?? [], old('prepared_by'), ['class' => 'form-control hidesearch']) }}
                        </div>
                        
                        <div class="form-group col-md-6 mt-3">
                            {{ Form::label('approved_by', __('Approver Name') . ' <span class="text-danger">*</span>', ['class' => 'form-label'], false) }}
                            {{ Form::select('approved_by', $users ?? [], old('approved_by'), ['class' => 'form-control hidesearch']) }}
                        </div>
                        
                        <div class="form-group col-md-6 mt-3">
                            {{ Form::label('reviewers', __('Reviewer Name'), ['class' => 'form-label']) }}
                            {{ Form::select('reviewers[]', $users ?? [], old('reviewers'), ['class' => 'form-control hidesearch', 'multiple' => 'multiple']) }}
                            <small class="text-muted">{{ __('You can select multiple reviewers') }}</small>
                        </div>
    
                        <div class="form-group col-md-6 mt-3">
                            {{ Form::label('issue_date', __('Issue Date') . ' <span class="text-danger">*</span>', ['class' => 'form-label'], false) }}
                            <div class="input-group date">
                                {{ Form::text('issue_date', old('issue_date'), ['class' => 'form-control' . ($errors->has('issue_date') ? ' is-invalid' : ''), 'id' => 'issue_date', 'placeholder' => 'YYYY-MM-DD', 'autocomplete' => 'off']) }}
                                <span class="input-group-text bg-primary">
                                    <i class="ti ti-calendar text-white"></i>
                                </span>
                            </div>
                            
                        </div>
    
                        <div class="form-group col-md-6 mt-3">
                            {{ Form::label('expiry_date', __('Expiry Date') . ' <span class="text-danger">*</span>', ['class' => 'form-label'], false) }}
                            <span class="text-red-200">{{ __('Default value is 3 years from issue date') }}</span>
                            <div class="input-group date">
                                {{ Form::text('expiry_date', old('expiry_date'), ['class' => 'form-control' . ($errors->has('expiry_date') ? ' is-invalid' : ''), 'id' => 'expiry_date', 'placeholder' => 'YYYY-MM-DD', 'autocomplete' => 'off']) }}
                                <span class="input-group-text bg-primary">
                                    <i class="ti ti-calendar text-white"></i>
                                </span>
                            </div>
                        </div>

                        <div class="form-group col-md-6 mt-3">
                            {{ Form::label('reminder_days', __('Reminder Before Expiry') . ' <span class="text-danger">*</span>', ['class' => 'form-label'], false) }}
                            <div class="input-group">
                                {{ Form::number('reminder_days', old('reminder_days'), ['class' => 'form-control' . ($errors->has('reminder_days') ? ' is-invalid' : ''), 'min' => '1', 'max' => '365', 'id' => 'reminder_days']) }}
                                <span class="input-group-text bg-info">
                                    <i class="ti ti-bell text-white"></i>
                                </span>
                            </div>
                            <small class="text-muted">{{ __('Number of days before expiry to send reminder') }}</small>
                        </div>

                    </div>



                   
                    <div class="form-group col-md-6" style="display: none;">
                        {{ Form::label('is_optional', __('Required Procedure'), ['class' => 'form-label d-block']) }}
                        <div class="form-check form-check-inline">
                            {{ Form::radio('is_optional', 1, true, ['class' => 'form-check-input', 'id' => 'is_optional']) }}
                            {{ Form::label('Optional', __('Optional'), ['class' => 'form-check-label']) }}
                        </div>
                        <div class="form-check form-check-inline">
                            {{ Form::radio('is_optional', 0, false, ['class' => 'form-check-input', 'id' => 'required']) }}
                            {{ Form::label('Required', __('Required'), ['class' => 'form-check-label']) }}
                        </div>
                    </div>

                   

                   



                    <div class="form-group col-md-6">
                        {{ Form::label('status', __('Status'), ['class' => 'form-label d-block']) }}
                        <div class="form-check form-check-inline">
                            {{ Form::radio('status', 1, old('status', 1), ['class' => 'form-check-input', 'id' => 'status_active']) }}
                            {{ Form::label('status_active', __('Active'), ['class' => 'form-check-label']) }}
                        </div>
                        <div class="form-check form-check-inline">
                            {{ Form::radio('status', 0, old('status', 0), ['class' => 'form-check-input', 'id' => 'status_inactive']) }}
                            {{ Form::label('status_inactive', __('Inactive'), ['class' => 'form-check-label']) }}
                        </div>
                    </div>
                    
                    <div class="form-group col-md-12" style="display: none;">   
                        <div class="form-check form-check-inline">
                            {!! Form::checkbox('enable_upload_file', 1, null, ['class' => 'form-check-input', 'id' => 'enable_upload_file']) !!}
                            {!! Form::label('enable_upload_file', __('Enable Upload File'), ['class' => 'form-check-label']) !!}
                        </div>

                        <div class="form-check form-check-inline" style="display: none;">
                            {!! Form::checkbox('enable_editor', 1, null, ['class' => 'form-check-input', 'id' => 'enable_editor']) !!}
                            {!! Form::label('enable_editor', __('Enable Editor'), ['class' => 'form-check-label']) !!}
                        </div>

                        <div class="form-check form-check-inline" style="display: none;">
                            {!! Form::checkbox('has_menual_config', 1, 1, ['class' => 'form-check-input', 'id' => 'has_menual_config']) !!}
                            {!! Form::label('has_menual_config', __('Has Manual Config'), ['class' => 'form-check-label']) !!}
                        </div>
                    </div>

                   

                    <div class="form-group col-md-12 text-end">
                        <a href="{{ route('iso_dic.procedures.index') }}"
                            class="btn btn-secondary">{{ __('Cancel') }}</a>
                        {{ Form::submit(__('Create'), ['class' => 'btn btn-primary']) }}
                    </div>
                </div>
                {{ Form::close() }}

                {{-- Config section that will be loaded via AJAX --}}
                <div id="procedure-config-container" style="display: none;">
                    <div class="text-center py-3" id="config-loading">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">{{ __('Loading configuration options...') }}</p>
                    </div>
                    <div class="card-body">
                        <div id="config-content">

                        </div>
                        <div class="text-end mt-3">
                            <button type="button" class="btn btn-secondary" onclick="window.history.back()">{{ __('Cancel') }}</button>
                            <button type="button" id="save-configuration" class="btn btn-primary">{{ __('Save') }}</button>
                        </div>
                    </div>
                    
                </div>
            </div>
            
        </div>
    </div>
    </div>
@endsection

@push('script-page')
    <script>
        $(document).ready(function() {
           
            // Handle AJAX form submission
            $('#procedure-form').on('submit', function(e) {
                e.preventDefault();
                
                // Show submit button loader
                const submitBtn = $(this).find('input[type="submit"]');
                const originalBtnText = submitBtn.val();
                submitBtn.prop('disabled', true).val('{{ __('Processing...') }}');
                
                // Get form data
                const formData = new FormData(this);
                
                // Make AJAX request
                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            // Show success message
                            notifier.show('Success!', response.message, 'success',successImg, 4000);

                            // Show config container
                            $('#procedure-config-container').show();
                            
                            // Add procedure ID as data attribute for future API calls
                            $('#procedure-config-container').attr('data-procedure-id', response.procedure_id);
                            
                            // Hide loading indicator and show config content
                            $('#config-loading').hide();
                            $('#config-content').html(response.config_html);
                            
                            // Hide the form
                            $('#procedure-form').hide();
                            
                            // Initialize any scripts needed for the config form
                            initConfigScripts();
                        } else {
                            // Show error message
                            notifier.show('Error!', response.message || 'An error occurred. Please try again.', 'error',errorImg, 4000);
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'An error occurred. Please try again.';
                        
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            const errors = xhr.responseJSON.errors;
                            let errorList = '';
                            
                            // Compile error messages
                            for (const field in errors) {
                                errorList += `<li>${errors[field][0]}</li>`;
                            }
                            
                            errorMessage = `<ul>${errorList}</ul>`;
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        
                        // Show error message
                        notifier.show('Error!', errorMessage, 'error',errorImg, 4000);
                        
                        // Append error message above the form

                    },
                    complete: function() {
                        // Reset submit button
                        submitBtn.prop('disabled', false).val(originalBtnText);
                    }
                });
            });
            
            // Function to initialize scripts for the config form
            function initConfigScripts() {
               
                $('#save-configuration').on('click', function(event) {
                    event.preventDefault();
                    
                    sendAllFormData();
                });
            }
            
            // Function to collect and send all form data
            function sendAllFormData() {
                // جمع البيانات من جميع الحقول في النموذج
                var formData = new FormData();
                
                // جمع البيانات من النموذج الرئيسي
                var procedureId = $('#procedure-config-container').attr('data-procedure-id');
                // إضافة بيانات الإجراء المجمعة من الواجهة
                var procedureSetupData = {};
                
                // استدعاء دالة collectAllFormData() لجمع البيانات
                if (typeof collectAllFormData === 'function') {
                    procedureSetupData = collectAllFormData();
                } else {
                    console.error('collectAllFormData الدالة غير موجودة');
                }
                console.log(procedureSetupData);
                // إضافة البيانات إلى FormData
                formData.append('procedure_id', procedureId);
                formData.append('procedure_setup_data', JSON.stringify(procedureSetupData));
                formData.append('category_id', $('#category_id').val());
                
                // عرض رسالة تحميل
                Swal.fire({
                    title: '{{ __('Saving...') }}',
                    text: '{{ __('Please wait while saving the data') }}',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    willOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // إرسال البيانات إلى الخادم
                $.ajax({
                    url: '{{ route("tenant.document.procedures.saveConfigure", "__PLACEHOLDER__") }}'.replace('__PLACEHOLDER__', procedureId),
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        Swal.close();
                        if (response.status === 'success') {
                            notifier.show('Success','{{ __('Data saved successfully') }}', 'success',successImg, 4000);
                            setTimeout(() => {
                                window.location.href = '{{ $redirectUrl }}';
                            }, 2000);
                        } else {
                            notifier.show('Error',response.message || '{{ __('An error occurred while saving the data') }}', 'error',errorImg, 4000);
                        }
                    },
                    error: function(xhr) {
                        Swal.close();
                        notifier.show('Error!', '{{ __("An unexpected error occurred.") }}', 'error', errorImg, 4000);
                        console.error(xhr.responseText);
                    }
                });
            }
            
        });
    </script>
@endpush

