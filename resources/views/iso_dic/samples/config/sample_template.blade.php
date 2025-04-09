@extends('layouts.admin-app')
@section('page-title')
    {{ $pageTitle }}
@endsection

@push('script-page')
    <script src="{{ asset('assets/js/plugins/tinymce/tinymce.min.js') }}"></script>
@endpush

@section('breadcrumb')
    <ul class="breadcrumb mb-0">
        <li class="breadcrumb-item">
            <a href="{{ route('home') }}">{{ __('Dashboard') }}</a>
        </li>
        <li class="breadcrumb-item">
            <a href="">{{ __('ISO Systems') }}</a>
        </li>
        <li class="breadcrumb-item active">
            <a href="#"> {{ $pageTitle }} </a>
        </li>
    </ul>
@endsection
@section('content')
    <div class="row">
        <div class="col-sm-12">

            <div class="card">
                <div class="card-header">

                    <div class="row align-items-center g-2">
                        <div class="col">
                            <h5>
                                {{ $pageTitle }}
                            </h5>
                        </div>
                        <div class="col-auto">
                            <ul class="nav nav-tabs" id="myTab" role="tablist">

                                {{-- scope-tab --}}
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="menual_config-tab" data-bs-toggle="tab"
                                        data-bs-target="#menual_config" type="button" role="tab"
                                        aria-controls="menual_config" aria-selected="true">
                                        {{ __('Menual Configrature') }}
                                    </button>
                                </li>

                                {{-- scope-tab --}}
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link " id="upload_file-tab" data-bs-toggle="tab"
                                        data-bs-target="#upload_file" type="button" role="tab"
                                        aria-controls="upload_file" aria-selected="false">
                                        {{ __('Upload File') }}
                                    </button>
                                </li>
                                <li class="nav-item  px-2" role="presentation">
                                    <a href="{{ route('iso_dic.samples.configure', ['id' => Crypt::encrypt($id)]) . '?config=editor' }}" class="btn btn-secondary customModal" data-size="lg"
                                     data-url=""                                        
                                     data-title="{{ __('Create Sample') }}">
                                        {{ __('Editor') }}
                                    </a>
                                </li>
                            </ul>

                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="email-body">
                            <div class="card">
                                <div class="tab-content mt-3" id="myTabContent">

                                    <div class="tab-pane fade  show active" id="menual_config" role="tabpanel"
                                        aria-labelledby="menual_config-tab">
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

                                    </div>



                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

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
