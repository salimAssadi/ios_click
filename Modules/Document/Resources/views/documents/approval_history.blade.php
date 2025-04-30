@extends('tenant::layouts.master')

@section('title', __('Document Approval History'))

@section('content')
    <div class="pc-container">
        <div class="pc-content">
            <div class="page-header">
                <div class="page-block">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <div class="page-header-title">
                                <h4 class="mb-0">{{ __('Document Approval History') }}</h4>
                            </div>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('tenant.dashboard') }}">{{ __('Dashboard') }}</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('tenant.documents.index') }}">{{ __('Documents') }}</a></li>
                                <li class="breadcrumb-item">{{ __('Approval History') }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h5>{{ $document->title }}</h5>
                            <div class="d-flex align-items-center">
                                <span class="badge bg-light-secondary mb-0 me-2">{{ __('ID') }}: {{ $document->document_number }}</span>
                                <span class="badge bg-light-secondary mb-0">{{ __('Version') }}: {{ $document->latest_version_number }}</span>
                            </div>
                        </div>
                        
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="timeline-steps">
                                        @forelse($historyLogs as $log)
                                            <div class="timeline-step">
                                                <div class="timeline-content">
                                                    <div class="timeline-heading @if($log->action_type == 'approved') text-success @elseif($log->action_type == 'rejected') text-danger @elseif($log->action_type == 'requested_modification') text-warning @else text-primary @endif">
                                                        <h6 class="fw-bold">
                                                            @if($log->action_type == 'approved')
                                                                <i class="fas fa-check-circle me-1"></i> {{ __('Approved') }}
                                                            @elseif($log->action_type == 'rejected')
                                                                <i class="fas fa-times-circle me-1"></i> {{ __('Rejected') }}
                                                            @elseif($log->action_type == 'requested_modification')
                                                                <i class="fas fa-edit me-1"></i> {{ __('Modification Requested') }}
                                                            @elseif($log->action_type == 'Request')
                                                                <i class="fas fa-paper-plane me-1"></i> {{ __('Request Created') }}
                                                            @else
                                                                <i class="fas fa-info-circle me-1"></i> {{ __(ucfirst($log->action_type)) }}
                                                            @endif
                                                        </h6>
                                                        <span class="text-muted small">
                                                            {{ $log->created_at->format('d M Y, h:i A') }}
                                                        </span>
                                                    </div>
                                                    <div class="timeline-body p-3 mt-2 border rounded">
                                                        <div class="d-flex align-items-center mb-2">
                                                            <div class="me-3">
                                                                <img src="{{ $log->performer->avatar ? asset('storage/' . $log->performer->avatar) : asset('assets/images/user/avatar-default.png') }}" class="img-radius wid-40" alt="{{ $log->performer->name }}">
                                                            </div>
                                                            <div>
                                                                <h6 class="fw-medium mb-0">{{ $log->performer->name }}</h6>
                                                                <p class="text-muted small mb-0">{{ $log->performer->position ? $log->performer->position->name : __('N/A') }}</p>
                                                            </div>
                                                        </div>
                                                        
                                                        @if(!empty($log->response))
                                                            <div class="mt-3">
                                                                <h6 class="fw-medium mb-2">{{ __('Comments') }}:</h6>
                                                                <p class="mb-0">{{ $log->response }}</p>
                                                            </div>
                                                        @endif
                                                        
                                                        @if(!empty($log->change_summary))
                                                            <div class="mt-3">
                                                                <h6 class="fw-medium mb-2">{{ __('Summary') }}:</h6>
                                                                <p class="mb-0">{{ $log->change_summary }}</p>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="text-center py-4">
                                                <i class="ti ti-history text-secondary opacity-50" style="font-size: 4rem;"></i>
                                                <p class="mt-3 text-muted">{{ __('No approval history found for this document') }}</p>
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css-page')
<style>
    .timeline-steps {
        position: relative;
        padding-left: 45px;
    }
    
    .timeline-steps:before {
        content: '';
        position: absolute;
        top: 0;
        bottom: 0;
        width: 2px;
        background-color: #e5e5e5;
        left: 7px;
    }
    
    .timeline-step {
        position: relative;
        padding-bottom: 30px;
    }
    
    .timeline-step:last-child {
        padding-bottom: 0;
    }
    
    .timeline-step:before {
        content: '';
        position: absolute;
        left: -43px;
        top: 0;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        background-color: #fff;
        border: 2px solid var(--bs-primary);
        z-index: 1;
    }
    
    .timeline-step.success:before {
        border-color: var(--bs-success);
    }
    
    .timeline-step.danger:before {
        border-color: var(--bs-danger);
    }
    
    .timeline-step.warning:before {
        border-color: var(--bs-warning);
    }
    
    .timeline-content {
        position: relative;
    }
    
    .timeline-heading {
        margin-bottom: 10px;
    }
</style>
@endpush
