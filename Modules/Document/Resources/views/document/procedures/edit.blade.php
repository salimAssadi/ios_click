@extends('tenant::layouts.app')

@section('page-title')
    {{ __('Edit Procedure') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="">{{ __('Dashboard') }}</a>
    </li>
    <li class="breadcrumb-item">
        @if($procedure->category_id == 1)
            <a href="{{ route('tenant.document.procedures.main') }}">{{ __('Main Procedures') }}</a>
        @elseif($procedure->category_id == 2)
            <a href="{{ route('tenant.document.procedures.public') }}">{{ __('Public Procedures') }}</a>
        @elseif($procedure->category_id == 3)
            <a href="{{ route('tenant.document.procedures.private') }}">{{ __('Private Procedures') }}</a>
        @endif
    </li>
    <li class="breadcrumb-item" aria-current="page">
        {{ __('Edit Procedure') }}
    </li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h5>{{ __('Edit') }}: {{ $procedure->procedure_name_ar }}</h5>
            </div>

            <div class="card-body">
                <div class="alert alert-warning">
                    {{ __('This action will be applied to the system you have specified') }}
                </div>
                <div class="general-info-box p-3 mb-4 border rounded bg-light">
                    <div class="row">
                        <div class="col-md-2">
                            <p class="mb-1 text-muted"><strong>{{ __('Procedure Number') }}:</strong></p>
                            <h6>{{ $procedure->id ?? 'N/A' }}</h6>
                        </div>
                        
                        <div class="col-md-2">
                            <p class="mb-1 text-muted"><strong>{{ __('Version Number') }}:</strong></p>
                            <h6>{{ $document->lastVersion->version ?? '1.0' }}</h6>
                        </div>
                        <div class="col-md-2">
                            <p class="mb-1 text-muted"><strong>{{ __('Review Number') }}:</strong></p>
                            <h6>{{ $document->lastVersion->latestRevision->revision_number ?? '0' }}</h6>
                        </div>
                        <div class="col-md-2">
                            <p class="mb-1 text-muted"><strong>{{ __('ISO System') }}:</strong></p>
                            <h6>{{ getIsoSystem(1)->name  ?? 'N/A' }}</h6>
                        </div>
                        <div class="col-md-2">
                            <p class="mb-1 text-muted"><strong>{{ __('Status') }}:</strong></p>
                            <h6>{!! $document->lastVersion->status_badge ?? 'N/A' !!}</h6>
                        </div>
                    </div>
                </div>
                
                <div class="accordion" id="procedureAccordion">
                    
                    <!-- Basic Info Section -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingBasic">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseBasic" aria-expanded="true" aria-controls="collapseBasic">
                                <strong class="text-black"> {{ __('Basic Information') }}</strong>
                            </button>
                        </h2>
                        <div id="collapseBasic" class="accordion-collapse collapse show" aria-labelledby="headingBasic" data-bs-parent="#procedureAccordion">
                            <div class="accordion-body">
                            {{ Form::model($procedure, ['id' => 'basicInfoForm', 'files' => true]) }}
                            @csrf
                            @method('PUT')
                            <div class="row">
                            <input type="hidden" name="iso_system_procedure_id" value="{{ $iso_system_Procedure->id }}">
                            <div class="form-group col-md-6">
                                {{ Form::label('category_id', __('Category'), ['class' => 'form-label']) }}
                                {{ Form::select('category_id', $categories, old('category_id', $procedure->category_id), ['class' => 'form-control hidesearch']) }}
                            </div>
                            <div class="form-group col-md-6">
                                {{ Form::label('procedure_code', __('Procedure Code') . ' <span class="text-danger">*</span>', ['class' => 'form-label'], false) }}
                                {{ Form::text('procedure_code', old('procedure_code', $procedure->procedure_code ?? $procedureCodeing), ['class' => 'form-control', 'placeholder' => __('Enter Procedure Code'), 'required' => 'required']) }}
                            </div>
                            <div class="form-group col-md-6">
                                {{ Form::label('procedure_name_ar', __('Procedure Name (arabic)') . ' <span class="text-danger">*</span>', ['class' => 'form-label'], false) }}
                                {{ Form::text('procedure_name_ar', old('procedure_name_ar', $procedure->procedure_name_ar), ['class' => 'form-control', 'placeholder' => __('Enter Procedure Name (arabic)'), 'required' => 'required']) }}
                            </div>

                            <div class="form-group col-md-6">
                                {{ Form::label('procedure_name_en', __('Procedure Name (english)') . ' <span class="text-danger">*</span>', ['class' => 'form-label'], false) }}
                                {{ Form::text('procedure_name_en', old('procedure_name_en', $procedure->procedure_name_en), ['class' => 'form-control', 'placeholder' => __('Enter Procedure Name (english)'), 'required' => 'required']) }}
                            </div>

                            <div class="form-group col-md-6">
                                {{ Form::label('procedure_description_ar', __('Procedure Description (arabic)'), ['class' => 'form-label']) }}
                                {{ Form::textarea('procedure_description_ar', old('procedure_description_ar', $procedure->description_ar), ['class' => 'form-control', 'placeholder' => __('Enter Procedure Description (arabic)'), 'rows' => 2]) }}
                            </div>

                            <div class="form-group col-md-6">
                                {{ Form::label('procedure_description_en', __('Procedure Description (english)'), ['class' => 'form-label']) }}
                                {{ Form::textarea('procedure_description_en', old('procedure_description_en', $procedure->description_en), ['class' => 'form-control', 'placeholder' => __('Enter Procedure Description (english)'), 'rows' => 2]) }}
                            </div>

                            <div class="form-group col-md-6 mt-3">
                                {{ Form::label('prepared_by', __('Preparer Name') . ' <span class="text-danger">*</span>', ['class' => 'form-label'], false) }}
                                {{ Form::select('prepared_by', $users ?? [], old('prepared_by', $procedure->prepared_by), ['class' => 'form-control hidesearch', 'required' => 'required']) }}
                            </div>
                            
                            <div class="form-group col-md-6 mt-3">
                                {{ Form::label('approved_by', __('Approver Name') . ' <span class="text-danger">*</span>', ['class' => 'form-label'], false) }}
                                {{ Form::select('approved_by', $users ?? [], old('approved_by', $procedure->approved_by), ['class' => 'form-control hidesearch', 'required' => 'required']) }}
                            </div>
                            
                            <div class="form-group col-md-12 mt-3">
                                {{ Form::label('reviewers', __('Reviewer Name'), ['class' => 'form-label']) }}
                                {{ Form::select('reviewers[]', $users ?? [], old('reviewers', $procedure->reviewers ? json_decode($procedure->reviewers) : []), ['class' => 'form-control hidesearch', 'multiple' => 'multiple']) }}
                                <small class="text-muted">{{ __('You can select multiple reviewers') }}</small>
                            </div>

                            <div class="form-group col-md-6 mt-3">
                                {{ Form::label('issue_date', __('Issue Date') . ' <span class="text-danger">*</span>', ['class' => 'form-label'], false) }}
                                <div class="input-group date">
                                    {{ Form::text('issue_date', old('issue_date', $document?->lastVersion->issue_date), ['class' => 'form-control' . ($errors->has('issue_date') ? ' is-invalid' : ''), 'required' => 'required', 'id' => 'issue_date', 'placeholder' => 'YYYY-MM-DD', 'autocomplete' => 'off']) }}
                                    <span class="input-group-text bg-primary">
                                        <i class="ti ti-calendar text-white"></i>
                                    </span>
                                </div>
                                
                            </div>
    
                            <div class="form-group col-md-6 mt-3">
                                {{ Form::label('expiry_date', __('Expiry Date') . ' <span class="text-danger">*</span>', ['class' => 'form-label'], false) }}
                                <span class="text-red-200">{{ __('Default value is 3 years from issue date') }}</span>
                                <div class="input-group date">
                                    {{ Form::text('expiry_date', old('expiry_date', $document?->lastVersion->expiry_date), ['class' => 'form-control' . ($errors->has('expiry_date') ? ' is-invalid' : ''), 'required' => 'required', 'id' => 'expiry_date', 'placeholder' => 'YYYY-MM-DD', 'autocomplete' => 'off']) }}
                                    <span class="input-group-text bg-primary">
                                        <i class="ti ti-calendar text-white"></i>
                                    </span>
                                </div>
                            </div>
    
                            <div class="form-group col-md-6 mt-3">
                                {{ Form::label('reminder_days', __('Reminder Before Expiry') . ' <span class="text-danger">*</span>', ['class' => 'form-label'], false) }}
                                <div class="input-group">
                                    {{ Form::number('reminder_days', old('reminder_days', $document?->lastVersion->reminder_days), ['class' => 'form-control' . ($errors->has('reminder_days') ? ' is-invalid' : ''), 'required' => 'required', 'min' => '1', 'max' => '365', 'id' => 'reminder_days']) }}
                                    <span class="input-group-text bg-info">
                                        <i class="ti ti-bell text-white"></i>
                                    </span>
                                </div>
                                <small class="text-muted">{{ __('Number of days before expiry to send reminder') }}</small>
                            </div>
    
                            <div class="form-group col-md-6 " style="display: none;">
                                {{ Form::label('is_optional', __('Is Required'), ['class' => 'form-label d-block']) }}
                                <div class="form-check form-check-inline">
                                    {{ Form::radio('is_optional', 1, $procedure->is_optional == 1, ['class' => 'form-check-input', 'id' => 'is_optional']) }}
                                    {{ Form::label('is_optional', __('Optional'), ['class' => 'form-check-label']) }}
                                </div>
                                <div class="form-check form-check-inline">
                                    {{ Form::radio('is_optional', 0, $procedure->is_optional == 0, ['class' => 'form-check-input', 'id' => 'required']) }}
                                    {{ Form::label('required', __('Required'), ['class' => 'form-check-label']) }}
                                </div>
                            </div>

                            <div class="form-group col-md-6">
                                {{ Form::label('status', __('Status'), ['class' => 'form-label d-block']) }}
                                <div class="form-check form-check-inline">
                                    {{ Form::radio('status', 1, $procedure->status == 1, ['class' => 'form-check-input', 'id' => 'status_active']) }}
                                    {{ Form::label('status_active', __('Active'), ['class' => 'form-check-label']) }}
                                </div>
                                <div class="form-check form-check-inline">
                                    {{ Form::radio('status', 0, $procedure->status == 0, ['class' => 'form-check-input', 'id' => 'status_inactive']) }}
                                    {{ Form::label('status_inactive', __('Inactive'), ['class' => 'form-check-label']) }}
                                </div>
                            </div>

                            <div class="form-group col-md-12 text-end mt-3">
                                <button type="button" class="btn btn-secondary" onclick="window.history.back()">{{ __('Cancel') }}</button>
                                <button type="button" id="update-basic-info" class="btn btn-primary">{{ __('Update') }}</button>
                            </div>

                            {{ Form::close() }}
                         </div>
                            </div>
                        </div>
                    </div>

                    <!-- Configuration Section -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingConfig">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseConfig" aria-expanded="false" aria-controls="collapseConfig">
                               <strong class="text-black"> {{ __('Procedure Content') }}</strong>
                            </button>
                        </h2>
                        <div id="collapseConfig" class="accordion-collapse collapse" aria-labelledby="headingConfig" data-bs-parent="#procedureAccordion">
                            <div class="accordion-body">
                                @php
                                    
                                @endphp
                                @include('document::document.procedures.config.procedure', [
                                    'purposes' => $purposes,
                                    'scopes' => $scopes,
                                    'responsibilities' => $responsibilities,
                                    'definitions' => $definitions,
                                    'forms' => $forms,
                                    'procedures' => $procedures,
                                    'risk_matrix' => $risk_matrix,
                                    'kpis' => $kpis,
                                    'users' => $users,
                                ])

                                <div class="text-end mt-3">
                                    <button type="button" class="btn btn-secondary" onclick="window.history.back()">{{ __('Cancel') }}</button>
                                    <button type="button" id="save-configuration" class="btn btn-primary">{{ __('Save Configuration') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div> <!-- End Accordion -->
            </div>
        </div>
    </div>
</div>

@endsection

@push('script-page')
<script>
    $(document).ready(function() {
       
        // Handle Basic Info Update via AJAX
        $('#update-basic-info').on('click', function() {
            let form = $('#basicInfoForm');
            let button = $(this);
            
            // Clear previous validation errors
            form.find('.is-invalid').removeClass('is-invalid');
            form.find('.invalid-feedback').remove();
            
            // Show loading state and disable button
            let originalText = button.html();
            button.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> {{ __('Updating...') }}');
            button.prop('disabled', true);
            $('#save-procedure').prop('disabled', true);
            
            let formData = new FormData(form[0]);
            formData.append('_method', 'PUT'); // Laravel method spoofing for PUT
            
            $.ajax({
                url: '{{ route("tenant.document.procedures.update", $procedure->id) }}',
                type: 'POST', // Use type instead of method
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    notifier.show('Success!', '{{ __('Procedure updated successfully') }}', 'success',successImg, 4000);

                    button.html(originalText);
                    button.prop('disabled', false);
                    $('#save-procedure').prop('disabled', false);
                    
                    $('#collapseBasic').removeClass('show');
                    $('#collapseConfig').addClass('show');
                    
                    $('#headingBasic button').addClass('collapsed').attr('aria-expanded', 'false');
                    $('#headingConfig button').removeClass('collapsed').attr('aria-expanded', 'true');
                },
                error: function(xhr) {
                    button.html(originalText);
                    button.prop('disabled', false);
                    $('#save-procedure').prop('disabled', false);
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        
                        $.each(errors, function (field, messages) {
                            let input = form.find('[name="' + field + '"]');

                            if (input.length) {
                                input.addClass('is-invalid');
                                let parentElement = input.closest('.form-group');
                                parentElement.append('<div class="invalid-feedback d-block">' + messages[0] + '</div>');
                            } else {
                                input = form.find('[name="' + field + '[]"]');
                                if (input.length) {
                                    input.addClass('is-invalid');
                                    let parentElement = input.closest('.form-group');
                                    parentElement.append('<div class="invalid-feedback d-block">' + messages[0] + '</div>');
                                }
                            }
                        });
                    } else {

                        notifier.show('Error!', '{{ __("An unexpected error occurred.") }}', 'error', errorImg, 4000);
                    }
                }
            });
        });

        // Handle Configuration Save via AJAX
        $('#save-configuration').on('click', function() {
            let button = $(this);
            
            // Show loading state and disable button
            let originalText = button.html();
            button.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> {{ __('Saving...') }}');
            button.prop('disabled', true);
            $('#save-procedure').prop('disabled', true);
            
            // Collect all form data using the function from procedure.blade.php
            const configData = collectAllFormData();
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
            $.ajax({
                url: '{{ route("tenant.document.procedures.saveConfigure", $procedure->id) }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    procedure_setup_data: JSON.stringify(configData),
                    category_id: '{{ $procedure->category_id }}'
                },
                success: function(response) {
                    Swal.close();
                    notifier.show('Success!', response.message || '{{ __('Configuration saved successfully') }}', 'success',successImg, 4000);
                    button.html(originalText);
                    button.prop('disabled', false);
                    $('#save-procedure').prop('disabled', false);
                },
                error: function(xhr) {
                    Swal.close();
                    button.html(originalText);
                    button.prop('disabled', false);
                    $('#save-procedure').prop('disabled', false);
                    
                    let errorMessage = '{{ __('Error saving configuration') }}';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    notifier.show('Error!', errorMessage, 'error',errorImg, 4000);
                }
            });
        });

        // Handle main form save button
        $('#save-procedure').on('click', function() {
            // Determine which tab is active and trigger its save button
            if ($('#basic-info').hasClass('active')) {
                $('#update-basic-info').click();
            } else if ($('#configuration').hasClass('active')) {
                $('#save-configuration').click();
            }
        });

        // Helper function to show notifications
        function showNotification(type, message) {
           
            toastrs(type,message)
        }
    });
</script>
@endpush
