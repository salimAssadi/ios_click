@extends('tenant::layouts.app')

@section('title', __('Edit Reminder'))

@section('content')
<div class="pc-container">
    <div class="pc-content">
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <div class="page-header-title">
                            <h5 class="m-b-10">{{ __('Edit Reminder') }}</h5>
                        </div>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('tenant.dashboard') }}">{{ __('Dashboard') }}</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('tenant.reminder.index') }}">{{ __('Reminders') }}</a></li>
                            <li class="breadcrumb-item">{{ __('Edit') }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card">
                    <div class="card-header">
                        <h5>{{ __('Edit Reminder') }}</h5>
                        <p class="text-muted mb-0">{{ __('Update the reminder details below.') }}</p>
                    </div>
                    
                    <div class="card-body">
                        <form action="{{ route('tenant.reminder.update', $reminder->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <div class="form-group">
                                        <label class="form-label">{{ __('Title') }} <span class="text-danger">*</span></label>
                                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $reminder->title) }}" required>
                                        @error('title')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-12 mb-3">
                                    <div class="form-group">
                                        <label class="form-label">{{ __('Description') }}</label>
                                        <textarea name="description" rows="3" class="form-control @error('description') is-invalid @enderror">{{ old('description', $reminder->description) }}</textarea>
                                        @error('description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <!-- Only show date/time fields for personal reminders -->
                                @if($reminder->reminder_type == 'personal')
                                    <div class="col-md-6 mb-3">
                                        <div class="form-group">
                                            <label class="form-label">{{ __('Remind Date') }} <span class="text-danger">*</span></label>
                                            <input type="date" name="remind_date" class="form-control @error('remind_date') is-invalid @enderror" value="{{ old('remind_date', $reminder->remind_at->format('Y-m-d')) }}">
                                            @error('remind_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <div class="form-group">
                                            <label class="form-label">{{ __('Remind Time') }}</label>
                                            <input type="time" name="remind_time" class="form-control @error('remind_time') is-invalid @enderror" value="{{ old('remind_time', $reminder->remind_at->format('H:i')) }}">
                                            @error('remind_time')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-12 mb-3">
                                        <div class="form-check">
                                            <input type="checkbox" name="is_recurring" id="is_recurring" class="form-check-input" value="1" {{ old('is_recurring', $reminder->is_recurring) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_recurring">{{ __('Recurring Reminder') }}</label>
                                        </div>
                                    </div>
                                    
                                    <div class="row recurrence-fields" style="{{ $reminder->is_recurring ? '' : 'display: none;' }}">
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label class="form-label">{{ __('Recurrence Pattern') }}</label>
                                                <select name="recurrence_pattern" class="form-select @error('recurrence_pattern') is-invalid @enderror">
                                                    <option value="daily" {{ old('recurrence_pattern', $reminder->recurrence_pattern) == 'daily' ? 'selected' : '' }}>{{ __('Daily') }}</option>
                                                    <option value="weekly" {{ old('recurrence_pattern', $reminder->recurrence_pattern) == 'weekly' ? 'selected' : '' }}>{{ __('Weekly') }}</option>
                                                    <option value="monthly" {{ old('recurrence_pattern', $reminder->recurrence_pattern) == 'monthly' ? 'selected' : '' }}>{{ __('Monthly') }}</option>
                                                    <option value="yearly" {{ old('recurrence_pattern', $reminder->recurrence_pattern) == 'yearly' ? 'selected' : '' }}>{{ __('Yearly') }}</option>
                                                </select>
                                                @error('recurrence_pattern')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label class="form-label">{{ __('Recurrence Interval') }}</label>
                                                <input type="number" name="recurrence_interval" class="form-control @error('recurrence_interval') is-invalid @enderror" value="{{ old('recurrence_interval', $reminder->recurrence_interval ?? 1) }}" min="1">
                                                <small class="text-muted">{{ __('How often to repeat (e.g., every 2 weeks)') }}</small>
                                                @error('recurrence_interval')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-12 mb-3">
                                            <div class="form-group">
                                                <label class="form-label">{{ __('End Date') }} <small class="text-muted">({{ __('Optional') }})</small></label>
                                                <input type="date" name="recurrence_end_date" class="form-control @error('recurrence_end_date') is-invalid @enderror" value="{{ old('recurrence_end_date', $reminder->recurrence_end_date ? $reminder->recurrence_end_date->format('Y-m-d') : '') }}">
                                                <small class="text-muted">{{ __('Leave empty for no end date') }}</small>
                                                @error('recurrence_end_date')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                
                                <div class="col-md-12 mb-3">
                                    <div class="form-group">
                                        <label class="form-label">{{ __('Notification Channels') }}</label>
                                        @php
                                            $channels = explode(',', $reminder->notification_channels);
                                        @endphp
                                        <div class="form-check">
                                            <input type="checkbox" name="notification_channels[]" id="channel_email" class="form-check-input" value="email" {{ in_array('email', $channels) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="channel_email">{{ __('Email') }}</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" name="notification_channels[]" id="channel_system" class="form-check-input" value="system" {{ in_array('system', $channels) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="channel_system">{{ __('System Notification') }}</label>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-12 mt-3">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ti ti-device-floppy me-1"></i> {{ __('Update Reminder') }}
                                    </button>
                                    <a href="{{ route('tenant.reminder.show', $reminder->id) }}" class="btn btn-light">
                                        <i class="ti ti-arrow-left me-1"></i> {{ __('Cancel') }}
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const isRecurringCheckbox = document.getElementById('is_recurring');
        const recurrenceFields = document.querySelector('.recurrence-fields');
        
        // Handle recurring checkbox change
        function handleRecurringChange() {
            if (isRecurringCheckbox && recurrenceFields) {
                if (isRecurringCheckbox.checked) {
                    recurrenceFields.style.display = 'flex';
                } else {
                    recurrenceFields.style.display = 'none';
                }
            }
        }
        
        // Event listener
        if (isRecurringCheckbox) {
            isRecurringCheckbox.addEventListener('change', handleRecurringChange);
        }
    });
</script>
@endpush
