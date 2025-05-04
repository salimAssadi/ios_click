$(document).ready(function () {
    "use strict";
    select2();
    datatable();
    ckediter();
    setTimeout(function() {
        datepicker();
    }, 500);

    setInterval(() => {
        feather.replace();
    }, 1000);
});

$(document).on("click", ".customModal", function () {
    "use strict";
    var modalTitle = $(this).data("title");
    var modalUrl = $(this).data("url");
    var modalSize = $(this).data("size") == "" ? "md" : $(this).data("size");
    $("#customModal .modal-title").html(modalTitle);
    $("#customModal .modal-dialog").addClass("modal-" + modalSize);
    $.ajax({
        url: modalUrl,
        success: function (result) {
            if (result.status == "error") {
                notifier.show(
                    "Error!",
                    result.messages,
                    "error",
                    errorImg,
                    4000
                );
            } else {
                $("#customModal .body").html(result);
                $("#customModal").modal("show");
                select2();
                ckediter();
            }
        },
        error: function (result) { },
    });
});

// basic message
$(document).on("click", ".confirm_dialog", function (e) {
    "use strict";
    var title = $(this).attr("data-dialog-title");
    if (title == undefined) {
        var title = "Are you sure you want to delete this record ?";
    }
    var text = $(this).attr("data-dialog-text");
    if (text == undefined) {
        var text = "This record can not be restore after delete. Do you want to confirm?";
    }
    var dialogForm = $(this).closest("form");
    Swal.fire({
        title: title,
        text: text,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "نعم",
        cancelButtonText: "إلغاء",
    }).then((data) => {
        if (data.isConfirmed) {
            dialogForm.submit();
        }
    });
});

// common
$(document).on("click", ".common_confirm_dialog", function (e) {
    "use strict";
    var dialogForm = $(this).closest("form");
    var actions = $(this).data("actions");
    Swal.fire({
        title: "Are you sure you want to delete " + actions + " ?",
        text:
            "This " +
            actions +
            " can not be restore after delete. Do you want to confirm?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "نعم",
        cancelButtonText: "إلغاء",
    }).then((data) => {
        if (data.isConfirmed) {
            dialogForm.submit();
        }
    });
});

$(document).on("click", ".fc-day-grid-event", function (e) {
    "use strict";
    e.preventDefault();
    var event = $(this);
    var modalTitle = $(this).find(".fc-content .fc-title").html();
    var modalSize = "md";
    var modalUrl = $(this).attr("href");
    $("#customModal .modal-title").html(modalTitle);
    $("#customModal .modal-dialog").addClass("modal-" + modalSize);
    $.ajax({
        url: modalUrl,
        success: function (result) {
            $("#customModal .modal-body").html(result);
            $("#customModal").modal("show");
        },
        error: function (result) { },
    });
});

function toastrs(title, message, status) {
    "use strict";
    if (status == "success") {
        notifier.show("Success!", message, "success", successImg, 4000);
    } else {
        notifier.show("Error!", message, "error", errorImg, 4000);
    }
}

function convertArrayToJson(form) {
    "use strict";
    var data = $(form).serializeArray();
    var indexed_array = {};

    $.map(data, function (n, i) {
        indexed_array[n["name"]] = n["value"];
    });

    return indexed_array;
}

function select2() {
    "use strict";
    if ($(".basic-select").length > 0) {
        $(".basic-select").each(function () {
            var basic_select = new Choices(this, {
                searchEnabled: false,
                removeItemButton: false,
            });
        });
    }

    "use strict";
    if ($(".showsearch").length > 0) {
        $(".showsearch").each(function () {
            var basic_select = new Choices(this, {
                searchEnabled: true,
                removeItemButton: true,
            });
        });
    }

    if ($(".hidesearch").length > 0) {
        $(".hidesearch").each(function () {
            var basic_select = new Choices(this, {
                searchEnabled: false,
                removeItemButton: true,
            });
        });
    }
}

function ckediter(editer_id = "") {
    if (editer_id == "") {
        editer_id = "#classic-editor";
    }
    if ($(editer_id).length > 0) {
        ClassicEditor.create(document.querySelector(editer_id), {
            // Add configuration options here
            // height: '300px', // Example height, adjust as needed
        })
            .then((editor) => {
                // Set the minimum height directly // editor.ui.view.editable.element.style.minHeight = '300px';
            })
            .catch((error) => {
                console.error(error);
            });
    }
}
function datatable() {
    "use strict";

    if ($(".basic-datatable").length > 0) {
        $(".basic-datatable").DataTable({
            scrollX: true,
            stateSave: true,
            // paging: false,
        });
    }

    if ($(".easy-datatable").length > 0) {
        $(".easy-datatable").DataTable({
            scrollX: true,
            // stateSave: false,
            // dom: "Bfrtip",
            // buttons: ["copy", "csv", "excel", "print"],
        });
    }

    if ($(".advance-datatable").length > 0) {
        $(".advance-datatable").DataTable({
            scrollX: true,
            stateSave: false,
            dom: "Bfrtip",
            buttons: [
                {
                    extend: "excelHtml5",
                    exportOptions: {
                        columns: ":visible",
                    },
                },
                {
                    extend: "pdfHtml5",
                    exportOptions: {
                        columns: ":visible",
                    },
                },
                {
                    extend: "copyHtml5",
                    exportOptions: {
                        columns: ":visible",
                    },
                },

                "colvis",
            ],
        });
    }
}

function datepicker() {
    // Check if datepicker elements exist
    if (!$('#issue_date').length && !$('#expiry_date').length) {
        return;
    }
    
    try {
        // For Bootstrap 5 Datepicker
        if (typeof Datepicker !== 'undefined') {
            let issueDatePicker;
            let expiryDatePicker;
            
            // Issue date picker
            if ($('#issue_date').length) {
                issueDatePicker = new Datepicker(document.getElementById('issue_date'), {
                    format: 'yyyy-mm-dd',
                    autohide: true,
                    todayHighlight: true,
                    clearBtn: true
                });
                
                // Add click handler to calendar icon
                $('#issue_date').next('.input-group-text').on('click', function() {
                    issueDatePicker.show();
                });
            }
            
            // Expiry date picker
            if ($('#expiry_date').length) {
                expiryDatePicker = new Datepicker(document.getElementById('expiry_date'), {
                    format: 'yyyy-mm-dd',
                    autohide: true,
                    todayHighlight: true,
                    clearBtn: true
                });
                
                // Add click handler to calendar icon
                $('#expiry_date').next('.input-group-text').on('click', function() {
                    expiryDatePicker.show();
                });
            }
            
            // Add change event listener for issue date
            if (document.getElementById('issue_date') && issueDatePicker) {
                document.getElementById('issue_date').addEventListener('changeDate', function(e) {
                    const selectedDate = e.detail.date;
                    
                    // Update expiry date's minimum date
                    if (expiryDatePicker) {
                        expiryDatePicker.setOptions({
                            minDate: selectedDate
                        });
                    }
                });
            }
            
            return;
        }
        
        // Fallback for jQuery UI Datepicker
        if (typeof $.fn.datepicker === 'function') {
            var issueDateInput = $('#issue_date');
            var expiryDateInput = $('#expiry_date');

            var commonOptions = {
                format: 'yyyy-mm-dd',
                autoclose: true,
                todayHighlight: true,
                clearBtn: true,
                orientation: 'bottom'
            };

            if (issueDateInput.length) {
                issueDateInput.datepicker(commonOptions)
                    .on('changeDate', function(e) {
                        // Update expiry date min date when issue date changes
                        if (expiryDateInput.length) {
                            expiryDateInput.datepicker('setStartDate', e.date);
                        }
                    });
            }

            if (expiryDateInput.length) {
                expiryDateInput.datepicker(commonOptions);
            }

            // Add click handler to calendar icons
            $('.input-group-text').on('click', function() {
                var input = $(this).siblings('input');
                if (input.length && typeof input.datepicker === 'function') {
                    input.datepicker('show');
                }
            });
            
            console.log('jQuery Datepicker initialized');
            return;
        }
        
        console.warn('No datepicker library detected');
    } catch (error) {
        console.error('Error initializing datepicker:', error);
    }
}

if ($(".summernote").length) {
    "use strict";
    var lang = document.documentElement.lang;
    var editor_config = {
        path_absolute: window.location.origin + "/",  // Use the current domain
        document_base_url: window.location.origin + "/",  // Use the current domain
        selector: "textarea.summernote",
        theme: 'silver', // Specify your custom theme name
        plugins: ' image  table   preview anchor    visualblocks visualchars code   fullscreen',
        toolbar: ' undo redo  link |  emoticons styleselect  |fontfamily backcolor fontsize |alignleft aligncenter alignright alignjustify | preview language fullscreen',
        table_toolbar: 'tableprops tabledelete | tableinsertrowbefore tableinsertrowafter tabledeleterow | tableinsertcolbefore tableinsertcolafter tabledeletecol link  ',
        promotion: false,
        convert_urls: true,
        remove_script_host: false,
        relative_urls: false,
        directionality: "rtl",
        file_picker_callback: function (callback, value, meta) {
            if (meta.filetype === 'image') {
                // Open the Alexusmai file manager
                var x = window.innerWidth || document.documentElement.clientWidth || document.getElementsByTagName('body')[0].clientWidth;
                var y = window.innerHeight || document.documentElement.clientHeight || document.getElementsByTagName('body')[0].clientHeight;

                tinyMCE.activeEditor.windowManager.openUrl({
                    url: 'iso_dic/file-manager', // Adjust this to your file manager route
                    title: 'File Manager',
                    width: x * 0.8,
                    height: y * 0.8,
                    resizable: "yes",
                    close_previous: "no",
                    onMessage: (api, message) => {
                        callback(message.content); // Use the returned URL
                    }
                });
            }
        }

    };

    tinymce.init(editor_config);
}
