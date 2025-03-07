$(document).ready(function() {
    (function($) {
        "use strict";

        $('[name=form_type]').on('change', function() {
            var formType = $(this).val();
            if (typeof formGenerator !== "undefined") {
                var extraFields = formGenerator.extraFields(formType);
                $('.extra_area').html(extraFields);
                $('.extra_area').find('select').select2({
                    dropdownParent: $('#formGenerateModal')
                });
            } else {
                console.error("formGenerator is not defined.");
            }
        }).change();

        $(document).on('click', '.addOption', function() {
            if (typeof formGenerator !== "undefined") {
                var html = formGenerator.addOptions();
                $('.options').append(html);
            }
        });

        $(document).on('click', '.removeOption', function() {
            $(this).closest('.form-group').remove();
        });

        $(document).on('click', '.editFormData', function() {
            if (typeof formGenerator !== "undefined") {
                formGenerator.formEdit($(this));
                $('.extra_area').find('select').select2({
                    dropdownParent: $('#formGenerateModal')
                });
            }
        });

        $(document).on('click', '.removeFormData', function() {
            $(this).closest('.col-md-4').remove();
        });

        $('.form-generate-btn').on('click', function() {
            if (typeof formGenerator !== "undefined") {
                formGenerator.showModal2();
            }
        });

        if (typeof formGenerator !== "undefined") {
            var updateId = formGenerator.totalField;
            $(formGenerator.formClassName).submit(function(e) {
                updateId += 1;
                e.preventDefault();
                var form = $(this);
                var formItem = formGenerator.formsToJson(form);
                formGenerator.makeFormHtml(formItem, updateId);
                formGenerator.closeModal();
            });
        } else {
            console.error("formGenerator is not defined.");
        }

    })(jQuery);
});
