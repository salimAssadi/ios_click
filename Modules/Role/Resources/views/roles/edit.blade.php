@extends('tenant::layouts.app')
@section('page-title')
    {{ __('Edit Role') }}
@endsection
@section('content')
<div class="container">
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

                        <div class="form-group mb-3">
                            <label>{{ __('Permissions') }}</label>
                            <div class="row">
                                @foreach($permissions as $permission)
                                    <div class="col-md-3 mb-2">
                                        <div class="form-check">
                                            <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" class="form-check-input" id="permission_{{ $permission->id }}" 
                                                {{ in_array($permission->id, $rolePermissions) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="permission_{{ $permission->id }}">
                                                {{ $permission->name }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">{{ __('Update Role') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
