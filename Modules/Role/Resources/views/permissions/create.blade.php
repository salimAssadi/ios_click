@extends('tenant::layouts.app')
@section('page-title')
    {{ __('Create Permission') }}
@endsection
@push('css-page')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
@endpush
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5>{{ __('Create New Permission') }}</h5>
                    <a href="{{ route('tenant.role.permissions.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fa fa-arrow-left"></i> {{ __('Back to Permissions') }}
                    </a>
                </div>
                <div class="card-body ">
                    <form class="row"  action="{{ route('tenant.role.permissions.store') }}" method="POST">
                        @csrf
                        <div class="col-md-4">
                            <div class="form-group mb-3 ">
                                <label for="name">{{ __('Permission Name') }} <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                                <small class="form-text text-muted">{{ __('Suggested format: action-resource (e.g., create-users, edit-documents)') }}</small>
                                @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label for="guard_name">{{ __('Guard Name') }}</label>
                                <input type="text" name="guard_name" id="guard_name" class="form-control @error('guard_name') is-invalid @enderror" value="{{ old('guard_name', 'web') }}">
                                @error('guard_name')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                        </div>
                        </div>
                        
                        <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label for="module">{{ __('Module') }} <span class="text-danger">*</span></label>
                            <select id="module" name="module" class="form-control" required>
                                @foreach ($modules as $module)
                                    <option value="{{ $module }}" {{ old('module', $permission->module ?? '') == $module ? 'selected' : '' }}>
                                        {{ ucfirst($module) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('module')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        </div>
                        
                        <div class="form-group d-flex gap-2">
                            <button type="submit" class="btn btn-primary">{{ __('Create Permission') }}</button>
                            <button type="submit" name="add_new" value="1" class="btn btn-secondary">{{ __('Save and Add New') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script-page')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

<script>
    new TomSelect("#module", {
        create: true, // يسمح بكتابة خيارات جديدة
        persist: false,
        placeholder: '{{ __('Select or type a module') }}',
        maxOptions: 50
    });
</script>
@endpush
    
