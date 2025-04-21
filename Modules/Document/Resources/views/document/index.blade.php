@extends('tenant::layouts.app')

@push('css-page')
    <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <style>
        /* Sidebar Toggle */
        #sidebar-wrapper {
            transition: all .3s cubic-bezier(0.4, 0, 0.2, 1);
            will-change: margin-left, width;
            position: relative;
            width: 16%;
        }

        #sidebar-wrapper.collapsed {
            margin-left: -1%;
            width: 0;
        }

        #content-wrapper {
            transition: all .3s cubic-bezier(0.4, 0, 0.2, 1);
            will-change: margin-left, width;
            width: 83.33333%;
        }

        #content-wrapper.expanded {
            margin-left: 0;
            width: 100%;
        }

        /* Toggle Button */
        #sidebarToggle {
            transition: all .2s cubic-bezier(0.4, 0, 0.2, 1);
            will-change: transform;
            border: 1px solid #dee2e6;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            width: 32px;
            height: 32px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background: white;
        }

        #sidebarToggle:hover {
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transform: translateY(-1px);
        }

        #sidebarToggle:active {
            transform: translateY(0);
        }

        #toggleIcon {
            transition: transform .3s cubic-bezier(0.4, 0, 0.2, 1);
            will-change: transform;
            font-size: 1.25rem;
        }

        .collapsed #toggleIcon {
            transform: rotate(180deg);
        }

        /* File Tree Styles */
        .file-tree {
            font-size: 0.95rem;
            overflow-x: hidden;
        }

        .file-tree .folder-toggle {
            color: var(--bs-body-color);
            padding: 0.5rem;
            border-radius: 0.25rem;
            transition: all .2s cubic-bezier(0.4, 0, 0.2, 1);
            margin-bottom: 2px;
        }

        .file-tree .folder-toggle:hover {
            background-color: rgba(var(--bs-primary-rgb), 0.05);
            transform: translateX(2px);
        }

        .file-tree .document-filter {
            transition: all .2s cubic-bezier(0.4, 0, 0.2, 1);
            padding: 0.5rem;
            border-radius: 0.25rem;
            margin: 2px 0;
        }

        .file-tree .document-filter:hover {
            transform: translateX(4px);
            background-color: rgba(var(--bs-primary-rgb), 0.05);
        }

        .file-tree .document-filter.active {
            background-color: rgba(var(--bs-primary-rgb), 0.1);
            color: var(--bs-primary) !important;
            font-weight: 500;
        }

        .rotate-icon {
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            will-change: transform;
        }

        .folder-toggle.collapsed .rotate-icon {
            transform: rotate(-90deg);
        }

        /* Card Styles */
        .card {
            transition: all .3s cubic-bezier(0.4, 0, 0.2, 1);
            height: calc(100vh - 100px);
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .card:hover {
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .card-body {
            flex: 1;
            overflow: auto;
        }

        /* Smooth collapse animation */
        .collapse {
            transition: height .3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* DataTable improvements */
        .table-responsive {
            transition: all .3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .dataTables_wrapper .row {
            margin: 0;
            padding: 1rem 0;
        }
    </style>
@endpush

@section('content')
    <div class="row g-2">
        <!-- Sidebar Toggle Button -->
       

        <!-- File Browser Style Document Tree -->
        <div class="col-md-2" id="sidebar-wrapper">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">{{ __('Document Structure') }}</h5>
                </div>
                <div class="card-body p-0">
                    <div class="file-tree">
                        <ul class="list-unstyled mb-0">
                            <!-- Procedures -->
                            <li>
                                <a data-bs-toggle="collapse" href="#folderProcedures" role="button" aria-expanded="false"
                                    class="d-flex align-items-center folder-toggle text-decoration-none collapsed">
                                    <i class="ti ti-folder me-2 text-warning"></i>
                                    <span>{{ __('Procedures') }}</span>
                                    <i class="ti ti-chevron-right ms-auto rotate-icon"></i>
                                </a>
                                <ul class="collapse list-unstyled ps-3" id="folderProcedures">
                                    <li>
                                        <a href="#" class="text-dark d-flex align-items-center document-filter p-2"
                                            data-type="procedure" data-status="active">
                                            <i class="ti ti-file-text me-2 text-success"></i> {{ __('Active') }}
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#" class="text-dark d-flex align-items-center document-filter p-2"
                                            data-type="procedure" data-status="draft">
                                            <i class="ti ti-file-text me-2 text-warning"></i> {{ __('Drafts') }}
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#" class="text-dark d-flex align-items-center document-filter p-2"
                                            data-type="procedure" data-status="archived">
                                            <i class="ti ti-file-text me-2 text-secondary"></i> {{ __('Archived') }}
                                        </a>
                                    </li>
                                </ul>
                            </li>

                            <!-- Forms -->
                            <li class="mt-2">
                                <a data-bs-toggle="collapse" href="#folderForms" role="button" aria-expanded="false"
                                    class="d-flex align-items-center folder-toggle text-decoration-none collapsed">
                                    <i class="ti ti-folder me-2 text-warning"></i>
                                    <span>{{ __('Forms') }}</span>
                                    <i class="ti ti-chevron-right ms-auto rotate-icon"></i>
                                </a>
                                <ul class="collapse list-unstyled ps-3" id="folderForms">
                                    <li>
                                        <a href="#" class="text-dark d-flex align-items-center document-filter p-2"
                                            data-type="form" data-status="active">
                                            <i class="ti ti-file-text me-2 text-success"></i> {{ __('Active') }}
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#" class="text-dark d-flex align-items-center document-filter p-2"
                                            data-type="form" data-status="draft">
                                            <i class="ti ti-file-text me-2 text-warning"></i> {{ __('Drafts') }}
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#" class="text-dark d-flex align-items-center document-filter p-2"
                                            data-type="form" data-status="archived">
                                            <i class="ti ti-file-text me-2 text-secondary"></i> {{ __('Archived') }}
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-10" id="content-wrapper">
            <div class="card">
                <div class="card-header">
                   
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 d-flex align-items-center gap-4">
                            <button type="button" class="btn btn-sm btn-light rounded-circle" id="sidebarToggle" style="right: 16.66666%; z-index: 1000; transform: translateX(-50%);">
                                <i class="ti ti-chevron-left" id="toggleIcon"></i>
                            </button>
                            <span id="current-folder">{{ __('All Documents') }}</span>
                        </h5>
                        <div>
                            <a href="{{ route('tenant.document.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus me-1"></i>{{ __('New Document') }}
                            </a>
                            @if(getSettingsValByName('import_dictionary')!='1')
                            <button type="button" class="btn btn-info btn-sm" id="importDictionary">
                                <i class="fas fa-download me-1"></i>{{ __('Get Dictionary Documents') }}
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="documents-table">
                            <thead>
                                <tr>
                                    <th>{{ __('ID') }}</th>
                                    <th>{{ __('Title') }}</th>
                                    <th>{{ __('Version') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Created At') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Import Progress Modal -->
    <div class="modal fade" id="importProgressModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Importing Documents') }}</h5>
                </div>
                <div class="modal-body text-center">
                    <div class="mb-3">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">{{ __('Loading...') }}</span>
                        </div>
                    </div>
                    <p id="importStatus">{{ __('Importing documents from dictionary...') }}</p>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script-page')
<script>
$(document).ready(function() {
    // Initialize variables for document filtering
    let currentType = null;
    let currentStatus = null;
    let isAnimating = false;

    // Initialize DataTable
    var table = $('#documents-table').DataTable({
        serverSide: true,
        processing: true,
        ajax: {
            url: '{{ route('tenant.document.list') }}',
            data: function(d) {
                d.document_type = currentType;
                d.status = currentStatus;
            }
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'title', name: 'title' },
            { data: 'version_badge', name: 'version_badge' },
            { data: 'status_badge', name: 'status_badge' },
            { data: 'created_at', name: 'created_at' },
            { 
                data: 'actions',
                name: 'actions',
                orderable: false,
                searchable: false,
                className: 'text-center'
            }
        ],
        initComplete: function(settings, json) {
            // if (json.recordsTotal > 0) {
            //     $('#importDictionary').hide();
            // }
        }
    });

    // Handle document filtering
    $('.document-filter').click(function(e) {
        e.preventDefault();
        $('.document-filter').removeClass('active');
        $(this).addClass('active');

        currentType = $(this).data('type');
        currentStatus = $(this).data('status');

        // Update current folder text
        const folderName = $(this).closest('ul').siblings('.folder-toggle').find('span').text();
        const status = $(this).text().trim();
        $('#current-folder').text(folderName + ' - ' + status);

        // Reload table with new filters
        table.ajax.reload();
    });

    // Toggle sidebar with smooth animation
    $('#sidebarToggle').click(function() {
        if (isAnimating) return;
        isAnimating = true;

        $('#sidebar-wrapper').toggleClass('collapsed');
        $('#content-wrapper').toggleClass('expanded');
        $('#toggleIcon').toggleClass('ti-chevron-left ti-chevron-right');
        
        // Save state to localStorage
        localStorage.setItem('sidebarCollapsed', $('#sidebar-wrapper').hasClass('collapsed'));

        // Reset animation flag after transition completes
        setTimeout(() => {
            isAnimating = false;
        }, 300); // Match the transition duration
    });

    // Restore sidebar state with animation
    if (localStorage.getItem('sidebarCollapsed') === 'true') {
        $('#sidebar-wrapper').addClass('collapsed');
        $('#content-wrapper').addClass('expanded');
        $('#toggleIcon').addClass('ti-chevron-right').removeClass('ti-chevron-left');
    }

    // Import dictionary documents
    $('#importDictionary').click(function() {
        $('#importProgressModal').modal('show');

        $.ajax({
            url: '{{ route("tenant.document.import-dictionary") }}',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    $('#importStatus').html('<i class="fas fa-check-circle text-success"></i> ' + response.message);
                    setTimeout(function() {
                        $('#importProgressModal').modal('hide');
                        table.draw();
                        $('#importDictionary').hide();
                    }, 1500);
                } else {
                    $('#importStatus').html('<i class="fas fa-times-circle text-danger"></i> ' + response.message);
                    setTimeout(function() {
                        $('#importProgressModal').modal('hide');
                    }, 1500);
                }
            },
            error: function(xhr) {
                let message = xhr.responseJSON?.message || '{{ __("Error importing documents") }}';
                $('#importStatus').html('<i class="fas fa-times-circle text-danger"></i> ' + message);
                setTimeout(function() {
                    $('#importProgressModal').modal('hide');
                }, 1500);
            }
        });
    });
});
</script>
@endpush
