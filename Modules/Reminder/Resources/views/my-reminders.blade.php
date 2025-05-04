@extends('tenant::layouts.app')

@section('page-title', __('My Reminders'))
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('tenant.dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('My Reminders') }}</li>
@endsection

@section('content')       
        <div class="row">
            <!-- Upcoming Reminders -->
            <div class="col-xl-4 col-md-6">
                <div class="card">
                    <div class="card-header border-0 pb-0">
                        <h5 class="mb-0">{{ __('Upcoming') }}</h5>
                    </div>
                    <div class="card-body pt-2">
                        @if(isset($upcoming) && count($upcoming) > 0)
                            <div class="upcoming-reminders">
                                @foreach($upcoming as $reminder)
                                    <div class="card border mb-3">
                                        <div class="card-body p-3">
                                            <div class="d-flex align-items-center mb-2">
                                                @if($reminder->reminder_type == 'document_expiry')
                                                    <span class="avatar avatar-sm me-2 f-24"><i class="ti ti-file-certificate text-info"></i></span>
                                                @else
                                                    <span class="avatar avatar-sm  me-2 f-24"><i class="ti ti-bell text-primary"></i></span>
                                                @endif
                                                <h6 class="mb-0 flex-grow-1 text-truncate" title="{{ $reminder->title }}">{{ $reminder->title }}</h6>
                                            </div>
                                            <p class="text-muted small mb-2">{{ Str::limit($reminder->description, 60) }}</p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="badge bg-light-warning">
                                                    <i class="ti ti-calendar-event me-1"></i>
                                                    {{ $reminder->remind_at->format('Y-m-d') }}
                                                </span>
                                                <a href="{{ route('tenant.reminder.show', $reminder->id) }}" class="btn btn-sm btn-light">
                                                    <i class="ti ti-eye"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <span class="avatar avatar-lg  mb-3">
                                    <i class="ti ti-bell-off text-warning f-24"></i>
                                </span>
                                <h6>{{ __('No Upcoming Reminders') }}</h6>
                                <p class="text-muted small mb-0">{{ __('You have no upcoming reminders at the moment.') }}</p>
                            </div>
                        @endif
                    </div>
                    @if(isset($upcoming) && count($upcoming) > 3)
                        <div class="card-footer text-center border-0 pt-0">
                            <a href="{{ route('tenant.reminder.index', ['filter' => 'upcoming']) }}" class="btn btn-sm btn-light-primary">
                                {{ __('View All') }} <i class="ti ti-arrow-right ms-1"></i>
                            </a>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Recent Reminders -->
            <div class="col-xl-4 col-md-6">
                <div class="card">
                    <div class="card-header border-0 pb-0">
                        <h5 class="mb-0">{{ __('Recent') }}</h5>
                    </div>
                    <div class="card-body pt-2">
                        @if(isset($recent) && count($recent) > 0)
                            <div class="recent-reminders">
                                @foreach($recent as $reminder)
                                    <div class="card border mb-3">
                                        <div class="card-body p-3">
                                            <div class="d-flex align-items-center mb-2">
                                                @if($reminder->reminder_type == 'document_expiry')
                                                    <span class="avatar avatar-sm bg-light-info me-2"><i class="ti ti-file-certificate"></i></span>
                                                @else
                                                    <span class="avatar avatar-sm bg-light-primary me-2"><i class="ti ti-bell"></i></span>
                                                @endif
                                                <h6 class="mb-0 flex-grow-1 text-truncate" title="{{ $reminder->title }}">{{ $reminder->title }}</h6>
                                            </div>
                                            <p class="text-muted small mb-2">{{ Str::limit($reminder->description, 60) }}</p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="badge {{ $reminder->status == 'sent' ? 'bg-light-success' : 'bg-light-secondary' }}">
                                                    <i class="{{ $reminder->status == 'sent' ? 'ti ti-bell-ringing' : 'ti ti-bell' }} me-1"></i>
                                                    {{ $reminder->status == 'sent' ? __('Sent') : __('Processed') }}
                                                </span>
                                                <a href="{{ route('tenant.reminder.show', $reminder->id) }}" class="btn btn-sm btn-light">
                                                    <i class="ti ti-eye"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <span class="avatar avatar-lg  mb-3">
                                    <i class="ti ti-history text-secondary f-24"></i>
                                </span>
                                <h6>{{ __('No Recent Reminders') }}</h6>
                                <p class="text-muted small mb-0">{{ __('You have no recently processed reminders.') }}</p>
                            </div>
                        @endif
                    </div>
                    @if(isset($recent) && count($recent) > 3)
                        <div class="card-footer text-center border-0 pt-0">
                            <a href="{{ route('tenant.reminder.index', ['filter' => 'recent']) }}" class="btn btn-sm btn-light-primary">
                                {{ __('View All') }} <i class="ti ti-arrow-right ms-1"></i>
                            </a>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="col-xl-4 col-md-12">
                <div class="card">
                    <div class="card-header border-0 pb-0">
                        <h5 class="mb-0">{{ __('Quick Actions') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-6">
                                <a href="{{ route('tenant.reminder.create', ['type' => 'personal']) }}" class="card border text-center h-100 p-3 hover-shadow">
                                    <div class="d-flex flex-column align-items-center justify-content-center h-100">
                                        <span class="avatar  mb-2">
                                            <i class="ti ti-bell-plus text-primary f-20"></i>
                                        </span>
                                        <h6 class="mb-0">{{ __('Personal') }}</h6>
                                        <p class="text-muted small mb-0">{{ __('Create reminder') }}</p>
                                    </div>
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="{{ route('tenant.reminder.create', ['type' => 'document_expiry']) }}" class="card border text-center h-100 p-3 hover-shadow">
                                    <div class="d-flex flex-column align-items-center justify-content-center h-100">
                                        <span class="avatar  mb-2">
                                            <i class="ti ti-file-certificate text-info f-20"></i>
                                        </span>
                                        <h6 class="mb-0">{{ __('Document') }}</h6>
                                        <p class="text-muted small mb-0">{{ __('Expiry reminder') }}</p>
                                    </div>
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="{{ route('tenant.reminder.index', ['filter' => 'document_expiry']) }}" class="card border text-center h-100 p-3 hover-shadow">
                                    <div class="d-flex flex-column align-items-center justify-content-center h-100">
                                        <span class="avatar  mb-2">
                                            <i class="ti ti-file text-warning f-20"></i>
                                        </span>
                                        <h6 class="mb-0">{{ __('Document') }}</h6>
                                        <p class="text-muted small mb-0">{{ __('View all') }}</p>
                                    </div>
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="{{ route('tenant.reminder.index', ['filter' => 'personal']) }}" class="card border text-center h-100 p-3 hover-shadow">
                                    <div class="d-flex flex-column align-items-center justify-content-center h-100">
                                        <span class="avatar  mb-2">
                                            <i class="ti ti-list-check text-success f-20"></i>
                                        </span>
                                        <h6 class="mb-0">{{ __('Personal') }}</h6>
                                        <p class="text-muted small mb-0">{{ __('View all') }}</p>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Reminder Statistics -->
                <div class="card mt-4">
                    <div class="card-header border-0 pb-0">
                        <h5 class="mb-0">{{ __('Statistics') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="bg-light-primary rounded p-3 text-center">
                                    <h3 class="mb-1 text-primary">{{ $statistics['total'] ?? 0 }}</h3>
                                    <p class="text-muted small mb-0">{{ __('Total Reminders') }}</p>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="bg-light-warning rounded p-3 text-center">
                                    <h3 class="mb-1 text-warning">{{ $statistics['upcoming'] ?? 0 }}</h3>
                                    <p class="text-muted small mb-0">{{ __('Upcoming') }}</p>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="bg-light-info rounded p-3 text-center">
                                    <h3 class="mb-1 text-info">{{ $statistics['document'] ?? 0 }}</h3>
                                    <p class="text-muted small mb-0">{{ __('Document Expiry') }}</p>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="bg-light-success rounded p-3 text-center">
                                    <h3 class="mb-1 text-success">{{ $statistics['personal'] ?? 0 }}</h3>
                                    <p class="text-muted small mb-0">{{ __('Personal') }}</p>
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
    .hover-shadow {
        transition: all 0.3s ease;
    }
    .hover-shadow:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        transform: translateY(-3px);
    }
    .upcoming-reminders, .recent-reminders {
        max-height: 400px;
        overflow-y: auto;
    }
</style>
@endpush
