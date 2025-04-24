@extends('tenant::layouts.app')
@section('page-title')
    {{ __('Edit Role') }}
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5>{{ __('Edit Role') }}: {{ $role->name }}</h5>
                    <a href="{{ route('tenant.role.roles.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fa fa-arrow-left"></i> {{ __('Back to Roles') }}
                    </a>
                </div>
                <div class="card-body">
                    <form action="{{ route('tenant.role.roles.update', $role->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group mb-3">
                            <label for="name">{{ __('Role Name') }} <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $role->name) }}" required>
                            @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="guard_name">{{ __('Guard Name') }}</label>
                            <input type="text" name="guard_name" id="guard_name" class="form-control @error('guard_name') is-invalid @enderror" value="{{ old('guard_name', $role->guard_name) }}">
                            @error('guard_name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            @if (!empty($permissions))
                                <h6 class="my-3">{{ __('Update Role Permissions') }}</h6>
                        
                                <div class="accordion" id="permissionAccordion">
                                    @foreach ($permissions as $module => $modulePermissions)
                                        <div class="card mb-2">
                                            <div class="card-header" id="heading_{{ $loop->index }}">
                                                <h2 class="mb-0 d-flex justify-content-between align-items-center">
                                                    <button class="btn btn-link text-start w-100 text-decoration-none" type="button" data-bs-toggle="collapse"
                                                        data-bs-target="#collapse_{{ $loop->index }}" aria-expanded="{{ $loop->first ? 'true' : 'false' }}"
                                                        aria-controls="collapse_{{ $loop->index }}">
                                                        <strong>{{ ucfirst($module) }}</strong>
                                                    </button>
                                                    <div class="form-check me-3">
                                                        <input type="checkbox" class="form-check-input check-module" data-module-index="{{ $loop->index }}">
                                                    </div>
                                                </h2>
                                            </div>
                        
                                            <div id="collapse_{{ $loop->index }}" class="collapse {{ $loop->first ? 'show' : '' }}" aria-labelledby="heading_{{ $loop->index }}"
                                                data-bs-parent="#permissionAccordion">
                                                <div class="card-body">
                                                    <div class="row">
                                                        @foreach ($modulePermissions as $permission)
                                                            <div class="col-md-4 mb-2">
                                                                <div class="form-check">
                                                                    <input type="checkbox"
                                                                        name="permissions[]"
                                                                        value="{{ $permission->id }}"
                                                                        class="form-check-input perm-checkbox perm-module-{{ $loop->parent->index }}"
                                                                        id="perm_{{ $permission->id }}"
                                                                        {{ in_array($permission->id, array_keys($rolePermissions)) ? 'checked' : '' }}>
                                                                    <label class="form-check-label" for="perm_{{ $permission->id }}">
                                                                        {{ $permission->name }}
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        
                        

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">{{ __('Update Role') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script-page')
<script>
    document.getElementById('checkall')?.addEventListener('change', function () {
        const allCheckboxes = document.querySelectorAll('.perm-checkbox');
        allCheckboxes.forEach(cb => cb.checked = this.checked);
        
        document.querySelectorAll('.check-module').forEach(mod => {
            mod.checked = this.checked;
        });
    });

    document.querySelectorAll('.check-module').forEach(moduleCheckbox => {
        const index = moduleCheckbox.getAttribute('data-module-index');

        moduleCheckbox.addEventListener('change', function () {
            const checkboxes = document.querySelectorAll(`.perm-module-${index}`);
            checkboxes.forEach(cb => cb.checked = this.checked);
        });
    });
</script>
@endpush



