@extends('tenant::layouts.app')

@section('title')
    {{ __('Backup Management') }}
@endsection

@section('content')
    <div class="pc-content">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col">
                                <h4>{{ __('Backup Management') }}</h4>
                            </div>
                            <div class="col-auto">
                                <button type="button" class="btn btn-primary" onclick="createBackup()">
                                    <i class="ti ti-download"></i> {{ __('Create Backup') }}
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="ti ti-info-circle"></i>
                            {{ __('Backups include your database and uploaded files. They are stored securely and can be downloaded or restored when needed.') }}
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover" id="backups-table">
                                <thead>
                                    <tr>
                                        <th>{{ __('File Name') }}</th>
                                        <th>{{ __('Size') }}</th>
                                        <th>{{ __('Created At') }}</th>
                                        <th>{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($backups as $backup)
                                        @php
                                            $fileInfo = pathinfo($backup);
                                            $size = Storage::disk('backups')->size($backup);
                                            $createdAt = Storage::disk('backups')->lastModified($backup);
                                        @endphp
                                        <tr>
                                            <td>{{ $fileInfo['basename'] }}</td>
                                            <td>{{ formatBytes($size) }}</td>
                                            <td>{{ date('Y-m-d H:i:s', $createdAt) }}</td>
                                            <td>
                                                <a href="{{ route('settings.backup.download', $fileInfo['basename']) }}" class="btn btn-sm btn-info">
                                                    <i class="ti ti-download"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-danger" onclick="deleteBackup('{{ $fileInfo['basename'] }}')">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Backup Progress Modal -->
    <div class="modal fade" id="backupProgressModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Creating Backup') }}</h5>
                </div>
                <div class="modal-body">
                    <div class="progress">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%"></div>
                    </div>
                    <p class="text-center mt-3" id="backupStatus">{{ __('Initializing backup...') }}</p>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#backups-table').DataTable({
        order: [[2, 'desc']] // Sort by created at
    });
});

function createBackup() {
    $('#backupProgressModal').modal('show');
    updateProgress(10, '{{ __("Starting backup process...") }}');

    $.ajax({
        url: '{{ route("settings.backup.create") }}',
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.success) {
                updateProgress(100, response.message);
                setTimeout(function() {
                    location.reload();
                }, 1500);
            }
        },
        error: function(xhr) {
            $('#backupProgressModal').modal('hide');
            toastr.error(xhr.responseJSON.message);
        }
    });
}

function deleteBackup(filename) {
    if (confirm('{{ __("Are you sure you want to delete this backup?") }}')) {
        $.ajax({
            url: '{{ route("settings.backup.delete", "") }}/' + filename,
            method: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    location.reload();
                }
            },
            error: function(xhr) {
                toastr.error(xhr.responseJSON.message);
            }
        });
    }
}

function updateProgress(percentage, status) {
    $('.progress-bar').css('width', percentage + '%');
    $('#backupStatus').text(status);
}

// Helper function to format bytes
function formatBytes(bytes, decimals = 2) {
    if (bytes === 0) return '0 Bytes';

    const k = 1024;
    const dm = decimals < 0 ? 0 : decimals;
    const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];

    const i = Math.floor(Math.log(bytes) / Math.log(k));

    return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
}
</script>
@endsection
