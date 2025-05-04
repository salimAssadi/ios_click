@extends('tenant::layouts.app')

@section('page-title', __('Reminders'))
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('tenant.dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Reminders') }}</li>
    @endsection

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5>{{ __('All Reminders') }}</h5>
                    
                    <div class="d-flex align-items-center">
                        <div class="btn-group me-2">
                            <button type="button" class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                @php
                                    $filterLabels = [
                                        'all' => __('All Reminders'),
                                        'document_expiry' => __('Document Expiry'),
                                        'personal' => __('Personal')
                                    ];
                                    $currentFilter = request()->get('filter', 'all');
                                @endphp
                                {{ $filterLabels[$currentFilter] ?? $filterLabels['all'] }}
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item {{ $currentFilter == 'all' ? 'active' : '' }}" href="{{ route('tenant.reminder.index', ['filter' => 'all']) }}">{{ __('All Reminders') }}</a></li>
                                <li><a class="dropdown-item {{ $currentFilter == 'document_expiry' ? 'active' : '' }}" href="{{ route('tenant.reminder.index', ['filter' => 'document_expiry']) }}">{{ __('Document Expiry') }}</a></li>
                                <li><a class="dropdown-item {{ $currentFilter == 'personal' ? 'active' : '' }}" href="{{ route('tenant.reminder.index', ['filter' => 'personal']) }}">{{ __('Personal') }}</a></li>
                            </ul>
                        </div>
                        
                        <a href="{{ route('tenant.reminder.create') }}" class="btn btn-primary">
                            <i class="ti ti-plus me-1"></i> {{ __('Create Reminder') }}
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="card-body p-0">
                @if(isset($reminders) && count($reminders) > 0)
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>{{ __('ID') }}</th>
                                    <th>{{ __('Title') }}</th>
                                    <th>{{ __('Type') }}</th>
                                    <th>{{ __('Remind Date') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($reminders as $reminder)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0">
                                                    @if($reminder->reminder_type == 'document_expiry')
                                                        <span class="avatar avatar-sm "><i class="ti ti-file-certificate text-info f-24"></i></span>
                                                    @else
                                                        <span class="avatar avatar-sm "><i class="ti ti-bell text-primary f-24"></i></span>
                                                    @endif
                                                </div>
                                                <div class="flex-grow-1 ms-3">
                                                    <h6 class="mb-0">{{ $reminder->title }}</h6>
                                                    <small class="text-muted">{{ Str::limit($reminder->description, 50) }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if($reminder->reminder_type == 'document_expiry')
                                                <span class="badge bg-light-info">{{ __('Document Expiry') }}</span>
                                            @else
                                                <span class="badge bg-light-primary">{{ __('Personal') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span>{{ $reminder->remind_at->format('Y-m-d') }}</span>
                                                <small class="text-muted">{{ $reminder->remind_at->format('h:i A') }}</small>
                                            </div>
                                        </td>
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
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="ti ti-dots-vertical"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" href="{{ route('tenant.reminder.show', $reminder->id) }}"><i class="ti ti-eye me-1"></i> {{ __('View') }}</a></li>
                                                    @if($reminder->reminder_type == 'personal')
                                                    <li><a class="dropdown-item" href="{{ route('tenant.reminder.edit', $reminder->id) }}"><i class="ti ti-edit me-1"></i> {{ __('Edit') }}</a></li>
                                                    @endif
                                                    <li>
                                                        <form action="{{ route('tenant.reminder.toggle-active', $reminder->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="dropdown-item">
                                                                @if($reminder->is_active)
                                                                    <i class="ti ti-bell-off me-1"></i> {{ __('Disable') }}
                                                                @else
                                                                    <i class="ti ti-bell-ringing me-1"></i> {{ __('Enable') }}
                                                                @endif
                                                            </button>
                                                        </form>
                                                    </li>
                                                    @if($reminder->reminder_type == 'personal')
                                                    <li>
                                                        <form action="{{ route('tenant.reminder.destroy', $reminder->id) }}" method="POST" class="d-inline delete-item">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item text-danger">
                                                                <i class="ti ti-trash me-1"></i> {{ __('Delete') }}
                                                            </button>
                                                        </form>
                                                    </li>
                                                    @endif
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="card-footer py-2">
                        <div class="d-flex justify-content-center">
                            {{ $reminders->links() }}
                        </div>
                    </div>
                @else
                    <div class="text-center p-5">
                        <div class="avatar avatar-lg">
                            <div class="avatar-title bg-light-primary text-primary rounded-3">
                                <i class="ti ti-bell-off fs-1"></i>
                            </div>
                        </div>
                        <h5 class="mt-3">{{ __('No Reminders Found') }}</h5>
                        <p class="text-muted">{{ __('You have no reminders for the selected filter.') }}</p>
                        <a href="{{ route('tenant.reminder.create') }}" class="btn btn-primary mt-2">
                            <i class="ti ti-plus me-1"></i> {{ __('Create Reminder') }}
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Delete confirmation
        const deleteButtons = document.querySelectorAll('.delete-item');
        deleteButtons.forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const confirmDelete = confirm("{{ __('Are you sure you want to delete this reminder? This action cannot be undone.') }}");
                if(confirmDelete) {
                    this.submit();
                }
            });
        });
    });
</script>
@endpush
