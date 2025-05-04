@extends('tenant::layouts.app')

@section('page-title')
    {{ __('Create Supporting Document') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('tenant.dashboard') }}">{{ __('Dashboard') }}</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('tenant.document.supporting-documents.index') }}">{{ __('Supporting Documents') }}</a>
    </li>
    <li class="breadcrumb-item active">
        <a href="#">{{ __('Create Supporting Document') }}</a>
    </li>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5>{{ __('Create Supporting Document') }}</h5>
                </div>
                <div class="card-body">
                    {{ Form::open(['route' => 'tenant.document.supporting-documents.store', 'method' => 'post', 'files' => true]) }}
                    <div class="row">
                        <div class="form-group col-md-6">
                            {{ Form::label('title_ar', __('Document Title (Arabic)') . ' <span class="text-danger">*</span>', ['class' => 'form-label'], false) }}
                            {{ Form::text('title_ar', old('title_ar'), ['class' => 'form-control' . ($errors->has('title_ar') ? ' is-invalid' : ''), 'placeholder' => __('Enter Document Title in Arabic'), 'required' => 'required']) }}
                            @error('title_ar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group col-md-6">
                            {{ Form::label('title_en', __('Document Title (English)') . ' <span class="text-danger">*</span>', ['class' => 'form-label'], false) }}
                            {{ Form::text('title_en', old('title_en'), ['class' => 'form-control' . ($errors->has('title_en') ? ' is-invalid' : ''), 'placeholder' => __('Enter Document Title in English'), 'required' => 'required']) }}
                            @error('title_en')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group col-md-6 mt-3">
                            {{ Form::label('description_ar', __('Description (Arabic)'), ['class' => 'form-label']) }}
                            {{ Form::textarea('description_ar', old('description_ar'), ['class' => 'form-control' . ($errors->has('description_ar') ? ' is-invalid' : ''), 'placeholder' => __('Enter Description in Arabic'), 'rows' => 3]) }}
                            @error('description_ar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group col-md-6 mt-3">
                            {{ Form::label('description_en', __('Description (English)'), ['class' => 'form-label']) }}
                            {{ Form::textarea('description_en', old('description_en'), ['class' => 'form-control' . ($errors->has('description_en') ? ' is-invalid' : ''), 'placeholder' => __('Enter Description in English'), 'rows' => 3]) }}
                            @error('description_en')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group col-md-6 mt-3">
                            {{ Form::label('category_id', __('Supporting Document Category') . ' <span class="text-danger">*</span>', ['class' => 'form-label'], false) }}
                            <div class="input-group">
                                {{ Form::select('category_id', $categories, old('category_id'), ['class' => 'form-control' . ($errors->has('category_id') ? ' is-invalid' : ''), 'required' => 'required', 'id' => 'category_select']) }}
                                <button type="button" class="btn btn-sm btn-primary text-sm " title="{{ __('Add New Category') }}" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                                    <i class="fas fa-plus text-sm mx-1 me-1 "></i>
                                </button>
                            </div>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group col-md-6 mt-3">
                            {{ Form::label('issue_date', __('Issue Date') . ' <span class="text-danger">*</span>', ['class' => 'form-label'], false) }}
                            <div class="input-group date">
                                {{ Form::text('issue_date', old('issue_date'), ['class' => 'form-control' . ($errors->has('issue_date') ? ' is-invalid' : ''), 'required' => 'required', 'id' => 'issue_date', 'placeholder' => 'YYYY-MM-DD', 'autocomplete' => 'off']) }}
                                <span class="input-group-text bg-primary">
                                    <i class="ti ti-calendar text-white"></i>
                                </span>
                            </div>
                            @error('issue_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group col-md-6 mt-3">
                            {{ Form::label('expiry_date', __('Expiry Date') . ' <span class="text-danger">*</span>', ['class' => 'form-label'], false) }}
                            <div class="input-group date">
                                {{ Form::text('expiry_date', old('expiry_date'), ['class' => 'form-control' . ($errors->has('expiry_date') ? ' is-invalid' : ''), 'required' => 'required', 'id' => 'expiry_date', 'placeholder' => 'YYYY-MM-DD', 'autocomplete' => 'off']) }}
                                <span class="input-group-text bg-primary">
                                    <i class="ti ti-calendar text-white"></i>
                                </span>
                            </div>
                            @error('expiry_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group col-md-6 mt-3">
                            {{ Form::label('reminder_days', __('Reminder Before Expiry') . ' <span class="text-danger">*</span>', ['class' => 'form-label'], false) }}
                            <div class="input-group">
                                {{ Form::number('reminder_days', old('reminder_days', 30), ['class' => 'form-control' . ($errors->has('reminder_days') ? ' is-invalid' : ''), 'required' => 'required', 'min' => '1', 'max' => '365', 'id' => 'reminder_days']) }}
                                <span class="input-group-text bg-info">
                                    <i class="ti ti-bell text-white"></i>
                                </span>
                            </div>
                            <small class="text-muted">{{ __('Number of days before expiry to send reminder') }}</small>
                            @error('reminder_days')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group col-md-6 mt-3">
                            {{ Form::label('file', __('Document File') . ' <span class="text-danger">*</span>', ['class' => 'form-label'], false) }}
                            {{ Form::file('file', ['class' => 'form-control' . ($errors->has('file') ? ' is-invalid' : ''), 'required' => 'required']) }}
                            <small class="text-muted">{{ __('Supported formats: PDF. Max size: 10MB') }}</small>
                            @error('file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group col-md-12 text-end mt-4">
                            <a href="{{ route('tenant.document.supporting-documents.index') }}" class="btn btn-secondary">
                                {{ __('Cancel') }}
                            </a>
                            {{ Form::submit(__('Create'), ['class' => 'btn btn-primary']) }}
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script-page')
    
    <!-- Add Category Modal -->
    <div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCategoryModalLabel">{{ __('Add New Category') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addCategoryForm">
                        @csrf
                        <div class="mb-3">
                            <label for="name_ar" class="form-label">{{ __('Name (Arabic)') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="category_name_ar" placeholder="{{ __('Enter Name (Arabic)') }}" name="name_ar" required>
                        </div>
                        <div class="mb-3">
                            <label for="name_en" class="form-label">{{ __('Name (English)') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="category_name_en" placeholder="{{ __('Enter Name (English)') }}" name="name_en" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="button" class="btn btn-primary" id="saveCategory">{{ __('Save') }}</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#saveCategory').click(function() {
                // Get form data
                var nameAr = $('#category_name_ar').val();
                var nameEn = $('#category_name_en').val();
                
                if (!nameAr || !nameEn) {
                    alert("{{ __('Please fill all required fields') }}");
                    return;
                }
                
                // Send AJAX request
                $.ajax({
                    url: "{{ route('tenant.document.categories.store.ajax') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        name_ar: nameAr,
                        name_en: nameEn
                    },
                    success: function(response) {
                        if (response.success) {
                            // Add new option to select
                            $('#category_select').append(new Option(response.category.name, response.category.id, true, true));
                            
                            // Close modal
                            $('#addCategoryModal').modal('hide');
                            
                            // Clear form
                            $('#addCategoryForm')[0].reset();
                            
                            // Show success message
                            toastr.success("{{ __('Category added successfully') }}");
                        } else {
                            toastr.error(response.message || "{{ __('Error adding category') }}");
                        }
                    },
                    error: function(xhr) {
                        var errors = xhr.responseJSON.errors;
                        var errorMessages = [];
                        
                        for (var key in errors) {
                            errorMessages.push(errors[key][0]);
                        }
                        
                        toastr.error(errorMessages.join('<br>'));
                    }
                });
            });
            
           
        });
    </script>
@endpush
