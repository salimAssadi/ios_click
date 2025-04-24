@extends('tenant::layouts.app')
@section('page-title')
    {{ __('Create Permission') }}
@endsection
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5>{{ __('Create New Permission') }}</h5>
                    <a href="{{ route('tenant.role.permissions.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fa fa-arrow-left"></i> {{ __('Back to Permissions') }}
                    </a>
                </div>
                <div class="card-body">
                    <form action="{{ route('tenant.role.permissions.store') }}" method="POST">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="name">{{ __('Permission Name') }} <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                            <small class="form-text text-muted">{{ __('Suggested format: action-resource (e.g., create-users, edit-documents)') }}</small>
                            @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="guard_name">{{ __('Guard Name') }}</label>
                            <input type="text" name="guard_name" id="guard_name" class="form-control @error('guard_name') is-invalid @enderror" value="{{ old('guard_name', 'web') }}">
                            @error('guard_name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">{{ __('Create Permission') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
