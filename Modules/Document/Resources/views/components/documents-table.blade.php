@props([
    'documentType' => null, 
    'title' => 'Documents List', 
    'relatedProcess' => null, 
    'categoryId' => null,
    'customColumns' => [],
    'filters' => []
])
    
    <div class="card-body">
        <x-document::dynamic-filter :filters="$filters" />
        <div class="table-responsive">
            <table class="table table-hover table-striped documents-datatable" id="documents-table">
                <thead class="thead-light">
                    <tr>
                        <th>{{ __('N') }}</th>
                        <th>{{ __('Title') }}</th>
                        {{-- إضافة الأعمدة المخصصة إذا وجدت --}}
                        @foreach($customColumns as $column)
                            <th>{{ __($column['title']) }}</th>
                        @endforeach
                        <th>{{ __('Category') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Version') }}</th>
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
                    // Add dynamic filters
                    $('#dynamic-filters .filter-input').each(function() {
                        d[$(this).attr('name')] = $(this).val();
                    });
                    // Add custom filters
                    d.document_type = "{{ $documentType }}";
                    d.related_process = "{{ $relatedProcess }}";
                    d.category_id = "{{ $categoryId }}";
                    @if(count($customColumns) > 0)
                        d.custom_columns = @json($customColumns);
                    @endif
                }
            },
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'title', name: 'title_en'},
                
                @foreach($customColumns as $column)
                // إضافة تعريف كل عمود مخصص
                {data: '{{ $column["data"] }}', name: '{{ $column["name"] ?? $column["data"] }}', orderable: {{ $column["orderable"] ?? 'true' }}, searchable: {{ $column["searchable"] ?? 'true' }}},
                @endforeach
                
                {data: 'category', name: 'category.title_en'},
                {data: 'status_badge', name: 'status.name', orderable: false, searchable: false},
                {data: 'version', name: 'version', orderable: false, searchable: false},
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
        
        // Reload table when any filter changes
        $(document).on('change', '#dynamic-filters .filter-input', function() {
            table.ajax.reload();
        });
        // Special: reload when custom_days changes and custom_period is selected
        $(document).on('input', 'input[name="custom_days"]', function() {
            if ($('input[name="expiry_filter"]:checked').val() === 'custom_period') {
                table.ajax.reload();
            }
        });
        // Refresh table when needed
        window.refreshDocumentsTable = function() {
            table.ajax.reload();
        };
    });
</script>
@endpush
