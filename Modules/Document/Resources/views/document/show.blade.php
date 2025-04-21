@extends('tenant::layouts.app')

@section('title', __('Document Details'))

@section('content')
    <div class="row">
        <!-- Document Details Card -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5>{{ __('Document Details') }}</h5>
                        <div>
                            <a href="{{ route('tenant.document.requests.create', $document->id) }}" 
                               class="btn btn-primary btn-sm">
                                <i class="ti ti-plus"></i>
                                {{ __('New Request') }}
                            </a>
                            @can('edit_documents')
                            <a href="{{ route('tenant.document.edit', $document->id) }}" 
                               class="btn btn-warning btn-sm">
                                <i class="ti ti-pencil"></i>
                                {{ __('Edit') }}
                            </a>
                            @endcan
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">{{ __('Title') }}</label>
                        <p class="form-control-static">{{ $document->title }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Document Number') }}</label>
                        <p class="form-control-static">{{ $document->document_number ?? '-' }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Document Type') }}</label>
                        <p class="form-control-static">{{ $document->document_type }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Created By') }}</label>
                        <p class="form-control-static">{{ $document->creator->name }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Created At') }}</label>
                        <p class="form-control-static">{{ $document->created_at->format('Y-m-d H:i') }}</p>
                    </div>
                    @if($document->lastVersion)
                        <div class="mb-3">
                            <label class="form-label">{{ __('Current Version') }}</label>
                            <p class="form-control-static">
                                <span class="badge bg-info">v{{ number_format($document->lastVersion->version, 1) }}</span>
                                <a href="{{ route('tenant.document.serve', $document->id) }}" class="btn btn-sm btn-primary ms-2" target="_blank">
                                    <i class="fas fa-external-link-alt"></i> {{ __('Open Document') }}
                                </a>
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Versions Card -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">{{ __('Document Versions') }}</h3>
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#newVersionModal">
                        <i class="fas fa-plus"></i> {{ __('New Version') }}
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>{{ __('Version') }}</th>
                                    <th>{{ __('Created At') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Created By') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($document->versions as $version)
                                    <tr>
                                        <td>{{ $version->version }}</td>
                                        <td>{{ $version->created_at->format('Y-m-d') }}</td>
                                        <td>{!! $version->status_badge !!}</td>
                                        <td>{{ $version->created_by }}</td>
                                        <td class="text-right">
                                            <a href="{{ route('tenant.document.serve', ['id' => $document->id, 'version' => $version->id, 'preview' => true]) }}"
                                               class="btn btn-sm btn-info" 
                                               target="_blank"
                                               title="{{ __('Preview Document') }}">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('tenant.document.serve', ['id' => $document->id, 'version' => $version->id]) }}"
                                               class="btn btn-sm btn-primary" 
                                               title="{{ __('Download Document') }}">
                                                <i class="fas fa-download"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">{{ __('No versions available.') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- New Version Modal -->
    <div class="modal fade" id="newVersionModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="newVersionForm" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('Create New Version') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label required">{{ __('Change Notes') }}</label>
                            <textarea class="form-control" name="change_notes" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label required">{{ __('Document File') }}</label>
                            <input type="file" class="form-control" name="file" required>
                            <small class="text-muted">{{ __('Upload the new version of the document') }}</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('Create Version') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
$(function() {
    $('#newVersionForm').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: '{{ route("tenant.document.versions.store", $document->id) }}',
            method: 'POST',
            data: new FormData(this),
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    $('#newVersionModal').modal('hide');
                    window.location.reload();
                    toastr.success(response.message);
                }
            },
            error: function(xhr) {
                let message = xhr.responseJSON?.message || '{{ __("Error creating version") }}';
                toastr.error(message);
            }
        });
    });
});
</script>
@endpush
