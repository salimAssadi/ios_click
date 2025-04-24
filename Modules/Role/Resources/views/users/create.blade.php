@extends('tenant::layouts.app')
@section('page-title')
    {{ __('Create New User') }}
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5>{{ __('Create New User') }}</h5>
                    <a href="{{ route('tenant.role.users.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fa fa-arrow-left"></i> {{ __('Back to Users') }}
                    </a>
                </div>
                <div class="card-body">
                    <form action="{{ route('tenant.role.users.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="mb-3">{{ __('User Information') }}</h4>
                                
                                <div class="form-group mb-3">
                                    <label for="name">{{ __('Full Name') }} <span class="text-danger">*</span></label>
                                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                                    <small class="form-text text-muted">{{ __('Enter first and last name') }}</small>
                                    @error('name')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="email">{{ __('Email Address') }} <span class="text-danger">*</span></label>
                                    <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                                    @error('email')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="phone">{{ __('Phone Number') }}</label>
                                    <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}">
                                    @error('phone')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="password">{{ __('Password') }} <span class="text-danger">*</span></label>
                                    <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" required>
                                    <small class="form-text text-muted">{{ __('Minimum 8 characters') }}</small>
                                    @error('password')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="password_confirmation">{{ __('Confirm Password') }} <span class="text-danger">*</span></label>
                                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label for="status">{{ __('Status') }} <span class="text-danger">*</span></label>
                                    <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                                    </select>
                                    @error('status')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <h4 class="mb-3">{{ __('Employee Information') }}</h4>
                                
                                <div class="form-group mb-3">
                                    <label for="department">{{ __('Department') }}</label>
                                    <select id="department" class="form-control" onchange="loadPositions()">
                                        <option value="">{{ __('Select Department') }}</option>
                                        @foreach($departments as $department)
                                            <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                                {{ $department->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label for="position_id">{{ __('Position') }} <span class="text-danger">*</span></label>
                                    <select name="position_id" id="position_id" class="form-control @error('position_id') is-invalid @enderror" required>
                                        <option value="">{{ __('Select Position') }}</option>
                                        @foreach($positions as $position)
                                            <option value="{{ $position->id }}" 
                                                data-department="{{ $position->department_id }}"
                                                {{ old('position_id') == $position->id ? 'selected' : '' }}
                                                class="position-option">
                                                {{ $position->name }} ({{ $position->department->name }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('position_id')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label>{{ __('Assign Roles') }}</label>
                                    <div class="row">
                                        @foreach($roles as $role)
                                            <div class="col-md-6 mb-2">
                                                <div class="form-check">
                                                    <input type="checkbox" name="roles[]" value="{{ $role->id }}" class="form-check-input" id="role_{{ $role->id }}" {{ (is_array(old('roles')) && in_array($role->id, old('roles'))) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="role_{{ $role->id }}">
                                                        {{ $role->name }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary">{{ __('Create User & Employee') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    function loadPositions() {
        const departmentId = document.getElementById('department').value;
        const positionOptions = document.querySelectorAll('.position-option');
        
        positionOptions.forEach(option => {
            if (departmentId === '' || option.getAttribute('data-department') === departmentId) {
                option.style.display = 'block';
            } else {
                option.style.display = 'none';
            }
        });
        
        // Reset position selection when department changes
        document.getElementById('position_id').value = '';
    }
    
    // Initialize positions based on department selection
    document.addEventListener('DOMContentLoaded', function() {
        loadPositions();
    });
</script>
@endsection
