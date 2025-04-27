@extends('tenant::layouts.app')
@section('page-title')
    {{ __('Dashboard') }}
@endsection
@section('content')
    <!-- Quick Stats -->
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="avtar bg-light-secondary">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <p class="text-muted text-sm mt-4 mb-2">{{ __('Total Documents') }}</p>
                    <h6 class="mb-3">{{ $result['documentStats']['total'] }}</h6>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="avtar bg-warning">
                        <i class="fas fa-clock"></i>
                    </div>
                    <p class="text-muted text-sm mt-4 mb-2">{{ __('Pending') }}</p>
                    <h6 class="mb-3">{{ $result['documentStats']['pending'] }}</h6>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="avtar bg-success">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <p class="text-muted text-sm mt-4 mb-2">{{ __('Approved') }}</p>
                    <h6 class="mb-3">{{ $result['documentStats']['approved'] }}</h6>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="avtar bg-danger">
                        <i class="fas fa-times-circle"></i>
                    </div>
                    <p class="text-muted text-sm mt-4 mb-2">{{ __('Rejected') }}</p>
                    <h6 class="mb-3">{{ $result['documentStats']['rejected'] }}</h6>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>{{ __('Quick Actions') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($result['quickActions'] as $action)
                            <div class="col-md-4">
                                <a href="{{ $action['route'] }}" class="btn btn-outline-primary w-100 mb-3">
                                    <i class="{{ $action['icon'] }} mr-2"></i>
                                    {{ __($action['name']) }}
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Documents -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>{{ __('Recent Documents') }}</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('Document Name') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Last Updated') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($result['recentDocuments'] as $document)
                                    <tr>
                                        <td>{{ $document['name'] }}</td>
                                        <td>
                                            @php
                                                $statusClass = [
                                                    'Approved' => 'badge bg-success',
                                                    'Pending' => 'badge bg-warning',
                                                    'Under Review' => 'badge bg-info',
                                                    'Rejected' => 'badge bg-danger'
                                                ][$document['status']] ?? 'badge bg-secondary';
                                            @endphp
                                            <span class="{{ $statusClass }}">{{ $document['status'] }}</span>
                                        </td>
                                        <td>{{ $document['date'] }}</td>
                                        <td>
                                            <div class="action-btn bg-info ms-2">
                                                <a href="#" class="mx-3 btn btn-sm d-inline-flex align-items-center" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('View') }}">
                                                    <i class="fas fa-eye text-white"></i>
                                                </a>
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
