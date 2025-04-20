@extends('tenant::layouts.app')

@section('title', __('Document Versions'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Document Versions') }}</h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="row">
                            <div class="col-md-3">
                                <select id="document-filter" class="form-control">
                                    <option value="">{{ __('All Documents') }}</option>
                                    @foreach($documents as $doc)
                                        <option value="{{ $doc->id }}">{{ $doc->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select id="status-filter" class="form-control">
                                    <option value="">{{ __('All Statuses') }}</option>
                                    <option value="draft">{{ __('Draft') }}</option>
                                    <option value="under_review">{{ __('Under Review') }}</option>
                                    <option value="approved">{{ __('Approved') }}</option>
                                    <option value="modified">{{ __('Modified') }}</option>
                                    <option value="obsolete">{{ __('Obsolete') }}</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="versions-table" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>{{ __('Document') }}</th>
                                    <th>{{ __('Version') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Dates') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script>
$(function() {
    let table = $('#versions-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("document.versions.data") }}',
            data: function(d) {
                d.document_id = $('#document-filter').val();
                d.status = $('#status-filter').val();
            }
        },
        columns: [
            { data: 'document_info', name: 'document_info' },
            { data: 'version_info', name: 'version_info' },
            { data: 'status_badge', name: 'status' },
            { data: 'dates', name: 'dates' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        order: [[1, 'desc']]
    });

    $('#document-filter, #status-filter').change(function() {
        table.draw();
    });
});
</script>
@endpush
