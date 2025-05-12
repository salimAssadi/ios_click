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
                    {{ Form::open(['route' => 'tenant.document.procedures.store', 'method' => 'post', 'files' => true, 'id' => 'procedure-form']) }}
                    <div class="form-group col-md-12">
                        {{ Form::label('category_id', __('Category'), ['class' => 'form-label']) }}
                        {{ Form::select('category_id', $categories, old('category_id'), ['class' => 'form-control hidesearch' , 'required' => 'required' ]) }}
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6">
                            {{ Form::label('procedure_name_ar', __('Procedure Name (arabic)') . ' <span class="text-danger">*</span>', ['class' => 'form-label'], false) }}
                            {{ Form::text('procedure_name_ar', old('procedure_name_ar',''), ['class' => 'form-control', 'placeholder' => __('Enter Procedure Name (arabic)'), 'required' => 'required']) }}
                        </div>

                        <div class="form-group col-md-6">
                            {{ Form::label('procedure_name_en', __('Procedure Name (english)') . ' <span class="text-danger">*</span>', ['class' => 'form-label'], false) }}
                            {{ Form::text('procedure_name_en', old('procedure_name_en',''), ['class' => 'form-control', 'placeholder' => __('Enter Procedure Name (english)'), 'required' => 'required']) }}
                        </div>
                        <div class="form-group col-md-6">
                            {{ Form::label('procedure_description_ar', __('Procedure Description (arabic)'), ['class' => 'form-label']) }}
                            {{ Form::textarea('procedure_description_ar', old('procedure_description_ar',''), ['class' => 'form-control', 'placeholder' => __('Enter Procedure Description (arabic)'), 'rows' => 2]) }}
                        </div>

                        <div class="form-group col-md-6">
                            {{ Form::label('procedure_description_en', __('Procedure Description (english)'), ['class' => 'form-label']) }}
                            {{ Form::textarea('procedure_description_en', old('procedure_description_en',''), ['class' => 'form-control', 'placeholder' => __('Enter Procedure Description (english)'), 'rows' => 2]) }}
                        </div>

                    </div>



                    <div class="form-group col-md-12"  style="display: none;">
                        <label for="attachments" class="form-label">{{ __('Attachments') }}</label>
                        <input type="file" name="attachments[]" id="attachments" class="form-control" multiple value="{{ old('attachments') }}">
                        <small class="text-muted">{{ __('You can select multiple files') }}</small>
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
                    <div id="config-content" class="card-body"></div>
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
                            toastrs('success',response.message, 'success');
                        
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
                            // initConfigScripts();
                        } else {
                            // Show error message
                            toastrs('Error', response.message || 'An error occurred. Please try again.', 'error');
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
                        toastrs('Error', errorMessage, 'error');
                        
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
                // This function will be called after the config form is loaded
                // Any initialization for the config form can be added here
                
                // Add row to dynamic tables
                $(document).on('click', '.add-row', function() {
                    const tabId = $(this).data('tab');
                    const tbody = $(`#dynamic-table-${tabId} tbody`);
                    const index = tbody.find('tr').length;
                    
                    const newRow = `
                        <tr>
                            <td style="width: 50px;">
                                <input type="number" name="${tabId}[${index}][order]" class="form-control" value="${index + 1}">
                            </td>
                            <td>
                                <textarea name="${tabId}[${index}][content]" class="form-control" rows="2"></textarea>
                            </td>
                            <td style="width: 50px;">
                                <button type="button" class="btn btn-sm btn-danger remove-row px-3">-</button>
                            </td>
                        </tr>`;
                    
                    tbody.append(newRow);
                });
                
                // Remove row from dynamic tables
                $(document).on('click', '.remove-row', function() {
                    $(this).closest('tr').remove();
                });
            }
        });
    </script>
@endpush
