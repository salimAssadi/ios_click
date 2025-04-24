@extends('tenant::layouts.app')
@section('page-title')
    {{ __('Roles Management') }}
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5>{{ __('Roles Management') }}</h5>
                    <a href="{{ route('tenant.role.roles.create') }}" class="btn btn-primary btn-sm">
                        <i class="fa fa-plus"></i> {{ __('Create New Role') }}
                    </a>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table" id="pc-dt-simple">
                            <thead>
                                <tr>
                                    <th>{{ __('Role') }}</th>
                                    <th>{{ __('Permissions') }}</th>
                                    <th width="200px">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($roles as $role)
                                    <tr>
                                        <td>{{ $role->name }}</td>
                                        <td style="white-space: inherit">
                                            @php
                                                $groupedPermissions = $role->permissions->groupBy('module');
                                            @endphp
                            
                                            @foreach ($groupedPermissions as $module => $permissions)
                                                <div class="mb-2">
                                                    <strong>{{ __(ucfirst($module)) }}:</strong>
                                                    <br>
                                                    @foreach ($permissions as $permission)
                                                        <span class="badge rounded p-2 m-1 px-3 bg-primary">
                                                            <a href="#" class="text-white">{{ __($permission->name) }}</a>
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @endforeach
                                        </td>
                                        <td class="Action">
                                            <span class="d-flex">
                                                {{-- @can('Edit Role') --}}
                                                    <div class="action-btn ms-2">
                                                        <a class="btn btn-sm btn-icon  bg-light-secondary me-2"
                                                            href="{{ route('tenant.role.roles.edit', $role->id) }}">
                                                            <i class="ti ti-edit"></i>
                                                        </a>
                                                    </div>
                                                {{-- @endcan --}}

                                                @can('Delete Role')
                                                    <div class="action-btn ms-2">
                                                        <a class="bs-pass-para btn btn-sm btn-icon bg-light-secondary "
                                                            href="#" data-title="{{ __('Delete Role') }}"
                                                            data-confirm="{{ __('Are You Sure?') }}"
                                                            data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                            data-confirm-yes="delete-form-{{ $role->id }}"
                                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                                            title="{{ __('Delete') }}">
                                                            <i class="ti ti-trash f-20"></i>
                                                        </a>
                                                        {!! Form::open([
                                                            'method' => 'DELETE',
                                                            'route' => ['tenant.role.roles.destroy', $role->id],
                                                            'id' => 'delete-form-' . $role->id,
                                                        ]) !!}
                                                        {!! Form::close() !!}
                                                    </div>
                                                @endcan
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
