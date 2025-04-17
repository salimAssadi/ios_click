@extends('tenant::layouts.app')

@section('css-page')
    {{-- <link rel="stylesheet" href="{{ asset('tenant/css/document.css') }}"> --}}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('tenant.document.index') }}">{{ __('Documents') }}</a></li>
@endsection
@section('page-title')
    {{ __('Document Structure') }}
@endsection

@section('content')
    <div class="row">
        <!-- File Browser Style Document Tree -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">{{ __('Document Structure') }}</h5>
                </div>
                <div class="card-body py-2 px-3">
                    <div class="file-tree">
                        <ul class="list-unstyled">
                            <!-- Folder -->
                            <li>
                                <a data-bs-toggle="collapse" href="#folderProcedures" role="button" aria-expanded="false"
                                    class="d-flex align-items-center folder-toggle text-decoration-none mb-1">
                                    <i class="ti ti-folder me-2 text-warning"></i>
                                    <span>{{ __('Procedures') }}</span>
                                    <i class="ti ti-chevron-right ms-auto transition rotate-icon"></i>
                                </a>
                                <ul class="collapse ps-4 list-unstyled" id="folderProcedures">
                                    <li>
                                        <a href="#" class="text-dark d-flex align-items-center document-filter"
                                            data-type="procedure" data-status="active">
                                            <i class="ti ti-file-text me-2 text-secondary"></i> {{ __('Active') }}
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#" class="text-dark d-flex align-items-center document-filter"
                                            data-type="procedure" data-status="draft">
                                            <i class="ti ti-file-text me-2 text-secondary"></i> {{ __('Drafts') }}
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#" class="text-dark d-flex align-items-center document-filter"
                                            data-type="procedure" data-status="archived">
                                            <i class="ti ti-file-text me-2 text-secondary"></i> {{ __('Archived') }}
                                        </a>
                                    </li>
                                </ul>
                            </li>

                            <!-- Repeat for each document type -->
                            <li class="mt-2">
                                <a data-bs-toggle="collapse" href="#folderPolicies"
                                    class="d-flex align-items-center folder-toggle text-decoration-none mb-1">
                                    <i class="ti ti-folder me-2 text-warning"></i>
                                    <span>{{ __('Policies') }}</span>
                                    <i class="ti ti-chevron-right ms-auto transition rotate-icon"></i>
                                </a>
                                <ul class="collapse ps-4 list-unstyled" id="folderPolicies">
                                    <li><a href="#" class="text-dark d-flex align-items-center document-filter"
                                            data-type="policy" data-status="active"><i
                                                class="ti ti-file-text me-2 text-secondary"></i> {{ __('Active') }}</a>
                                    </li>
                                    <li><a href="#" class="text-dark d-flex align-items-center document-filter"
                                            data-type="policy" data-status="draft"><i
                                                class="ti ti-file-text me-2 text-secondary"></i> {{ __('Drafts') }}</a>
                                    </li>
                                    <li><a href="#" class="text-dark d-flex align-items-center document-filter"
                                            data-type="policy" data-status="archived"><i
                                                class="ti ti-file-text me-2 text-secondary"></i> {{ __('Archived') }}</a>
                                    </li>
                                </ul>
                            </li>

                            <!-- Instructions -->
                            <li class="mt-2">
                                <a data-bs-toggle="collapse" href="#folderInstructions"
                                    class="d-flex align-items-center folder-toggle text-decoration-none mb-1">
                                    <i class="ti ti-folder me-2 text-warning"></i>
                                    <span>{{ __('Instructions') }}</span>
                                    <i class="ti ti-chevron-right ms-auto transition rotate-icon"></i>
                                </a>
                                <ul class="collapse ps-4 list-unstyled" id="folderInstructions">
                                    <li><a href="#" class="text-dark d-flex align-items-center document-filter"
                                            data-type="instruction" data-status="active"><i
                                                class="ti ti-file-text me-2 text-secondary"></i> {{ __('Active') }}</a>
                                    </li>
                                    <li><a href="#" class="text-dark d-flex align-items-center document-filter"
                                            data-type="instruction" data-status="draft"><i
                                                class="ti ti-file-text me-2 text-secondary"></i> {{ __('Drafts') }}</a>
                                    </li>
                                    <li><a href="#" class="text-dark d-flex align-items-center document-filter"
                                            data-type="instruction" data-status="archived"><i
                                                class="ti ti-file-text me-2 text-secondary"></i> {{ __('Archived') }}</a>
                                    </li>
                                </ul>
                            </li>

                            <!-- Samples -->
                            <li class="mt-2">
                                <a data-bs-toggle="collapse" href="#folderSamples"
                                    class="d-flex align-items-center folder-toggle text-decoration-none mb-1">
                                    <i class="ti ti-folder me-2 text-warning"></i>
                                    <span>{{ __('Samples') }}</span>
                                    <i class="ti ti-chevron-right ms-auto transition rotate-icon"></i>
                                </a>
                                <ul class="collapse ps-4 list-unstyled" id="folderSamples">
                                    <li><a href="#" class="text-dark d-flex align-items-center document-filter"
                                            data-type="sample" data-status="active"><i
                                                class="ti ti-file-text me-2 text-secondary"></i> {{ __('Active') }}</a>
                                    </li>
                                    <li><a href="#" class="text-dark d-flex align-items-center document-filter"
                                            data-type="sample" data-status="draft"><i
                                                class="ti ti-file-text me-2 text-secondary"></i> {{ __('Drafts') }}</a>
                                    </li>
                                    <li><a href="#" class="text-dark d-flex align-items-center document-filter"
                                            data-type="sample" data-status="archived"><i
                                                class="ti ti-file-text me-2 text-secondary"></i> {{ __('Archived') }}</a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- Document List -->
        <div class="col-md-9">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <span id="current-folder">{{ __('All Documents') }}</span>
                        </h5>
                        <div>
                            <a href="{{ route('tenant.document.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus me-1"></i>{{ __('New Document') }}
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="documents-table">
                            <thead>
                                <tr>
                                    <th>{{ __('Document Number') }}</th>
                                    <th>{{ __('Title') }}</th>
                                    <th>{{ __('Department') }}</th>
                                    <th>{{ __('Version') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('css-page')
        <style>
            /* File Browser Styles */
            .file-tree {
                font-size: 0.95rem;
            }

            .file-tree .folder-toggle {
                color: var(--bs-body-color);
                padding: 0.5rem;
                border-radius: 0.25rem;
                margin-bottom: 0.25rem;
            }

            .file-tree .folder-toggle:hover {
                color: var(--bs-primary);
                background-color: rgba(var(--bs-primary-rgb), 0.05);
            }

            .file-tree .folder-toggle.collapsed .rotate-icon {
                transform: rotate(0deg);
            }

            .file-tree .folder-toggle:not(.collapsed) .rotate-icon {
                transform: rotate(90deg);
            }

            .file-tree .transition {
                transition: transform 0.2s ease-in-out;
            }

            .file-tree a.document-filter {
                padding: 0.5rem;
                transition: all 0.2s;
                border-radius: 0.25rem;
                margin: 0.125rem 0;
                display: flex;
                align-items: center;
            }

            .file-tree a.document-filter:hover {
                color: var(--bs-primary) !important;
                text-decoration: none;
                background-color: rgba(var(--bs-primary-rgb), 0.05);
            }

            .file-tree a.document-filter.active {
                color: var(--bs-primary) !important;
                background-color: rgba(var(--bs-primary-rgb), 0.1);
            }

            .file-tree .ti-folder {
                font-size: 1.1em;
            }

            .file-tree .ti-file-text {
                font-size: 1em;
                opacity: 0.75;
            }

            /* Card Styles */
            .card.border-0.shadow-sm {
                box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
            }

            /* Animation for folder toggle */
            .file-tree .collapse {
                transition: all 0.2s ease-out;
            }

            .file-tree .collapse:not(.show) {
                display: block;
                height: 0;
                overflow: hidden;
            }

            /* Status colors */
            .file-tree a.document-filter[data-status="active"] .ti-file-text {
                color: var(--bs-success);
            }

            .file-tree a.document-filter[data-status="draft"] .ti-file-text {
                color: var(--bs-warning);
            }

            .file-tree a.document-filter[data-status="archived"] .ti-file-text {
                color: var(--bs-secondary);
            }
        </style>
    @endpush

    @push('script-page')
        <script>
            $(document).ready(function() {
                // Initialize variables for document filtering
                let currentType = null;
                let currentStatus = null;

                // Initialize DataTable
                var table = $('#documents-table').DataTable({
                    serverSide: true,
                    processing: false,
                    language: {
                        emptyTable: "{{ __('No documents found') }}",
                        zeroRecords: "{{ __('No matching documents found') }}"
                    },
                    ajax: {
                        url: '{{ route('tenant.document.list') }}',
                        method: 'GET',
                        data: function(d) {
                            d.document_type = currentType;
                            d.status = currentStatus;
                        },
                        error: function(xhr, error, thrown) {
                            console.error('DataTables error:', error);
                            console.error('Server response:', xhr.responseText);
                            // alert("{{ __('Error loading documents. Please try again.') }}");
                        }
                    },
                    drawCallback: function(settings) {
                        // Hide processing indicator if no data
                        if (settings.json.data.length === 0) {
                            $('.dataTables_processing').hide();
                        }
                    },
                    columns: [{
                            data: 'document_number',
                            name: 'document_number'
                        },
                        {
                            data: 'title',
                            name: 'title'
                        },
                        {
                            data: 'department_id',
                            name: 'department'
                        },
                        {
                            data: 'version_badge',
                            name: 'documentVersion.version'
                        },
                        {
                            data: 'status_badge',
                            name: 'status'
                        },
                        {
                            data: null,
                            orderable: false,
                            render: function(data) {
                                return `
                            <div class="btn-group">
                                <a href="${data.preview_url}" class="btn btn-sm btn-info" title="{{ __('Preview') }}">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="${data.download_url}" class="btn btn-sm btn-success" title="{{ __('Download') }}">
                                    <i class="fas fa-download"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-bs-toggle="dropdown">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="#"><i class="fas fa-edit me-2"></i>{{ __('Edit') }}</a></li>
                                    <li><a class="dropdown-item" href="#"><i class="fas fa-history me-2"></i>{{ __('Versions') }}</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item text-danger" href="#"><i class="fas fa-trash me-2"></i>{{ __('Delete') }}</a></li>
                                </ul>
                            </div>
                        `;
                            }
                        }
                    ]
                });

                // Handle document filtering
                $('.document-filter').click(function(e) {
                    e.preventDefault();
                    currentType = $(this).data('type');
                    currentStatus = $(this).data('status');

                    // Remove active class from all filters
                    $('.document-filter').removeClass('active');
                    // Add active class to clicked filter
                    $(this).addClass('active');

                    // Get folder name and status
                    const folderName = $(this).closest('ul').siblings('.folder-toggle').find('span').text();
                    const status = $(this).text().trim();

                    // Update current folder text
                    $('#current-folder').text(folderName + ' - ' + status);

                    // Reload table
                    table.ajax.reload();
                });

                // Bootstrap collapse events to handle icon rotation
                $('.collapse').on('show.bs.collapse', function() {
                    $(this).siblings('.folder-toggle').removeClass('collapsed');
                }).on('hide.bs.collapse', function() {
                    $(this).siblings('.folder-toggle').addClass('collapsed');
                });

                // Check URL parameters to set initial active state
                const urlParams = new URLSearchParams(window.location.search);
                const type = urlParams.get('type');
                const status = urlParams.get('status');
                if (type && status) {
                    const $filter = $(`.document-filter[data-type="${type}"][data-status="${status}"]`);
                    if ($filter.length) {
                        // Show the parent collapse
                        $filter.closest('.collapse').collapse('show');
                        // Trigger the filter click
                        $filter.click();
                    }
                }
            });
        </script>
    @endpush
@endsection
