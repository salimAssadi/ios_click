@extends('tenant::layouts.app')

@section('title')
    {{ __('Approval Workflow') }}
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/dataTables.bootstrap5.min.css') }}">
@endsection

@section('content')
   
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h4>{{ __('Document Workflow') }}</h4>
                    </div>
                    <div class="card-body">
                        <!-- Workflow Tabs -->
                        <ul class="nav nav-tabs mb-3" id="workflowTabs" role="tablist">
                            @foreach($statuses as $status)
                                <li class="nav-item">
                                    <a class="nav-link {{ $activeTab == $status->code ? 'active' : '' }}"
                                       href="{{ route('tenant.document.workflow.index', ['tab' => $status->code]) }}">
                                        {{ __($status->name_en) }}
                                        <span class="badge {{ $status->badge }}">{{ $counts[$status->code] ?? 0 }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>

                        <!-- Documents Table -->
                        <div class="table-responsive">
                            <table class="table table-hover" id="workflow-table">
                                <thead>
                                    <tr>
                                        <th>{{ __('Document') }}</th>
                                        <th>{{ __('Version') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th>{{ __('Created By') }}</th>
                                        <th>{{ __('Assigned To') }}</th>
                                        <th>{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>

                       
                    </div>
                </div>
            </div>
        </div>

    <!-- Status Update Modal -->
    <div class="modal fade" id="statusModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="statusForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('Update Document Status') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        
                        <div class="form-group">
                            <label for="notes">{{ __('Notes') }}</label>
                            <textarea name="notes" id="notes" class="form-control" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('Update Status') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script-page')
    <script src="{{ asset('assets/js/plugins/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/dataTables.bootstrap5.min.js') }}"></script>
    <script>
    $(document).ready(function() {
        var table = $('#workflow-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route("tenant.document.workflow.data") }}',
                data: function(d) {
                    d.tab = '{{ $activeTab }}';
                }
            },
            columns: [
                {data: 'title', name: 'title'},
                {data: 'version', name: 'version'},
                {data: 'status', name: 'status'},
                {data: 'created_by', name: 'created_by'},
                {data: 'assigned_to', name: 'assigned_to'},
                {data: 'actions', name: 'actions', orderable: false, searchable: false}
            ],
            order: [[4, 'desc']],
            language: {
                processing: '',
                search: '{{ __("Search") }}:',
                lengthMenu: '{{ __("Show _MENU_ entries") }}',
                info: '{{ __("Showing _START_ to _END_ of _TOTAL_ entries") }}',
                infoEmpty: '{{ __("Showing 0 to 0 of 0 entries") }}',
                infoFiltered: '{{ __("(filtered from _MAX_ total entries)") }}',
                loadingRecords: '{{ __("Loading...") }}',
                zeroRecords: '{{ __("No matching records found") }}',
                emptyTable: '{{ __("No data available in table") }}',
                paginate: {
                    first: '{{ __("First") }}',
                    previous: '{{ __("Previous") }}',
                    next: '{{ __("Next") }}',
                    last: '{{ __("Last") }}'
                }
            },
            drawCallback: function() {
                $('[data-bs-toggle="tooltip"]').tooltip();
            }
        });

        $('#statusForm').on('submit', function(e) {
            e.preventDefault();
            var form = $(this);
            var url = form.attr('action');
            
            $.ajax({
                url: url,
                method: 'POST',
                data: form.serialize(),
                success: function(response) {
                    $('#statusModal').modal('hide');
                    // table.ajax.reload();
                    window.location.reload();
                    notifier.show('Success!',
                            response.message,
                            'success',
                            successImg, 4000);
                },
                error: function(xhr) {
                    notifier.show('Error!',
                            xhr.responseJSON.message ||
                            Lang.get('An error occurred while creating the document'),
                            'error',
                            errorImg, 4000);
                }
            });
        });

        $(document).on('click', '.status-update', function() {
            var documentId = $(this).data('document-id');
            var status = $(this).data('status');
            var requestTypeCode = $(this).data('request-type-code');
            var form = $('#statusForm');
            
            form.attr('action', '{{ route("tenant.document.workflow.status", ["document" => "_id_"]) }}'.replace('_id_', documentId));
            
            var statusInput = form.find('input[name="status"]');
            if (statusInput.length) {
                statusInput.val(status);
            } else {
                form.append('<input type="hidden" name="status" value="' + status + '">');
            }

            var requestTypeInput = form.find('input[name="request_type_code"]');
            if (requestTypeInput.length) {
                requestTypeInput.val(requestTypeCode);
            } else {
                form.append('<input type="hidden" name="request_type_code" value="' + requestTypeCode + '">');
            }
        });

        $('#statusModal').on('hidden.bs.modal', function() {
            $('#statusForm').trigger('reset');
            var statusInput = $('#statusForm').find('input[name="status"]');
            if (statusInput.length) {
                statusInput.remove();
            }
            var requestTypeInput = $('#statusForm').find('input[name="request_type_code"]');
            if (requestTypeInput.length) {
                requestTypeInput.remove();
            }
        });
    });
    </script>
@endpush
