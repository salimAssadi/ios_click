@extends('tenant::layouts.app')
@section('page-title')
    {{ __('Edit User') }}
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5>{{ __('Edit User') }}: {{ $employee->name }}</h5>
                    <a href="{{ route('tenant.role.users.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fa fa-arrow-left"></i> {{ __('Back to Users') }}
                    </a>
                </div>
                <div class="card-body">
                    <form action="{{ route('tenant.role.users.update', $employee->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        @error('name')
                            <div class="alert alert-danger">
                                {{ $message }}
                            </div>
                        @enderror
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="mb-3">{{ __('User Information') }}</h4>
                                
                                <div class="form-group mb-3">
                                    <label for="name">{{ __('Full Name') }} <span class="text-danger">*</span></label>
                                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $employee->name) }}" required>
                                    <small class="form-text text-muted">{{ __('Enter first and last name') }}</small>
                                    @error('name')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="email">{{ __('Email Address') }} <span class="text-danger">*</span></label>
                                    <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user ? $user->email : $employee->email) }}" required>
                                    @error('email')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="phone">{{ __('Phone Number') }}</label>
                                    <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $user->phone_number ?? ($employee->phone ?? '')) }}">
                                    @error('phone')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="password">{{ __('Password') }}</label>
                                    <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror">
                                    <small class="form-text text-muted">{{ __('Leave blank to keep current password. Minimum 8 characters if changing.') }}</small>
                                    @error('password')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="password_confirmation">{{ __('Confirm Password') }}</label>
                                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label for="status">{{ __('Status') }} <span class="text-danger">*</span></label>
                                    <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                                        <option value="active" {{ old('status', $user ? $user->is_active : $employee->status ?? ($user->is_active ? 'active' : 'inactive')) == 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                                        <option value="inactive" {{ old('status', $user ? $user->is_active : $employee->status ?? ($user->is_active ? 'active' : 'inactive')) == 'inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
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
                                            <option value="{{ $department->id }}" 
                                                {{ old('department_id', $employee->position->department_id ?? '') == $department->id ? 'selected' : '' }}>
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
                                                {{ old('position_id', $employee->position_id ?? '') == $position->id ? 'selected' : '' }}
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
                                                    <input type="checkbox" name="roles[]" value="{{ $role->id }}" class="form-check-input" id="role_{{ $role->id }}" 
                                                        {{ in_array($role->id, $userRoles) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="role_{{ $role->id }}">
                                                        {{ $role->name }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="col-md-12 mb-4 ">
                                    <div class="form-group col-md-12 mt-3">
                                        <div class="form-check form-switch custom-switch-v1">
                                            <input type="checkbox" name="signature_pad_enable" id="signature_pad_enable" class="form-check-input"  >
                                            <label class="form-check-label" for="signature_pad_enable">{{ __('Enable Signature Pad') }}</label>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group col-md-12 signature-pad-container d-none">
                                        {{ Form::label('signature_pad', __('Create Signature'), ['class' => 'form-label']) }}
                                        <div class="signature-pad border rounded mb-2">
                                            <canvas id="signature-pad" style="width: 100%; height: 200px; border: 1px solid #ccc; background-color: #fff;"></canvas>
                                        </div>
                                        <div class="d-flex mt-1">
                                            <button type="button" class="btn btn-sm btn-danger signature-clear-btn me-2">{{ __('Clear') }}</button>
                                            <button type="button" class="btn btn-sm btn-success signature-save-btn">{{ __('Save Signature') }}</button>
                                            <input type="hidden" name="signature_pad_data" id="signature_pad_data">
                                        </div>
                                         @if(!empty($employee->signature_pad_data))
                                            <div class="mt-3">
                                                <strong>{{ __('Current Signature:') }}</strong><br>
                                                <img src="{{  $employee->signature_pad_data?? '' }}" class="img-responsive mt-2" width="200">
                                            </div>
                                        @endif 
                                    </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary">{{ __('Update User & Employee') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script-page')
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>

<script>
     var signaturePad = null;
        
        function initSignaturePad() {
            if (document.getElementById('signature-pad')) {
                const canvas = document.getElementById('signature-pad');
                signaturePad = new SignaturePad(canvas, {
                    backgroundColor: 'rgb(255, 255, 255)'
                });
                
                // Adjust canvas size
                function resizeCanvas() {
                    const ratio = Math.max(window.devicePixelRatio || 1, 1);
                    canvas.width = canvas.offsetWidth * ratio;
                    canvas.height = canvas.offsetHeight * ratio;
                    canvas.getContext("2d").scale(ratio, ratio);
                    signaturePad.clear(); // Otherwise isEmpty() might return incorrect value
                }
                
                window.addEventListener("resize", resizeCanvas);
                resizeCanvas();
            }
        }
        
        // Initialize on page load
        initSignaturePad();
        
        // Toggle signature pad container
        $('#signature_pad_enable').on('change', function() {
            if ($(this).is(':checked')) {
                $('.signature-pad-container').removeClass('d-none');
                // Re-initialize signature pad when it becomes visible
                setTimeout(function() {
                    initSignaturePad();
                }, 100);
            } else {
                $('.signature-pad-container').addClass('d-none');
            }
        });
        
        // Clear signature pad
        $('.signature-clear-btn').on('click', function() {
            if (signaturePad) {
                signaturePad.clear();
            }
        });
        
        // Save signature
        $('.signature-save-btn').on('click', function() {
            if (signaturePad && !signaturePad.isEmpty()) {
                var signatureData = signaturePad.toDataURL();
                $('#signature_pad_data').val(signatureData);
                
                // Show preview
                if ($('.signature-pad-container .mt-3').length) {
                    $('.signature-pad-container .mt-3 img').attr('src', signatureData);
                } else {
                    $('.signature-pad-container').append(
                        '<div class="mt-3"><strong>{{ __("Current Signature:") }}</strong><br>' +
                        '<img src="' + signatureData + '" class="img-responsive mt-2" width="500"></div>'
                    );
                }
                
                toastr.success('{{ __("Signature saved successfully") }}');
            } else {
                toastr.error('{{ __("Please provide signature first") }}');
            }
        });
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
@endpush

