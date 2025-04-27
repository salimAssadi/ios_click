@extends('tenant::layouts.app')
@section('page-title')
    {{ __('Users Management') }}
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5>{{ __('Users Management') }}</h5>
                    <a href="{{ route('tenant.role.users.create') }}" class="btn btn-primary btn-sm">
                        <i class="fa fa-plus"></i> {{ __('Create New User') }}
                    </a>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped basic-datatable">
                            <thead>
                                <tr>
                                    <th>{{ __('ID') }}</th>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Email') }}</th>
                                    <th>{{ __('Position') }}</th>
                                    <th>{{ __('Department') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Roles') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($employees as $employee)
                                <tr>
                                    <td>{{ $employee->id }}</td>
                                    <td>{{ $employee->name }}</td>
                                    <td>{{ $employee->user->email ?? __('No User') }}</td>
                                    <td>
                                        @if($employee->position)
                                            {{ $employee->position->name }}
                                        @else
                                            <span class="text-muted">{{ __('Not assigned') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($employee->position && $employee->position->department)
                                            {{ $employee->position->department->name }}
                                        @else
                                            <span class="text-muted">{{ __('Not assigned') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($employee->status == 'active')
                                            <span class="badge bg-success">{{ __('Active') }}</span>
                                        @else
                                            <span class="badge bg-danger">{{ __('Inactive') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($employee->user && $employee->user->roles->count() > 0)
                                            @foreach($employee->user->roles as $role)
                                                <span class="badge bg-info">{{ $role->name }}</span>
                                            @endforeach
                                        @else
                                            <span class="badge bg-warning">{{ __('No Roles') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group gap-2">
                                            <a href="{{ route('tenant.role.users.show', $employee->id) }}" class="btn btn-info btn-sm" title="{{ __('View') }}">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            <a href="{{ route('tenant.role.users.edit', $employee->id) }}" class="btn btn-primary btn-sm" title="{{ __('Edit') }}">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                                <form action="{{ route('tenant.role.users.destroy', $employee->id) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure you want to delete this user?') }}');" style="display: inline-block;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" title="{{ __('Delete') }}">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </form>
                                           
                                        </div>
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
