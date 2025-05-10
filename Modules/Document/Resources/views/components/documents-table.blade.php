@props(['documentType' => null, 'title' => 'Documents List', 'relatedProcess' => null, 'categoryId' => null])

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-striped documents-datatable" id="documents-table">
                <thead class="thead-light">
                    <tr>
                        <th>{{ __('N') }}</th>
                        <th>{{ __('Document Number') }}</th>
                        <th>{{ __('Title') }}</th>
                        <th>{{ __('Category') }}</th>
                        <th>{{ __('Status') }}</th>

                        <th class="w-15 text-end">{{ __('Action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data will be loaded via AJAX -->
                </tbody>
            </table>
        </div>
    </div>

@push('script-page')
<script>
    $(function() {
        // Initialize DataTable with AJAX source
        let table = $('#documents-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('tenant.document.datatable') }}",
                data: function(d) {
                    // Add custom filters
                    d.document_type = "{{ $documentType }}";
                    d.related_process = "{{ $relatedProcess }}";
                    d.category_id = "{{ $categoryId }}";
                }
            },
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'document_number', name: 'document_number'},
                {data: 'title', name: 'title_en'},
                {data: 'category', name: 'category.title_en'},
                {data: 'status_badge', name: 'status.name', orderable: false, searchable: false},
                {
                    data: 'action', 
                    name: 'action', 
                    orderable: false, 
                    searchable: false,
                    className: 'text-end'
                },
            ],
            order: [[0, 'desc']], // Order by row index by default
            language: {
                search: "{{ __('Search') }}:",
                lengthMenu: "{{ __('Show _MENU_ entries') }}",
                info: "{{ __('Showing _START_ to _END_ of _TOTAL_ entries') }}",
                infoEmpty: "{{ __('Showing 0 to 0 of 0 entries') }}",
                infoFiltered: "{{ __('(filtered from _MAX_ total entries)') }}",
                loadingRecords: "{{ __('Loading...') }}",
                zeroRecords: "{{ __('No matching records found') }}",
                emptyTable: "{{ __('No data available in table') }}",
                paginate: {
                    first: "{{ __('First') }}",
                    previous: "{{ __('Previous') }}",
                    next: "{{ __('Next') }}",
                    last: "{{ __('Last') }}"
                },
            }
        });
        
        // Refresh table when needed
        window.refreshDocumentsTable = function() {
            table.ajax.reload();
        };
    });
</script>
@endpush
