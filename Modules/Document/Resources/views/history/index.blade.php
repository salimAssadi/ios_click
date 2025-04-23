@extends('tenant::layouts.app')

@section('page-title', __('Activity History'))
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="">{{ __('Dashboard') }}</a>
    </li>
    <li class="breadcrumb-item" aria-current="page">{{ __('Activity History') }}</li>
@endsection

@section('content')

<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-vcenter card-table" id="history-table">
                        <thead>
                            <tr>
                                <th>{{ __('Date') }}</th>
                                <th>{{ __('Document') }}</th>
                                <th>{{ __('Version') }}</th>
                                <th>{{ __('Action') }}</th>
                                <th>{{ __('Performed By') }}</th>
                                <th>{{ __('Details') }}</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script-page')
<script>
$(function() {
    $('#history-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("tenant.document.history.data") }}',
        columns: [
            { data: 'date', name: 'created_at' },
            { data: 'document_title', name: 'document.title' },
            { data: 'version_number', name: 'version.version' },
            { data: 'action_type', name: 'action_type' },
            { data: 'performed_by', name: 'performer.name' },
            { data: 'details', name: 'change_summary' }
        ],
        order: [[0, 'desc']],
        pageLength: 25
    });
});
</script>
@endpush
