@extends('tenant::layouts.app')

@section('page-title', __('Create New Document'))
@push('script-page')
    <script src="{{ asset('assets/js/plugins/tinymce/tinymce.min.js') }}"></script>
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
                    <div class="progress-bar bg-gray-300 text-black" role="progressbar" style="width: 25%;"
                        aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
                        <span class="progress-text">{{ __('Step 1: ISO System') }}</span>
                    </div>
                </div>

                <!-- Form Steps -->
                <form id="documentWizard" action="{{ route('tenant.document.store') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <!-- Step 1: ISO System Selection -->
                    <div id="step1" class="card mb-4">
                        <div class="card-header bg-gray-300 text-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">{{ __('Select ISO System') }}</h5>
                            <span class="badge bg-light text-primary">{{ __('Step 1 of 4') }}</span>
                        </div>
                        <div class="card-body ">
                            <div class="row">
                                @foreach ($isoSystems as $system)
                                    <div class="col-md-6 mb-3">
                                        <div class="card h-100 cursor-pointer iso-system-card @error('iso_system_id') is-invalid @enderror"
                                            data-system-id="{{ $system->id }}">
                                            <div class="card-body shadow-sm rounded bg-gray-100">
                                                <div class="form-check">
                                                    <input type="radio" name="iso_system_id" id="iso_{{ $system->id }}"
                                                        value="{{ $system->id }}" class="form-check-input"
                                                        @if (old('iso_system_id') == $system->id) checked @endif>
                                                    <label class="form-check-label w-100" for="iso_{{ $system->id }}">
                                                        <div class="d-flex align-items-center">
                                                            @if ($system->image)
                                                                <img src="{{ getISOImage(getFilePath('isoIcon') . '/' . $system->image) }}"
                                                                    alt="{{ $system->name }}" class="me-3"
                                                                    style="width: 40px; height: 40px; object-fit: contain;">
                                                            @endif
                                                            <div>
                                                                <h6 class="mb-1">{{ $system->name }}</h6>
                                                                <small class="text-muted">{{ $system->code }}</small>
                                                            </div>
                                                        </div>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @error('iso_system_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Step 2: Document Type Selection -->
                    <div id="step2" class="card mb-4 d-none">
                        <div class="card-header bg-gray-300 text-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">{{ __('Select Document Type') }}</h5>
                            <span class="badge bg-light text-primary">{{ __('Step 2 of 4') }}</span>
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

                    <!-- Step 3: Template Selection -->
                    <div id="step3" class="card mb-4 d-none">
                        <div class="card-header bg-gray-300 text-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">{{ __('Select Template') }}</h5>
                            <span class="badge bg-light text-primary">{{ __('Step 3 of 4') }}</span>
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

                    <!-- Step 4: Document Details -->
                    <div id="step4" class="card mb-4 d-none">
                        <div class="card-header bg-gray-300 text-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">{{ __('Document Details') }}</h5>
                            <span class="badge bg-light text-primary">{{ __('Step 4 of 4') }}</span>
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
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">{{ __('Document Number') }} <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text document-prefix">DOC-</span>
                                        <input type="text" name="document_number" id="document_number"
                                            class="form-control @error('document_number') is-invalid @enderror"
                                            value="{{ old('document_number') }}" required>
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
                                    <textarea name="conent" id="document_content" class="form-control summernote @error('description') is-invalid @enderror" rows="3">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Navigation Buttons -->
                    <div class="d-flex justify-content-between bg-white p-2 mb-2 rounded-2">
                        <button type="button" class="btn btn-primary" id="prevBtn" style="display: none;">
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


@push('script-page')
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function() {
            // Wait for jQuery to be ready
            if (typeof jQuery === 'undefined') {
                console.error('jQuery is not loaded');
                return;
            }

            let currentStep = 1;
            const totalSteps = 4;

            function updateProgressBar(step) {
                const percent = (step / totalSteps) * 100;
                const $progressBar = $('.progress-bar');
                $progressBar.css('width', `${percent}%`);
                $progressBar.attr('aria-valuenow', percent);

                // Update progress text
                const stepTitles = [
                    '{{ __('Step 1: ISO System') }}',
                    '{{ __('Step 2: Document Type') }}',
                    '{{ __('Step 3: Template') }}',
                    '{{ __('Step 4: Details') }}'
                ];
                $('.progress-text').text(stepTitles[step - 1]);
            }

            function showStep(step) {
                // Hide all steps first
                $('#step1, #step2, #step3, #step4').addClass('d-none');
                // Show the current step
                $(`#step${step}`).removeClass('d-none');

                // Update buttons
                $('#prevBtn').toggle(step > 1);
                $('#nextBtn').toggle(step < totalSteps);
                $('#submitBtn').toggleClass('d-none', step !== totalSteps);

                updateProgressBar(step);
            }

            function validateStep(step) {
                let isValid = true;
                const $step = $(`#step${step}`);

                if (step === 1) {
                    if (!$('input[name="iso_system_id"]:checked').length) {
                        isValid = false;
                        toastr.error('{{ __('Please select an ISO system') }}');
                    }
                } else if (step === 2) {
                    if (!$('input[name="document_type"]:checked').length) {
                        isValid = false;
                        toastr.error('{{ __('Please select a document type') }}');
                    }
                } else if (step === 3) {
                    if (!$('input[name="template_id"]:checked').length) {
                        isValid = false;
                        toastr.error('{{ __('Please select a template') }}');
                    }
                }

                return isValid;
            }

            function loadTemplates() {
                const isoSystemId = $('input[name="iso_system_id"]:checked').val();
                const documentType = $('input[name="document_type"]:checked').val();

                if (isoSystemId && documentType) {
                    $.ajax({
                        url: '{{ route('tenant.document.templates') }}',
                        type: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        data: {
                            iso_system_id: isoSystemId,
                            document_type: documentType
                        },
                        beforeSend: function() {
                            $('#templatesContainer').html(
                                '<div class="text-center"><div class="spinner-border text-primary" role="status"></div></div>'
                                );
                        },
                        success: function(response) {
                            $('#templatesContainer').html(response.html);

                            // Add click handler for template cards
                            $('.template-card').click(function() {
                                const radio = $(this).find('input[type="radio"]');
                                radio.prop('checked', true);
                                $('.template-card').removeClass('selected');
                                $(this).addClass('selected');
                            });
                        },
                        error: function() {
                            $('#templatesContainer').html(
                                '<div class="alert alert-danger">Error loading templates</div>');
                        }
                    });
                }
            }

            // Event Handlers
            $('#nextBtn').click(function() {
                if (validateStep(currentStep)) {
                    // Check if we're on step 2 (document type) and custom type is selected
                    if (currentStep === 2 && $('input[name="document_type"]:checked').val() === 'custom') {
                        currentStep = 4; // Skip to step 4
                        showStep(currentStep);
                    }
                    // If we're on step 3 (template selection)
                    else if (currentStep === 3) {
                        const templateId = $('input[name="template_id"]:checked').val();
                        
                        if (templateId === 'custom') {
                            // For custom template, skip to step 4 and clear fields
                            currentStep = 4;
                            $('#title, #document_number').val('');
                            $('#version').val('1.0');
                            tinymce.get('document_content').setContent('');
                            showStep(currentStep);
                        } else {
                            // Show loading spinner
                            $('#nextBtn').prop('disabled', true)
                                .html('<span class="spinner-border spinner-border-sm me-2"></span>{{ __("Loading...") }}');
                            
                            // Make API request to get template data
                            const url = `{{ route('tenant.document.template.data', ':id') }}`.replace(':id', templateId);
                            
                            $.ajax({
                                url: url,
                                method: 'GET',
                                success: function(response) {
                                    if (response.data) {
                                        const template = response.data;
                                        $('#title').val(template.name || '');
                                        $('#document_number').val(template.number || '');
                                        $('#version').val(template.version || '');
                                        if (template.content) {
                                            tinymce.get('document_content').setContent(template.content);
                                        }
                                        // Move to next step after populating data
                                        currentStep++;
                                        showStep(currentStep);
                                    }
                                },
                                error: function(xhr) {
                                    const message = xhr.status === 404 ? 
                                        '{{ __("Template not found") }}' : 
                                        '{{ __("Failed to load template data") }}';
                                    notifier.show('Error!', message, 'error', errorImg, 4000);
                                },
                                complete: function() {
                                    // Re-enable next button and restore text
                                    $('#nextBtn').prop('disabled', false)
                                        .html('<i class="ti ti-arrow-left ms-1"></i> {{ __("Next") }}');
                                }
                            });
                        }
                    } else {
                        // For other steps, just move to next step
                        currentStep++;
                        showStep(currentStep);
                        if (currentStep === 3) {
                            loadTemplates();
                        }
                    }
                }
            });

            $('#prevBtn').click(function() {
                // If we are going back from step 4 and it's a custom document, skip step 3
                if (currentStep === 4 && $('input[name="document_type"]:checked').val() === 'custom') {
                    currentStep = 2; // Skip step 3 and go directly to step 2
                } else {
                    currentStep--;
                }
                showStep(currentStep);
            });

            // Card selection handlers
            $('.iso-system-card, .document-type-card').click(function() {
                const $card = $(this);
                const $radio = $card.find('input[type="radio"]');

                // Update visual selection
                if ($card.hasClass('iso-system-card')) {
                    $('.iso-system-card').removeClass('selected');
                } else if ($card.hasClass('document-type-card')) {
                    $('.document-type-card').removeClass('selected');
                }
                $card.addClass('selected');

                // Update radio button
                $radio.prop('checked', true);
            });

            // Template selection handler
            $(document).on('change', 'input[name="template_id"]', function() {
                const $card = $(this).closest('.template-card');
                $('.template-card').removeClass('selected');
                $card.addClass('selected');
            });

            // Initialize wizard - moved to the end
            setTimeout(function() {
                showStep(1);
            }, 100);

            // Form submission handler
            $('#documentWizard').on('submit', function(e) {
                e.preventDefault();
                if (validateStep(currentStep)) {
                    const $form = $(this);
                    const $submitBtn = $('#submitBtn');

                    $.ajax({
                        url: $form.attr('action'),
                        method: 'POST',
                        data: new FormData(this),
                        processData: false,
                        contentType: false,
                        beforeSend: function() {
                            $submitBtn.prop('disabled', true)
                                .html(
                                    '<span class="spinner-border spinner-border-sm me-2"></span>{{ __('Creating...') }}'
                                    );
                        },
                        success: function(response) {
                            notifier.show('Success!',
                                '{{ __('Document created successfully') }}', 'success',
                                successImg, 4000);
                            window.location.href = response.redirect;
                        },
                        error: function(xhr) {
                            $submitBtn.prop('disabled', false)
                                .html(
                                    '<i class="ti ti-device-floppy me-1"></i>{{ __('Create Document') }}'
                                    );

                            if (xhr.status === 422) {
                                const errors = xhr.responseJSON.errors;
                                Object.keys(errors).forEach(field => {
                                    notifier.show('Error!', errors[field][0], 'error',
                                        errorImg, 4000);
                                });
                            } else {
                                notifier.show('Error!',
                                    '{{ __('An error occurred while creating the document') }}',
                                    'error',
                                    errorImg, 4000);
                            }
                        }
                    });
                }
            });
        });
    </script>
@endpush
