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
                                        <td>{{ $employee->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('Email') }}</th>
                                        <td>{{ $user->email }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('Phone Number') }}</th>
                                        <td>{{ $employee->phone ?? 'N/A' }}</td>
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
                        </div>
                    </div>
                    
                   
                </div>
            </div>
        </div>
    </div>
@endsection
