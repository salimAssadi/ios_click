@extends('tenant::layouts.app')
@section('page-title')
    {{ __('User Details') }}
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5>{{ __('User Details') }}: {{ $user->name }}</h5>
                    <div>
                        <a href="{{ route('tenant.role.users.edit', $user->id) }}" class="btn btn-primary btn-sm">
                            <i class="fa fa-edit"></i> {{ __('Edit') }}
                        </a>
                        <a href="{{ route('tenant.role.users.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fa fa-arrow-left"></i> {{ __('Back to Users') }}
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h4 class="mb-3">{{ __('User Information') }}</h4>
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <th style="width: 30%">{{ __('Full Name') }}</th>
                                        <td>{{ $user->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('Email') }}</th>
                                        <td>{{ $user->email }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('Phone Number') }}</th>
                                        <td>{{ $user->phone_number ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('Status') }}</th>
                                        <td>
                                            @if($user->is_active)
                                                <span class="badge bg-success">{{ __('Active') }}</span>
                                            @else
                                                <span class="badge bg-danger">{{ __('Inactive') }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('Created At') }}</th>
                                        <td>{{ $user->created_at->format('Y-m-d H:i:s') }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('Last Updated') }}</th>
                                        <td>{{ $user->updated_at->format('Y-m-d H:i:s') }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="col-md-6">
                            <h4 class="mb-3">{{ __('Employee Information') }}</h4>
                            @if($employee)
                                <table class="table table-bordered">
                                    <tbody>
                                        <tr>
                                            <th style="width: 30%">{{ __('Department') }}</th>
                                            <td>{{ $employee->position->department->name ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>{{ __('Position') }}</th>
                                            <td>{{ $employee->position->name ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>{{ __('Reports To') }}</th>
                                            <td>{{ $employee->reportsTo->name ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>{{ __('Status') }}</th>
                                            <td>
                                                @if($employee->status == 'active')
                                                    <span class="badge bg-success">{{ __('Active') }}</span>
                                                @else
                                                    <span class="badge bg-danger">{{ __('Inactive') }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            @else
                                <div class="alert alert-warning">
                                    {{ __('No employee record found for this user.') }}
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <h4 class="mb-3">{{ __('Assigned Roles') }}</h4>
                            @if($user->roles->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>{{ __('Role Name') }}</th>
                                                <th>{{ __('Permissions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($user->roles as $role)
                                                <tr>
                                                    <td style="width: 20%">{{ $role->name }}</td>
                                                    <td>
                                                        @if($role->permissions->count() > 0)
                                                            @php
                                                                $permissionsByModule = $role->permissions->groupBy(function($permission) {
                                                                    return explode(' ', $permission->name)[0];
                                                                });
                                                            @endphp
                                                            
                                                            @foreach($permissionsByModule as $module => $permissions)
                                                                <div class="mb-2">
                                                                    <strong>{{ ucfirst($module) }}:</strong>
                                                                    <div>
                                                                        @foreach($permissions as $permission)
                                                                            <span class="badge bg-info me-1 mb-1">{{ $permission->name }}</span>
                                                                        @endforeach
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        @else
                                                            <span class="text-muted">{{ __('No permissions assigned') }}</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-warning">
                                    {{ __('No roles assigned to this user.') }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
