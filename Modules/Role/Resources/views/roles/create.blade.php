@extends('tenant::layouts.app')
@section('page-title')
    {{ __('Create New Role') }}
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5>{{ __('Create New Role') }}</h5>
                    <a href="{{ route('tenant.role.roles.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fa fa-arrow-left"></i> {{ __('Back to Roles') }}
                    </a>
                </div>
                <div class="card-body">
                    <form action="{{ route('tenant.role.roles.store') }}" method="POST">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="name">{{ __('Role Name') }} <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
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
                            @if (!empty($permissions))
                                <h6 class="my-3">{{ __('Assign Permission to Roles') }} </h6>
                                <table class="table mb-0" id="dataTable-1">
                                    <thead>
                                        <tr>
                                            <th>
                                                <input type="checkbox" class="form-check-input" id="checkall">
                                            </th>
                                            <th>{{ __('Module') }}</th>
                                            <th>{{ __('Permissions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($permissions as $module => $modulePermissions)
                                            <tr>
                                                <td>
                                                    <input type="checkbox" class="form-check-input ischeck" data-id="{{ Str::slug($module) }}">
                                                </td>
                                                <td><label class="form-label">{{ __(ucfirst($module)) }}</label></td>
                                                <td>
                                                    <div class="row">
                                                        @foreach ($modulePermissions as $permission)
                                                            <div class="col-md-3 custom-control custom-checkbox">
                                                                <input type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                                                                    class="form-check-input isscheck isscheck_{{ Str::slug($module) }}"
                                                                    id="permission{{ $permission->id }}">
                                                                <label for="permission{{ $permission->id }}" class="form-label font-weight-500">
                                                                    {{ __($permission->name) }}
                                                                </label>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @endif
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">{{ __('Create Role') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script-page')
<script>
    $(document).ready(function () {
        $("#checkall").click(function () {
            $('input:checkbox').not(this).prop('checked', this.checked);
        });
        $(".ischeck").click(function () {
            var moduleSlug = $(this).data('id');
            $('.isscheck_' + moduleSlug).prop('checked', this.checked);
        });
    });
</script>
@endpush

