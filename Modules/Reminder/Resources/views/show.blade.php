@extends('tenant::layouts.app')

@section('title', __('Reminder Details'))

@section('content')
<div class="pc-container">
    <div class="pc-content">
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <div class="page-header-title">
                            <h5 class="m-b-10">{{ __('Reminder Details') }}</h5>
                        </div>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('tenant.dashboard') }}">{{ __('Dashboard') }}</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('tenant.reminder.index') }}">{{ __('Reminders') }}</a></li>
                            <li class="breadcrumb-item">{{ __('Details') }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0">{{ $reminder->title }}</h5>
                            <p class="text-muted mb-0">{{ __('Reminder Details') }}</p>
                        </div>
                        <div class="d-flex">
                            @if($reminder->reminder_type == 'personal')
                                <a href="{{ route('tenant.reminder.edit', $reminder->id) }}" class="btn btn-outline-primary btn-sm me-2">
                                    <i class="ti ti-edit me-1"></i> {{ __('Edit') }}
                                </a>
                            @endif
                            
                            <form action="{{ route('tenant.reminder.toggle-active', $reminder->id) }}" method="POST" class="d-inline me-2">
                                @csrf
                                <button type="submit" class="btn btn-sm {{ $reminder->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}">
                                    @if($reminder->is_active)
                                        <i class="ti ti-bell-off me-1"></i> {{ __('Disable') }}
                                    @else
                                        <i class="ti ti-bell-ringing me-1"></i> {{ __('Enable') }}
                                    @endif
                                </button>
                            </form>
                            
                            @if($reminder->reminder_type == 'personal')
                                <form action="{{ route('tenant.reminder.destroy', $reminder->id) }}" method="POST" class="d-inline delete-item">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm">
                                        <i class="ti ti-trash me-1"></i> {{ __('Delete') }}
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6 class="text-muted mb-3">{{ __('Basic Information') }}</h6>
                                <div class="table-responsive">
                                    <table class="table table-borderless mb-0">
                                        <tbody>
                                            <tr>
                                                <td class="ps-0 text-muted" width="40%">{{ __('Title') }}</td>
                                                <td>{{ $reminder->title }}</td>
                                            </tr>
                                            <tr>
                                                <td class="ps-0 text-muted">{{ __('Description') }}</td>
                                                <td>{{ $reminder->description ?: __('N/A') }}</td>
                                            </tr>
                                            <tr>
                                                <td class="ps-0 text-muted">{{ __('Type') }}</td>
                                                <td>
                                                    @if($reminder->reminder_type == 'document_expiry')
                                                        <span class="badge bg-light-info">{{ __('Document Expiry') }}</span>
                                                    @else
                                                        <span class="badge bg-light-primary">{{ __('Personal') }}</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="ps-0 text-muted">{{ __('Status') }}</td>
                                                <td>
                                                    @if($reminder->status == 'pending')
                                                        <span class="badge bg-light-warning">{{ __('Pending') }}</span>
                                                    @elseif($reminder->status == 'sent')
                                                        <span class="badge bg-light-success">{{ __('Sent') }}</span>
                                                    @elseif($reminder->status == 'cancelled')
                                                        <span class="badge bg-light-danger">{{ __('Cancelled') }}</span>
                                                    @endif
                                                    
                                                    @if(!$reminder->is_active)
                                                        <span class="badge bg-light-secondary">{{ __('Inactive') }}</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <h6 class="text-muted mb-3">{{ __('Reminder Timing') }}</h6>
                                <div class="table-responsive">
                                    <table class="table table-borderless mb-0">
                                        <tbody>
                                            <tr>
                                                <td class="ps-0 text-muted" width="40%">{{ __('Remind Date') }}</td>
                                                <td>{{ $reminder->remind_at->format('Y-m-d') }}</td>
                                            </tr>
                                            <tr>
                                                <td class="ps-0 text-muted">{{ __('Remind Time') }}</td>
                                                <td>{{ $reminder->remind_at->format('h:i A') }}</td>
                                            </tr>
                                            <tr>
                                                <td class="ps-0 text-muted">{{ __('Last Sent') }}</td>
                                                <td>{{ $reminder->last_sent_at ? $reminder->last_sent_at->format('Y-m-d h:i A') : __('Never Sent') }}</td>
                                            </tr>
                                            <tr>
                                                <td class="ps-0 text-muted">{{ __('Created On') }}</td>
                                                <td>{{ $reminder->created_at->format('Y-m-d') }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Recurrence Information -->
                        @if($reminder->is_recurring)
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h6 class="text-muted mb-3">{{ __('Recurrence Details') }}</h6>
                                <div class="table-responsive">
                                    <table class="table table-borderless mb-0">
                                        <tbody>
                                            <tr>
                                                <td class="ps-0 text-muted" width="25%">{{ __('Recurrence') }}</td>
                                                <td>
                                                    <span class="badge bg-light-primary">{{ __('Recurring') }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="ps-0 text-muted">{{ __('Pattern') }}</td>
                                                <td>
                                                    @php
                                                        $patterns = [
                                                            'daily' => __('Daily'),
                                                            'weekly' => __('Weekly'),
                                                            'monthly' => __('Monthly'),
                                                            'yearly' => __('Yearly')
                                                        ];
                                                    @endphp
                                                    {{ $patterns[$reminder->recurrence_pattern] ?? $reminder->recurrence_pattern }}
                                                    
                                                    @if($reminder->recurrence_interval > 1)
                                                        {{ __('(Every :interval :type)', ['interval' => $reminder->recurrence_interval, 'type' => strtolower($patterns[$reminder->recurrence_pattern] ?? $reminder->recurrence_pattern)]) }}
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="ps-0 text-muted">{{ __('End Date') }}</td>
                                                <td>{{ $reminder->recurrence_end_date ? $reminder->recurrence_end_date->format('Y-m-d') : __('No End Date') }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        @endif
                        
                        <!-- Document Information (for document expiry reminders) -->
                        @if($reminder->reminder_type == 'document_expiry' && $reminder->remindable)
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h6 class="text-muted mb-3">{{ __('Document Information') }}</h6>
                                <div class="table-responsive">
                                    <table class="table table-borderless mb-0">
                                        <tbody>
                                            <tr>
                                                <td class="ps-0 text-muted" width="25%">{{ __('Document') }}</td>
                                                <td>
                                                    @php
                                                        $document = $reminder->remindable;
                                                        // If this is a document version, get the actual document
                                                        if (method_exists($document, 'document')) {
                                                            $document = $document->document;
                                                        }
                                                    @endphp
                                                    
                                                    @if($document)
                                                        <a href="{{ route('tenant.document.show', $document->id) }}" target="_blank">
                                                            {{ $document->title }} 
                                                            <i class="ti ti-external-link text-muted ms-1 small"></i>
                                                        </a>
                                                    @else
                                                        {{ __('Document Not Found') }}
                                                    @endif
                                                </td>
                                            </tr>
                                            
                                            @if($reminder->metadata && isset($reminder->metadata['days_before_expiry']))
                                            <tr>
                                                <td class="ps-0 text-muted">{{ __('Days Before Expiry') }}</td>
                                                <td>{{ $reminder->metadata['days_before_expiry'] }} {{ __('days') }}</td>
                                            </tr>
                                            @endif
                                            
                                            @if($reminder->metadata && isset($reminder->metadata['expiry_date']))
                                            <tr>
                                                <td class="ps-0 text-muted">{{ __('Expiry Date') }}</td>
                                                <td>{{ $reminder->metadata['expiry_date'] }}</td>
                                            </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        @endif
                        
                        <!-- Notification Information -->
                        <div class="row">
                            <div class="col-md-12">
                                <h6 class="text-muted mb-3">{{ __('Notification Information') }}</h6>
                                <div class="table-responsive">
                                    <table class="table table-borderless mb-0">
                                        <tbody>
                                            <tr>
                                                <td class="ps-0 text-muted" width="25%">{{ __('Channels') }}</td>
                                                <td>
                                                    @if($reminder->notification_channels)
                                                        @foreach(explode(',', $reminder->notification_channels) as $channel)
                                                            @if($channel == 'email')
                                                                <span class="badge bg-light-info me-1">{{ __('Email') }}</span>
                                                            @elseif($channel == 'system')
                                                                <span class="badge bg-light-primary me-1">{{ __('System') }}</span>
                                                            @else
                                                                <span class="badge bg-light-secondary me-1">{{ $channel }}</span>
                                                            @endif
                                                        @endforeach
                                                    @else
                                                        {{ __('None Specified') }}
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="ps-0 text-muted">{{ __('Recipients') }}</td>
                                                <td>
                                                    @if(count($reminder->recipients) > 0)
                                                        @foreach($reminder->recipients as $recipient)
                                                            <span class="badge bg-light-success me-1">{{ $recipient->name }}</span>
                                                        @endforeach
                                                    @else
                                                        {{ __('No recipients specified') }}
                                                    @endif
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-footer">
                        <a href="{{ route('tenant.reminder.index') }}" class="btn btn-light">
                            <i class="ti ti-arrow-left me-1"></i> {{ __('Back to Reminders') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Delete confirmation
        const deleteForm = document.querySelector('.delete-item');
        if (deleteForm) {
            deleteForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const confirmDelete = confirm("{{ __('Are you sure you want to delete this reminder? This action cannot be undone.') }}");
                if(confirmDelete) {
                    this.submit();
                }
            });
        }
    });
</script>
@endpush
