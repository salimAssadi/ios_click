@extends('tenant::layouts.app')

@section('page-title')
    {{ __('Supporting Document Categories') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('tenant.dashboard') }}">{{ __('Dashboard') }}</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('tenant.document.supporting-documents.index') }}">{{ __('Supporting Documents') }}</a>
    </li>
    <li class="breadcrumb-item active">
        {{ __('Supporting Document Categories') }}
    </li>
@endsection
@push('css-page')
<style>
    .border-dashed {
        border-style: dashed !important;
        border-width: 2px !important;
        border-color: var(--bs-primary) !important;
    }
    
    .add-category-card {
        background-color: rgba(var(--bs-primary-rgb), 0.05);
    }
    
    .notification-toast {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
</style>
@endpush
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ __('Supporting Document Categories') }}</h5>
                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                        <i class="ti ti-plus me-1"></i>{{ __('Add Category') }}
                    </button>
                </div>
                <div class="card-body">
                    <div id="categories-container">
                        @include('document::document.supporting-documents.partials.categories-list')
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Add Category Modal -->
    <div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCategoryModalLabel">{{ __('Add New Category') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addCategoryForm">
                        <div class="alert alert-danger d-none" id="addCategoryError"></div>
                        <div class="mb-3">
                            <label for="name_ar" class="form-label">{{ __('Category Name (Arabic)') }}</label>
                            <input type="text" class="form-control" id="name_ar" name="name_ar" required>
                        </div>
                        <div class="mb-3">
                            <label for="name_en" class="form-label">{{ __('Category Name (English)') }}</label>
                            <input type="text" class="form-control" id="name_en" name="name_en" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="button" class="btn btn-primary" id="saveCategory">{{ __('Save') }}</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Edit Category Modal -->
    <div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editCategoryModalLabel">{{ __('Edit Category') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editCategoryForm">
                        <div class="alert alert-danger d-none" id="editCategoryError"></div>
                        <input type="hidden" id="edit_category_id">
                        <div class="mb-3">
                            <label for="edit_name_ar" class="form-label">{{ __('Category Name (Arabic)') }}</label>
                            <input type="text" class="form-control" id="edit_name_ar" name="edit_name_ar" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_name_en" class="form-label">{{ __('Category Name (English)') }}</label>
                            <input type="text" class="form-control" id="edit_name_en" name="edit_name_en" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="button" class="btn btn-primary" id="updateCategory">{{ __('Update') }}</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script-page')
<script>
    // Define global variables for the category management script
    const CSRF_TOKEN = '{{ csrf_token() }}';
    const ROUTES = {
        category_store: '{{ route("tenant.document.categories.ajax-store") }}',
        category_update: '{{ route("tenant.document.categories.ajax-update", '') }}',
        categories_index: '{{ route("tenant.document.supporting-documents.index") }}'
    };
    const LANG_MESSAGES = {
        fill_required_fields: '{{ __('Please fill in all required fields') }}',
        error_occurred: '{{ __('An error occurred. Please try again.') }}',
        saving: '{{ __('Saving...') }}',
        updating: '{{ __('Updating...') }}'
    };
</script>

<!-- Load the category management script -->
<script src="{{ Module::asset('document:js/category-management.js') }}"></script>
@endpush
