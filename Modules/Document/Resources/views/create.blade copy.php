@extends('tenant::layouts.app')

@section('page-title')
    {{ __('Create New Document') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('tenant.document.index') }}">{{ __('Documents') }}</a></li>
    <li class="breadcrumb-item">{{ __('Create New Document') }}</li>
@endsection

@push('css')
<style>
.wizard-step {
    display: none;
}
.wizard-step.active {
    display: block;
}
.step-indicator {
    margin-bottom: 30px;
}
.step-indicator .step {
    display: inline-block;
    margin-right: 10px;
    padding: 10px 20px;
    background: #f8f9fa;
    border-radius: 5px;
    cursor: pointer;
}
.step-indicator .step.active {
    background: #6571ff;
    color: white;
}
.step-indicator .step.completed {
    background: #28a745;
    color: white;
}
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h5>{{ __('Create New Document') }}</h5>
            </div>
            <div class="card-body">
                <div class="step-indicator">
                    <div class="step active" data-step="1">{{ __('Select ISO System') }}</div>
                    <div class="step" data-step="2">{{ __('Document Type') }}</div>
                    <div class="step" data-step="3">{{ __('Document Template') }}</div>
                    <div class="step" data-step="4">{{ __('Document Details') }}</div>
                </div>

                <form id="documentForm" action="{{ route('tenant.document.store') }}" method="POST">
                    @csrf
                    
                    <!-- Step 1: ISO System Selection -->
                    <div class="wizard-step active" data-step="1">
                        <div class="row">
                            <div class="col-12">
                                <h6 class="mb-3">{{ __('Select ISO System') }}</h6>
                                <div class="iso-systems">
                                    @foreach($isoSystems as $system)
                                    <div class="form-check mb-2">
                                        <input type="radio" name="iso_system_id" id="iso_{{ $system->id }}" value="{{ $system->id }}" class="form-check-input">
                                        <label class="form-check-label" for="iso_{{ $system->id }}">
                                            {{ $system->name }}
                                        </label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Document Type Selection -->
                    <div class="wizard-step" data-step="2">
                        <div class="row">
                            <div class="col-12">
                                <h6 class="mb-3">{{ __('Select Document Type') }}</h6>
                                <div class="document-types">
                                    <div class="form-check mb-2">
                                        <input type="radio" name="document_type" id="type_procedure" value="procedure" class="form-check-input">
                                        <label class="form-check-label" for="type_procedure">
                                            {{ __('Procedure') }}
                                        </label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input type="radio" name="document_type" id="type_policy" value="policy" class="form-check-input">
                                        <label class="form-check-label" for="type_policy">
                                            {{ __('Policy') }}
                                        </label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input type="radio" name="document_type" id="type_instruction" value="instruction" class="form-check-input">
                                        <label class="form-check-label" for="type_instruction">
                                            {{ __('Work Instruction') }}
                                        </label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input type="radio" name="document_type" id="type_form" value="form" class="form-check-input">
                                        <label class="form-check-label" for="type_form">
                                            {{ __('Form') }}
                                        </label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input type="radio" name="document_type" id="type_custom" value="custom" class="form-check-input">
                                        <label class="form-check-label" for="type_custom">
                                            {{ __('Custom Document') }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 3: Template Selection -->
                    <div class="wizard-step" data-step="3">
                        <div class="row">
                            <div class="col-12">
                                <h6 class="mb-3">{{ __('Select Template') }}</h6>
                                <div class="templates-container">
                                    <!-- Templates will be loaded dynamically -->
                                    <div class="alert alert-info">{{ __('Please select an ISO system and document type first') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 4: Document Details -->
                    <div class="wizard-step" data-step="4">
                        <div class="row">
                            <div class="col-12">
                                <h6 class="mb-3">{{ __('Document Details') }}</h6>
                                <div class="mb-3">
                                    <label class="form-label">{{ __('Document Title') }}</label>
                                    <input type="text" name="title" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">{{ __('Document Number') }}</label>
                                    <input type="text" name="document_number" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">{{ __('Department') }}</label>
                                    <select name="department" class="form-select" required>
                                        <option value="">{{ __('Select Department') }}</option>
                                        <option value="quality">{{ __('Quality') }}</option>
                                        <option value="operations">{{ __('Operations') }}</option>
                                        <option value="hr">{{ __('HR') }}</option>
                                        <option value="finance">{{ __('Finance') }}</option>
                                        <option value="it">{{ __('IT') }}</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">{{ __('Description') }}</label>
                                    <textarea name="description" class="form-control" rows="3"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-12">
                            <button type="button" class="btn btn-secondary" id="prevStep" style="display: none;">{{ __('Previous') }}</button>
                            <button type="button" class="btn btn-primary" id="nextStep">{{ __('Next') }}</button>
                            <button type="submit" class="btn btn-success" id="submitForm" style="display: none;">{{ __('Create Document') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
$(document).ready(function() {
    let currentStep = 1;
    const totalSteps = 4;

    function updateSteps() {
        $('.wizard-step').removeClass('active');
        $(`.wizard-step[data-step="${currentStep}"]`).addClass('active');
        
        $('.step').removeClass('active completed');
        for(let i = 1; i <= totalSteps; i++) {
            if(i < currentStep) {
                $(`.step[data-step="${i}"]`).addClass('completed');
            } else if(i === currentStep) {
                $(`.step[data-step="${i}"]`).addClass('active');
            }
        }

        // Show/hide buttons
        if(currentStep === 1) {
            $('#prevStep').hide();
        } else {
            $('#prevStep').show();
        }

        if(currentStep === totalSteps) {
            $('#nextStep').hide();
            $('#submitForm').show();
        } else {
            $('#nextStep').show();
            $('#submitForm').hide();
        }
    }

    $('#nextStep').click(function() {
        if(validateCurrentStep()) {
            currentStep++;
            if(currentStep > totalSteps) currentStep = totalSteps;
            updateSteps();
            if(currentStep === 3) {
                loadTemplates();
            }
        }
    });

    $('#prevStep').click(function() {
        currentStep--;
        if(currentStep < 1) currentStep = 1;
        updateSteps();
    });

    function validateCurrentStep() {
        let valid = true;
        if(currentStep === 1) {
            if(!$('input[name="iso_system_id"]:checked').val()) {
                alert("{{ __('Please select an ISO system') }}");
                valid = false;
            }
        } else if(currentStep === 2) {
            if(!$('input[name="document_type"]:checked').val()) {
                alert("{{ __('Please select a document type') }}");
                valid = false;
            }
        }
        return valid;
    }

    function loadTemplates() {
        const isoSystemId = $('input[name="iso_system_id"]:checked').val();
        const documentType = $('input[name="document_type"]:checked').val();
        
        $.get(`{{ route('tenant.document.templates') }}`, {
            iso_system_id: isoSystemId,
            document_type: documentType
        }).done(function(response) {
            $('.templates-container').html(response);
        }).fail(function() {
            $('.templates-container').html('<div class="alert alert-danger">{{ __("Error loading templates") }}</div>');
        });
    }

    // Step indicator clicks
    $('.step').click(function() {
        const clickedStep = parseInt($(this).data('step'));
        if(clickedStep < currentStep) {
            currentStep = clickedStep;
            updateSteps();
        }
    });
});
</script>
@endpush
