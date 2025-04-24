@extends('tenant::layouts.app')
@section('page-title')
    {{ __('Permission Details') }}
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5>{{ __('Permission Details') }}: {{ $permission->name }}</h5>
                    <div>
                        <a href="{{ route('tenant.role.permissions.edit', $permission->id) }}" class="btn btn-primary btn-sm">
                            <i class="fa fa-edit"></i> {{ __('Edit') }}
                        </a>
                        <a href="{{ route('tenant.role.permissions.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fa fa-arrow-left"></i> {{ __('Back to Permissions') }}
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th>{{ __('ID') }}</th>
                                    <td>{{ $permission->id }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Name') }}</th>
                                    <td>{{ $permission->name }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Guard Name') }}</th>
                                    <td>{{ $permission->guard_name }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Created At') }}</th>
                                    <td>{{ $permission->created_at->format('Y-m-d H:i:s') }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Updated At') }}</th>
                                    <td>{{ $permission->updated_at->format('Y-m-d H:i:s') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <h6>{{ __('Roles with this Permission') }}</h6>
                            @if($permission->roles->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>{{ __('ID') }}</th>
                                                <th>{{ __('Role Name') }}</th>
                                                <th>{{ __('Guard') }}</th>
                                                <th>{{ __('Action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($permission->roles as $role)
                                                <tr>
                                                    <td>{{ $role->id }}</td>
                                                    <td>{{ $role->name }}</td>
                                                    <td>{{ $role->guard_name }}</td>
                                                    <td>
                                                        <a href="{{ route('tenant.role.roles.show', $role->id) }}" class="btn btn-info btn-sm">
                                                            <i class="fa fa-eye"></i> {{ __('View') }}
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-muted">{{ __('No roles have been assigned this permission.') }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
