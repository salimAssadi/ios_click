@extends('tenant::layouts.app')

@section('page-title', __('Create New Document'))
@push('script-page')
    <script src="{{ asset('assets/js/plugins/tinymce/tinymce.min.js') }}"></script>
    <script>
        // Define routes for JavaScript
        const routes = {
            getTemplates: '{{ route("tenant.document.templates") }}',
            getTemplateData: '{{ route("tenant.document.template.data", ":id") }}'
        };
        
        // Define Lang object for translations
        const Lang = {
            get: function(key) {
                return {
                    'Please select an ISO system': '{{ __("Please select an ISO system") }}',
                    'Please select a document type': '{{ __("Please select a document type") }}',
                    'Please select a template': '{{ __("Please select a template") }}',
                    'Step 1: ISO System': '{{ __("Step 1: ISO System") }}',
                    'Step 1: Document Type': '{{ __("Step 1: Document Type") }}',
                    'Step 2: Template': '{{ __("Step 2: Template") }}',
                    'Step 3: Document Details': '{{ __("Step 3: Document Details") }}',
                    'Loading...': '{{ __("Loading...") }}',
                    'Next': '{{ __("Next") }}',
                    'Error loading templates': '{{ __("Error loading templates") }}',
                    'Template not found': '{{ __("Template not found") }}',
                    'Failed to load template data': '{{ __("Failed to load template data") }}',
                    'Creating...': '{{ __("Creating...") }}',
                    'Create Document': '{{ __("Create Document") }}',
                    'Document created successfully': '{{ __("Document created successfully") }}',
                    'An error occurred while creating the document': '{{ __("An error occurred while creating the document") }}'
                }[key];
            }
        };
    </script>
    <script src="{{ Module::asset('document:js/document-create.js') }}"></script>
@endpush
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('tenant.dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('tenant.document.index') }}">{{ __('Documents') }}</a></li>
    <li class="breadcrumb-item">{{ __('Create New Document') }}</li>
@endsection

@push('css-page')
    <style>
        .progress {
            border-radius: 2px;
            overflow: visible;
        }

        .progress-bar {
            position: relative;
            transition: width 0.6s ease;
        }

        .progress-text {
            position: absolute;
            width: 100%;
            text-align: center;
            font-weight: 500;
        }

        .cursor-pointer {
            cursor: pointer;
        }

        .iso-system-card,
        .document-type-card {
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .iso-system-card:hover,
        .document-type-card:hover {
            border-color: #6571ff;
        }

        .iso-system-card.selected,
        .document-type-card.selected {
            border-color: #6571ff;
            background-color: #f8f9ff;
        }

        .form-check-input:checked {
            background-color: #6571ff;
            border-color: #6571ff;
        }
    </style>
@endpush
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <!-- Progress Bar -->
                <div class="progress my-3" style="height: 25px;">
                    <div class="progress-bar bg-gray-300 text-black" role="progressbar" style="width: 0%;"
                        aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                        <span class="progress-text">{{ __('Step 1: Document Type') }}</span>
                    </div>
                </div>

                <!-- Form Steps -->
                <form id="documentWizard" action="{{ route('tenant.document.store') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <!-- Step 1: Document Type Selection -->
                    <div id="step1" class="card mb-4">
                        <div class="card-header bg-gray-300 text-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">{{ __('Select Document Type') }}</h5>
                            <span class="badge bg-light text-primary">{{ __('Step 1 of 3') }}</span>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @foreach (['procedure' => 'Procedure', 'policy' => 'Policies', 'instruction' => 'Instruction', 'sample' => 'Samples', 'custom' => 'Custom'] as $type => $label)
                                    <div class="col-md-4 mb-3">
                                        <div class="card h-100 cursor-pointer document-type-card @error('document_type') is-invalid @enderror"
                                            data-type="{{ $type }}">
                                            <div class="card-body shadow-sm rounded ">
                                                <div class="form-check">
                                                    <input type="radio" name="document_type"
                                                        id="type_{{ $type }}" value="{{ $type }}"
                                                        class="form-check-input"
                                                        @if (old('document_type') == $type) checked @endif>
                                                    <label class="form-check-label w-100" for="type_{{ $type }}">
                                                        <i class="ti ti-file-text me-2"></i>
                                                        <span>{{ __($label) }}</span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @error('document_type')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Step 2: Template Selection -->
                    <div id="step2" class="card mb-4 d-none">
                        <div class="card-header bg-gray-300 text-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">{{ __('Select Template') }}</h5>
                            <span class="badge bg-light text-primary">{{ __('Step 2 of 3') }}</span>
                        </div>
                        <div class="card-body">
                            <div id="templatesContainer" class="row">
                                <!-- Templates will be loaded dynamically -->
                                <div class="col-12">
                                    <div class="alert alert-info">
                                        <i class="ti ti-info-circle me-2"></i>
                                        {{ __('Templates will be loaded based on your ISO system and document type selection') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 3: Document Details -->
                    <div id="step3" class="card mb-4 d-none">
                        <div class="card-header bg-gray-300 text-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">{{ __('Document Details') }}</h5>
                            <span class="badge bg-light text-primary">{{ __('Step 3 of 3') }}</span>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">{{ __('Document Title') }} <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="title" id="title"
                                        class="form-control @error('title') is-invalid @enderror"
                                        value="{{ old('title') }}" required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3 d-none">
                                    <label class="form-label">{{ __('Document Number') }} <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text document-prefix">DOC-</span>
                                        <input type="text" name="document_number" id="document_number"
                                            class="form-control @error('document_number') is-invalid @enderror"
                                            value="{{ old('document_number') }}" >
                                    </div>
                                    @error('document_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">{{ __('Department') }} <span
                                            class="text-danger">*</span></label>
                                    <select name="department"
                                        class="form-select @error('department') is-invalid @enderror" required>
                                        <option value="">{{ __('Select Department') }}</option>
                                        @foreach (['quality' => 'Quality', 'operations' => 'Operations', 'hr' => 'Human Resources', 'finance' => 'Finance', 'it' => 'Information Technology'] as $value => $label)
                                            <option value="{{ $value }}"
                                                @if (old('department') == $value) selected @endif>
                                                {{ __($label) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('department')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">{{ __('Version') }}</label>
                                    <input type="text" name="version" id="version"
                                        class="form-control @error('version') is-invalid @enderror"
                                        value="{{ old('version', '1.0') }}">
                                    @error('version')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 mb-3">
                                    <label class="form-label">{{ __('Description') }}</label>
                                    <textarea name="content" id="document_content" class="form-control summernote @error('description') is-invalid @enderror" rows="3">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Navigation Buttons -->
                    <div class="d-flex justify-content-between bg-white p-2 mb-2 rounded-2">
                        <button type="button" class="btn btn-primary" id="prevBtn" disabled>
                            {{ __('Previous') }}<i class="ti ti-arrow-right me-1"></i>
                        </button>
                        <button type="button" class="btn btn-secondary float-end" id="nextBtn">
                            <i class="ti ti-arrow-left ms-1"></i> {{ __('Next') }}
                        </button>
                        <button type="submit" class="btn btn-success d-none" id="submitBtn">
                            <i class="ti ti-device-floppy me-1"></i> {{ __('Create Document') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
