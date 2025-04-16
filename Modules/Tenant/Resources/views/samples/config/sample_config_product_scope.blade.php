@if ($configdata['products'])
    @php
     $products=$configdata['products'];
     $$parents=$configdata['products'];
    
    @endphp;
    
<form id="productForm" action="{{ route('iso_dic.samples.product-scope.store') }}"
method="POST">
@csrf
<table class="table table-bordered" id="productTable">
    <thead>
        <tr>
            <th>{{ __('Product or Service Name') }}</th>
            <th>{{ __('Parent Product') }}</th>
            <th style="width: 50px;">{{ __('Action') }}</th>
        </tr>
    </thead>
    <tbody>
        <!-- Loop through existing products -->
        @foreach ($products as $index => $product)
            <tr>
                <td>
                    <input type="text"
                        name="products[{{ $index }}][name]"
                        class="form-control" value="{{ $product->name }}"
                        placeholder="{{ __('Enter Product or Service Name') }}"
                        required>
                    <input type="hidden"
                        name="products[{{ $index }}][id]"
                        value="{{ $product->id }}">
                </td>
                <td>
                    <select name="products[{{ $index }}][parent_id]"
                        class="form-control showsearch">
                        <option value="">{{ __('No Parent') }}</option>
                        @foreach ($parents as $parent)
                            <option value="{{ $parent->id }}"
                                {{ $product->parent_id == $parent->id ? 'selected' : '' }}>
                                {{ $parent->name }}
                            </option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <button type="button" class="btn btn-danger remove-row"
                        data-id="{{ $product->id }}">
                        <i class="ti ti-trash"></i>
                    </button>
                </td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="2">
                <button type="submit"
                    class="btn btn-primary">{{ __('Save') }}</button>

            </td>
            <td>
                <button type="button" class="btn btn-success" id="addRow"><i
                        class="ti ti-plus"></i></button>
            </td>
        </tr>
    </tfoot>
</table>

</form>
@push('script-page')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            let rowCount = {{ $products->count() }};

            // Add a new row
            $('#addRow').on('click', function() {
                const newRow = `
                    <tr>
                        <td>
                            <input type="text" name="products[${rowCount}][name]" class="form-control" placeholder="{{ __('Enter Product or Service Name') }}" required>
                        </td>
                        <td>
                            <select name="products[${rowCount}][parent_id]" class="form-control showsearch">
                                <option value="">{{ __('No Parent') }}</option>
                                @foreach ($parents as $parent)
                                    <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <button type="button" class="btn btn-danger remove-row">
                                <i class="ti ti-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
                $('#productTable tbody').append(newRow);
                rowCount++;
            });

            // Remove a row
            $(document).on('click', '.remove-row', function() {
                const productId = $(this).data('id');
                const productName = $(this).closest('tr').find('input[name^="products"]')
                    .val(); // Get product name for SweetAlert2

                if (productId) {
                    Swal.fire({
                        title: "{{ __('Are you sure you want to delete this product?') }}",
                        text: "{{ __('The product :product cannot be restored after deletion. Do you want to confirm?') }}"
                            .replace(':product', productName),
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#3085d6",
                        cancelButtonColor: "#d33",
                        confirmButtonText: "{{ __('Yes') }}",
                        cancelButtonText: "{{ __('Cancel') }}",
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: `/iso_dic/samples/product-scope/${productId}`,
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                success: function() {
                                    $(this).closest('tr')
                                        .remove(); // Remove the row from the table
                                    Swal.fire({
                                        title: "{{ __('Deleted!') }}",
                                        text: "{{ __('The product has been deleted successfully.') }}",
                                        icon: "success",
                                        timer: 2000,
                                        showConfirmButton: false,
                                    });
                                }.bind(this), // Bind `this` to the current context
                                error: function() {
                                    Swal.fire({
                                        title: "{{ __('Error!') }}",
                                        text: "{{ __('Failed to delete the product.') }}",
                                        icon: "error",
                                        timer: 2000,
                                        showConfirmButton: false,
                                    });
                                }
                            });
                        }
                    });
                } else {
                    // If no ID, just remove the row (newly added row)
                    $(this).closest('tr').remove();
                }
            });



        });
    </script>
@endpush
@endif
