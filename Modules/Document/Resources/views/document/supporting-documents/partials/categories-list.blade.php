@if(count($categories) > 0)
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        @foreach($categories as $category)
            <div class="col category-item" data-category-id="{{ $category->id }}">
                <div class="card h-100 category-card shadow-sm">
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex justify-content-between mb-2">
                            <div class="category-icon">
                                <i class="ti ti-folder text-primary" style="font-size: 2.5rem;"></i>
                            </div>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-icon" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="ti ti-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item edit-category" href="javascript:void(0);" 
                                           data-id="{{ $category->id }}" 
                                           data-title-ar="{{ $category->title_ar }}" 
                                           data-title-en="{{ $category->title_en }}">
                                            <i class="ti ti-edit me-2"></i>{{ __('Edit') }}
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <h5 class="card-title text-primary mb-2">{{ $category->title }}</h5>
                        <div class="d-flex align-items-center mt-auto">
                            <span class="badge bg-light-primary text-primary">{{ __('Documents') }}: {{ $category->documents_count }}</span>
                            <a href="{{ route('tenant.document.supporting-documents.category-detail', $category->id) }}" class="ms-auto btn btn-sm btn-light-primary">
                                {{ __('Show Documents') }} <i class="ti ti-chevron-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
        
        <!-- Add Category Card -->
        <div class="col">
            <div class="card h-100 add-category-card shadow-sm border-dashed" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                <div class="card-body d-flex flex-column align-items-center justify-content-center">
                    <div class="category-icon mb-3">
                        <i class="ti ti-plus text-primary" style="font-size: 2.5rem;"></i>
                    </div>
                    <h5 class="card-title text-primary mb-2">{{ __('Add New Category') }}</h5>
                    <p class="text-muted text-center">{{ __('Click to add a new supporting document category') }}</p>
                </div>
            </div>
        </div>
    </div>
@else
    <div class="text-center py-5">
        <div class="mb-3">
            <i class="ti ti-folder-off text-secondary" style="font-size: 3rem;"></i>
        </div>
        <h6 class="text-muted">{{ __('No categories found') }}</h6>
        <p class="text-body-secondary">{{ __('There are no supporting document categories available.') }}</p>
        <button type="button" class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
            <i class="ti ti-plus me-1"></i>{{ __('Add Category') }}
        </button>
    </div>
@endif
