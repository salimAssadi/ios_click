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
                <a href="#" id="save-procedure" class="btn btn-sm btn-primary">{{ __('Save Changes') }}</a>
            </div>

            <div class="card-body">
                <div class="accordion" id="procedureAccordion">
                    
                    <!-- Basic Info Section -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingBasic">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseBasic" aria-expanded="true" aria-controls="collapseBasic">
                                {{ __('Basic Information') }}
                            </button>
                        </h2>
                        <div id="collapseBasic" class="accordion-collapse collapse show" aria-labelledby="headingBasic" data-bs-parent="#procedureAccordion">
                            <div class="accordion-body">
                                {{ Form::model($procedure, ['id' => 'basicInfoForm', 'files' => true]) }}
                                <div class="form-group col-md-12">
                                    {{ Form::label('category_id', __('Category'), ['class' => 'form-label']) }}
                                    {{ Form::select('category_id', $categories, old('category_id', $procedure->category_id), ['class' => 'form-control hidesearch']) }}
                                </div>
                                <div class="row">
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
                                <button type="button" id="update-basic-info" class="btn btn-primary">{{ __('Update Basic Info') }}</button>
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
                                {{ __('Configuration') }}
                            </button>
                        </h2>
                        <div id="collapseConfig" class="accordion-collapse collapse" aria-labelledby="headingConfig" data-bs-parent="#procedureAccordion">
                            <div class="accordion-body">
                                @include('document::document.procedures.config.procedure', [
                                    'purposes' => $purposes,
                                    'scopes' => $scopes,
                                    'responsibilities' => $responsibilities,
                                    'definitions' => $definitions,
                                    'forms' => $forms,
                                    'procedures' => $procedures,
                                    'risk_matrix' => $risk_matrix,
                                    'kpis' => $kpis,
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
            let formData = new FormData($('#basicInfoForm')[0]);
            formData.append('_method', 'PUT'); // Laravel method spoofing for PUT
            
            $.ajax({
                url: '{{ route("tenant.document.procedures.update", $procedure->id) }}',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    showNotification('success', '{{ __('Procedure updated successfully') }}');
                },
                error: function(xhr) {
                    let errors = xhr.responseJSON.errors;
                    let errorMessage = '{{ __('Error updating procedure') }}';
                    if (errors) {
                        errorMessage = Object.values(errors).flat().join('<br>');
                    }
                    showNotification('error', errorMessage);
                }
            });
        });

        // Handle Configuration Save via AJAX
        $('#save-configuration').on('click', function() {
            // Collect all form data using the function from procedure.blade.php
            const configData = collectAllFormData();
            
            $.ajax({
                url: '{{ route("tenant.document.procedures.saveConfigure", $procedure->id) }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    procedure_setup_data: JSON.stringify(configData),
                    category_id: '{{ $procedure->category_id }}'
                },
                success: function(response) {
                    showNotification('success', response.message || '{{ __('Configuration saved successfully') }}');
                },
                error: function(xhr) {
                    let errorMessage = '{{ __('Error saving configuration') }}';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    showNotification('error', errorMessage);
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
