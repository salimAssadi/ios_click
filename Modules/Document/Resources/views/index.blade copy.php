@extends('tenant::layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Document Tree -->
        <div class="col-md-3">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">{{ __('Document Structure') }}</h5>
                    <button class="btn btn-link btn-sm p-0" id="expand-all">
                        <i class="fas fa-expand-alt"></i>
                    </button>
                </div>
                <div class="card-body p-0">
                    <div id="document-tree" class="tree-container">
                        <ul class="tree">
                            <!-- Procedures -->
                            <li>
                                <div class="tree-item">
                                    <button class="btn-toggle">
                                        <i class="fas fa-chevron-right"></i>
                                    </button>
                                    <i class="fas fa-folder text-warning me-2"></i>
                                    <span>{{ __('Procedures') }}</span>
                                    <span class="badge bg-secondary ms-2">0</span>
                                </div>
                                <ul>
                                    <li>
                                        <div class="tree-item">
                                            <button class="btn-toggle">
                                                <i class="fas fa-chevron-right"></i>
                                            </button>
                                            <i class="fas fa-folder text-info me-2"></i>
                                            <span>{{ __('Active') }}</span>
                                        </div>
                                        <ul class="document-list" data-type="procedure" data-status="active"></ul>
                                    </li>
                                    <li>
                                        <div class="tree-item">
                                            <button class="btn-toggle">
                                                <i class="fas fa-chevron-right"></i>
                                            </button>
                                            <i class="fas fa-folder text-warning me-2"></i>
                                            <span>{{ __('Drafts') }}</span>
                                        </div>
                                        <ul class="document-list" data-type="procedure" data-status="draft"></ul>
                                    </li>
                                    <li>
                                        <div class="tree-item">
                                            <button class="btn-toggle">
                                                <i class="fas fa-chevron-right"></i>
                                            </button>
                                            <i class="fas fa-folder text-secondary me-2"></i>
                                            <span>{{ __('Archived') }}</span>
                                        </div>
                                        <ul class="document-list" data-type="procedure" data-status="archived"></ul>
                                    </li>
                                </ul>
                            </li>

                            <!-- Policies -->
                            <li>
                                <div class="tree-item">
                                    <button class="btn-toggle">
                                        <i class="fas fa-chevron-right"></i>
                                    </button>
                                    <i class="fas fa-folder text-primary me-2"></i>
                                    <span>{{ __('Policies') }}</span>
                                    <span class="badge bg-secondary ms-2">0</span>
                                </div>
                                <ul>
                                    <li>
                                        <div class="tree-item">
                                            <button class="btn-toggle">
                                                <i class="fas fa-chevron-right"></i>
                                            </button>
                                            <i class="fas fa-folder text-info me-2"></i>
                                            <span>{{ __('Active') }}</span>
                                        </div>
                                        <ul class="document-list" data-type="policy" data-status="active"></ul>
                                    </li>
                                    <li>
                                        <div class="tree-item">
                                            <button class="btn-toggle">
                                                <i class="fas fa-chevron-right"></i>
                                            </button>
                                            <i class="fas fa-folder text-warning me-2"></i>
                                            <span>{{ __('Drafts') }}</span>
                                        </div>
                                        <ul class="document-list" data-type="policy" data-status="draft"></ul>
                                    </li>
                                    <li>
                                        <div class="tree-item">
                                            <button class="btn-toggle">
                                                <i class="fas fa-chevron-right"></i>
                                            </button>
                                            <i class="fas fa-folder text-secondary me-2"></i>
                                            <span>{{ __('Archived') }}</span>
                                        </div>
                                        <ul class="document-list" data-type="policy" data-status="archived"></ul>
                                    </li>
                                </ul>
                            </li>

                            <!-- Instructions -->
                            <li>
                                <div class="tree-item">
                                    <button class="btn-toggle">
                                        <i class="fas fa-chevron-right"></i>
                                    </button>
                                    <i class="fas fa-folder text-success me-2"></i>
                                    <span>{{ __('Instructions') }}</span>
                                    <span class="badge bg-secondary ms-2">0</span>
                                </div>
                                <ul>
                                    <li>
                                        <div class="tree-item">
                                            <button class="btn-toggle">
                                                <i class="fas fa-chevron-right"></i>
                                            </button>
                                            <i class="fas fa-folder text-info me-2"></i>
                                            <span>{{ __('Active') }}</span>
                                        </div>
                                        <ul class="document-list" data-type="instruction" data-status="active"></ul>
                                    </li>
                                    <li>
                                        <div class="tree-item">
                                            <button class="btn-toggle">
                                                <i class="fas fa-chevron-right"></i>
                                            </button>
                                            <i class="fas fa-folder text-warning me-2"></i>
                                            <span>{{ __('Drafts') }}</span>
                                        </div>
                                        <ul class="document-list" data-type="instruction" data-status="draft"></ul>
                                    </li>
                                    <li>
                                        <div class="tree-item">
                                            <button class="btn-toggle">
                                                <i class="fas fa-chevron-right"></i>
                                            </button>
                                            <i class="fas fa-folder text-secondary me-2"></i>
                                            <span>{{ __('Archived') }}</span>
                                        </div>
                                        <ul class="document-list" data-type="instruction" data-status="archived"></ul>
                                    </li>
                                </ul>
                            </li>

                            <!-- Samples -->
                            <li>
                                <div class="tree-item">
                                    <button class="btn-toggle">
                                        <i class="fas fa-chevron-right"></i>
                                    </button>
                                    <i class="fas fa-folder text-danger me-2"></i>
                                    <span>{{ __('Samples') }}</span>
                                    <span class="badge bg-secondary ms-2">0</span>
                                </div>
                                <ul>
                                    <li>
                                        <div class="tree-item">
                                            <button class="btn-toggle">
                                                <i class="fas fa-chevron-right"></i>
                                            </button>
                                            <i class="fas fa-folder text-info me-2"></i>
                                            <span>{{ __('Active') }}</span>
                                        </div>
                                        <ul class="document-list" data-type="sample" data-status="active"></ul>
                                    </li>
                                    <li>
                                        <div class="tree-item">
                                            <button class="btn-toggle">
                                                <i class="fas fa-chevron-right"></i>
                                            </button>
                                            <i class="fas fa-folder text-warning me-2"></i>
                                            <span>{{ __('Drafts') }}</span>
                                        </div>
                                        <ul class="document-list" data-type="sample" data-status="draft"></ul>
                                    </li>
                                    <li>
                                        <div class="tree-item">
                                            <button class="btn-toggle">
                                                <i class="fas fa-chevron-right"></i>
                                            </button>
                                            <i class="fas fa-folder text-secondary me-2"></i>
                                            <span>{{ __('Archived') }}</span>
                                        </div>
                                        <ul class="document-list" data-type="sample" data-status="archived"></ul>
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
                            <small class="text-muted" id="document-count"></small>
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
                            <tbody>
                                <!-- Documents will be loaded here via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.tree-container {
    padding: 1rem;
    max-height: calc(100vh - 200px);
    overflow-y: auto;
}

.tree {
    list-style: none;
    padding: 0;
    margin: 0;
}

.tree ul {
    list-style: none;
    padding-left: 2rem;
    margin: 0;
    display: none;
}

.tree ul.show {
    display: block;
}

.tree-item {
    display: flex;
    align-items: center;
    padding: 0.5rem;
    cursor: pointer;
    border-radius: 4px;
    transition: background-color 0.2s;
}

.tree-item:hover {
    background-color: rgba(0, 0, 0, 0.05);
}

.tree-item.active {
    background-color: rgba(var(--bs-primary-rgb), 0.1);
}

.btn-toggle {
    background: none;
    border: none;
    padding: 0;
    width: 20px;
    height: 20px;
    margin-right: 0.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: transform 0.2s;
}

.btn-toggle.rotated {
    transform: rotate(90deg);
}

.document-item {
    padding-left: 2.5rem;
    margin: 0.25rem 0;
}

.document-item i {
    margin-right: 0.5rem;
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize DataTable
    var table = $('#documents-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("tenant.document.list") }}',
            data: function(d) {
                d.document_type = currentType;
                d.status = currentStatus;
            }
        },
        columns: [
            { data: 'document_number', name: 'document_number' },
            { data: 'title', name: 'title' },
            { data: 'department', name: 'department' },
            { data: 'version_badge', name: 'version' },
            { data: 'status_badge', name: 'status' },
            { 
                data: null,
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
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#"><i class="fas fa-edit me-2"></i>{{ __('Edit') }}</a></li>
                                <li><a class="dropdown-item" href="#"><i class="fas fa-history me-2"></i>{{ __('Versions') }}</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="#"><i class="fas fa-trash me-2"></i>{{ __('Delete') }}</a></li>
                            </ul>
                        </div>
                    `;
                }
            }
        ],
        drawCallback: function(settings) {
            var api = this.api();
            $('#document-count').text('(' + api.page.info().recordsTotal + ')');
        }
    });

    // Tree view functionality
    let currentType = null;
    let currentStatus = null;

    $('.tree-item').click(function(e) {
        if (!$(e.target).is('.btn-toggle')) {
            const $li = $(this).closest('li');
            const $ul = $li.children('ul');
            const $btn = $(this).find('.btn-toggle');
            
            if ($ul.length) {
                $ul.toggleClass('show');
                $btn.toggleClass('rotated');
            }

            // Update active state
            $('.tree-item').removeClass('active');
            $(this).addClass('active');

            // Update current folder text
            const folderName = $(this).find('span').first().text();
            $('#current-folder').text(folderName);
        }
    });

    $('.btn-toggle').click(function(e) {
        e.stopPropagation();
        const $li = $(this).closest('li');
        const $ul = $li.children('ul');
        
        $ul.toggleClass('show');
        $(this).toggleClass('rotated');
    });

    // Expand/Collapse all
    $('#expand-all').click(function() {
        const $icon = $(this).find('i');
        const isExpanded = $icon.hasClass('fa-compress-alt');
        
        if (isExpanded) {
            $('.tree ul').removeClass('show');
            $('.btn-toggle').removeClass('rotated');
            $icon.removeClass('fa-compress-alt').addClass('fa-expand-alt');
        } else {
            $('.tree ul').addClass('show');
            $('.btn-toggle').addClass('rotated');
            $icon.removeClass('fa-expand-alt').addClass('fa-compress-alt');
        }
    });

    // Load document counts
    function updateDocumentCounts() {
        $.get('{{ route("tenant.document.counts") }}', function(data) {
            Object.keys(data).forEach(type => {
                const $badge = $(`.tree-item:contains("${type}") .badge`);
                $badge.text(data[type].total);
            });
        });
    }

    // Initial load
    updateDocumentCounts();
});
</script>
@endpush
@endsection
