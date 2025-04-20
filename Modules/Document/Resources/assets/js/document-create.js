document.addEventListener('DOMContentLoaded', function() {
    // Wait for jQuery to be ready
    if (typeof jQuery === 'undefined') {
        console.error('jQuery is not loaded');
        return;
    }

    let currentStep = 1;
    const totalSteps = 3;

    // Function to validate each step
    function validateStep(step) {
        if (step === 1) {
            const documentType = $('input[name="document_type"]:checked').val();
            if (!documentType) {
                notifier.show('Error!', Lang.get('Please select a document type'), 'error', errorImg, 4000);
                return false;
            }
        } else if (step === 2) {
            const templateId = $('input[name="template_id"]:checked').val();
            if (!templateId) {
                notifier.show('Error!', Lang.get('Please select a template'), 'error', errorImg, 4000);
                return false;
            }
        }
        return true;
    }

    // Function to show/hide steps and update progress bar
    function showStep(step) {
        for (let i = 1; i <= totalSteps; i++) {
            $(`#step${i}`).addClass('d-none');
        }

        // Show current step
        $(`#step${step}`).removeClass('d-none');

        // Update progress bar
        const progress = (step / totalSteps) * 100;
        $('.progress-bar').css('width', `${progress}%`);
        $('.progress-bar').attr('aria-valuenow', progress);

        // Update progress text based on current step
        const stepTexts = {
            1: Lang.get('Step 2: Document Type'),
            2: Lang.get('Step 2: Template'),
            3: Lang.get('Step 3: Document Details')
        };
        $('.progress-text').text(stepTexts[step]);

        // Show/hide/disable navigation buttons
        $('#prevBtn').prop('disabled', step === 1); // Disable on first step
        $('#nextBtn').toggleClass('d-none', step === totalSteps);
        $('#submitBtn').toggleClass('d-none', step !== totalSteps);
    }

    // Function to load templates based on document type
    function loadTemplates() {
        const documentType = $('input[name="document_type"]:checked').val();

        if (documentType) {
            $.ajax({
                url: routes.getTemplates,
                method: 'GET',
                data: {
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
                        '<div class="alert alert-danger">' + Lang.get('Error loading templates') + '</div>');
                }
            });
        }
    }

    // Event Handlers
    $('#nextBtn').click(function() {
        if (validateStep(currentStep)) {
            var documentType = $('input[name="document_type"]:checked').val();
            // Check if we're on step 1 (document type) and custom type is selected
            if (currentStep === 1 && documentType === 'custom') {
                currentStep = 3; // Skip to step 3
                showStep(currentStep);
               
            }
            // If we're on step 2 (template selection)
            else if (currentStep === 2) {
                const templateId = $('input[name="template_id"]:checked').val();
                
                if (templateId === 'custom') {
                    // For custom template, skip to step 3 and clear fields
                    currentStep = 3;
                    $('#version').val('1.0');
                    tinymce.get('document_content').setContent('');
                    showStep(currentStep);
                } else {
                    // Show loading spinner
                    $('#nextBtn').prop('disabled', true)
                        .html('<span class="spinner-border spinner-border-sm me-2"></span>' + Lang.get('Loading...'));
                    
                    // Make API request to get template data
                    const url = routes.getTemplateData.replace(':id', templateId);
                    
                    $.ajax({
                        url: url,
                        method: 'GET',
                        data:{
                            documentType:documentType
                        },
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
                                Lang.get('Template not found') : 
                                Lang.get('Failed to load template data');
                            notifier.show('Error!', message, 'error', errorImg, 4000);
                        },
                        complete: function() {
                            // Re-enable next button and restore text
                            $('#nextBtn').prop('disabled', false)
                                .html('<i class="ti ti-arrow-left ms-1"></i>' + Lang.get('Next'));
                        }
                    });
                }
            } else {
                // For other steps, just move to next step
                currentStep++;
                showStep(currentStep);
                if (currentStep === 2) {
                    loadTemplates();
                }
            }
        }
    });

    $('#prevBtn').click(function() {
        if (currentStep === 3 && $('input[name="document_type"]:checked').val() === 'custom') {
            currentStep = 1; // Skip step 3 and go directly to step 2
        } else {
            currentStep--;
        }
        showStep(currentStep);
    });

    // Card selection handlers
    $('.document-type-card').click(function() {
        const $card = $(this);
        const $radio = $card.find('input[type="radio"]');

        // Update visual selection
        $('.document-type-card').removeClass('selected');
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
                            '<span class="spinner-border spinner-border-sm me-2"></span>' + Lang.get('Creating...')
                        );
                },
                success: function(response) {
                    notifier.show('Success!',
                        Lang.get('Document created successfully'), 'success',
                        successImg, 4000);
                    window.location.href = response.redirect;
                },
                error: function(xhr) {
                    $submitBtn.prop('disabled', false)
                        .html(
                            '<i class="ti ti-device-floppy me-1"></i>' + Lang.get('Create Document')
                        );

                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        Object.keys(errors).forEach(field => {
                            notifier.show('Error!', errors[field][0], 'error',
                                errorImg, 4000);
                        });
                    } else {
                        notifier.show('Error!',
                            xhr.responseJSON.message ||
                            Lang.get('An error occurred while creating the document'),
                            'error',
                            errorImg, 4000);
                    }
                }
            });
        }
    });

    // Initialize wizard
    setTimeout(function() {
        showStep(1);
    }, 100);
});
