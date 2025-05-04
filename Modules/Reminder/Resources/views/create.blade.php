@extends('tenant::layouts.app')

@section('page-title', __('Create Reminder'))
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('tenant.dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('tenant.reminder.index') }}">{{ __('Reminders') }}</a></li>
    <li class="breadcrumb-item">{{ __('Create') }}</li>
    @endsection

@section('content')  
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card">
                    <div class="card-header">
                        <h5>{{ __('Create New Reminder') }}</h5>
                        <p class="text-muted mb-0">{{ __('Fill in the details to create a new reminder.') }}</p>
                    </div>
                    
                    <div class="card-body">
                        <form action="{{ route('tenant.reminder.store') }}" method="POST">
                            @csrf
                            
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <div class="form-group">
                                        <label class="form-label">{{ __('Reminder Type') }} <span class="text-danger">*</span></label>
                                        <select name="reminder_type" id="reminder_type" class="form-select @error('reminder_type') is-invalid @enderror" required>
                                            <option value="">{{ __('Select Reminder Type') }}</option>
                                            <option value="personal" {{ old('reminder_type', request()->get('type')) == 'personal' ? 'selected' : '' }}>{{ __('Personal Reminder') }}</option>
                                            <option value="document_expiry" {{ old('reminder_type', request()->get('type')) == 'document_expiry' ? 'selected' : '' }}>{{ __('Document Expiry Reminder') }}</option>
                                        </select>
                                        @error('reminder_type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-12 mb-3">
                                    <div class="form-group">
                                        <label class="form-label">{{ __('Title') }} <span class="text-danger">*</span></label>
                                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" required>
                                        @error('title')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-12 mb-3">
                                    <div class="form-group">
                                        <label class="form-label">{{ __('Description') }}</label>
                                        <textarea name="description" rows="3" class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                                        @error('description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <!-- Document Selection - Only visible for document_expiry reminders -->
                                <div class="col-md-12 mb-3 document-fields" style="display: none;">
                                    <div class="form-group">
                                        <label class="form-label">{{ __('Select Document') }} <span class="text-danger">*</span></label>
                                        <select name="document_id" id="document_id" class="form-select @error('document_id') is-invalid @enderror">
                                            <option value="">{{ __('Select Document') }}</option>
                                            @if(isset($documents))
                                                @foreach($documents as $document)
                                                    <option value="{{ $document->id }}" {{ old('document_id') == $document->id ? 'selected' : '' }}>
                                                        {{ $document->title }} ({{ $document->document_number }})
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                        @error('document_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-12 mb-3 document-fields" style="display: none;">
                                    <div class="form-group">
                                        <label class="form-label">{{ __('Days Before Expiry') }} <span class="text-danger">*</span></label>
                                        <input type="number" name="days_before_expiry" class="form-control @error('days_before_expiry') is-invalid @enderror" value="{{ old('days_before_expiry', 30) }}" min="1" max="365">
                                        <small class="text-muted">{{ __('Number of days before expiry to send the reminder') }}</small>
                                        @error('days_before_expiry')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <!-- Date and Time Fields - Only visible for personal reminders -->
                                <div class="col-md-6 mb-3 personal-fields" style="display: none;">
                                    <div class="form-group">
                                        <label class="form-label">{{ __('Remind Date') }} <span class="text-danger">*</span></label>
                                        <input type="date" name="remind_date" class="form-control @error('remind_date') is-invalid @enderror" value="{{ old('remind_date', date('Y-m-d')) }}">
                                        @error('remind_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3 personal-fields" style="display: none;">
                                    <div class="form-group">
                                        <label class="form-label">{{ __('Remind Time') }}</label>
                                        <input type="time" name="remind_time" class="form-control @error('remind_time') is-invalid @enderror" value="{{ old('remind_time', '09:00') }}">
                                        @error('remind_time')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-12 mb-3 personal-fields" style="display: none;">
                                    <div class="form-check">
                                        <input type="checkbox" name="is_recurring" id="is_recurring" class="form-check-input" value="1" {{ old('is_recurring') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_recurring">{{ __('Recurring Reminder') }}</label>
                                    </div>
                                </div>
                                
                                <div class="row recurrence-fields" style="display: none;">
                                    <div class="col-md-6 mb-3">
                                        <div class="form-group">
                                            <label class="form-label">{{ __('Recurrence Pattern') }}</label>
                                            <select name="recurrence_pattern" class="form-select @error('recurrence_pattern') is-invalid @enderror">
                                                <option value="daily" {{ old('recurrence_pattern') == 'daily' ? 'selected' : '' }}>{{ __('Daily') }}</option>
                                                <option value="weekly" {{ old('recurrence_pattern') == 'weekly' ? 'selected' : '' }}>{{ __('Weekly') }}</option>
                                                <option value="monthly" {{ old('recurrence_pattern') == 'monthly' ? 'selected' : '' }}>{{ __('Monthly') }}</option>
                                                <option value="yearly" {{ old('recurrence_pattern') == 'yearly' ? 'selected' : '' }}>{{ __('Yearly') }}</option>
                                            </select>
                                            @error('recurrence_pattern')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <div class="form-group">
                                            <label class="form-label">{{ __('Recurrence Interval') }}</label>
                                            <input type="number" name="recurrence_interval" class="form-control @error('recurrence_interval') is-invalid @enderror" value="{{ old('recurrence_interval', 1) }}" min="1">
                                            <small class="text-muted">{{ __('How often to repeat (e.g., every 2 weeks)') }}</small>
                                            @error('recurrence_interval')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-12 mb-3">
                                        <div class="form-group">
                                            <label class="form-label">{{ __('End Date') }} <small class="text-muted">({{ __('Optional') }})</small></label>
                                            <input type="date" name="recurrence_end_date" class="form-control @error('recurrence_end_date') is-invalid @enderror" value="{{ old('recurrence_end_date') }}">
                                            <small class="text-muted">{{ __('Leave empty for no end date') }}</small>
                                            @error('recurrence_end_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-12 mb-3">
                                    <div class="form-group">
                                        <label class="form-label">{{ __('Notification Channels') }}</label>
                                        <div class="form-check">
                                            <input type="checkbox" name="notification_channels[]" id="channel_email" class="form-check-input" value="email" {{ is_array(old('notification_channels')) && in_array('email', old('notification_channels')) ? 'checked' : 'checked' }}>
                                            <label class="form-check-label" for="channel_email">{{ __('Email') }}</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" name="notification_channels[]" id="channel_system" class="form-check-input" value="system" {{ is_array(old('notification_channels')) && in_array('system', old('notification_channels')) ? 'checked' : 'checked' }}>
                                            <label class="form-check-label" for="channel_system">{{ __('System Notification') }}</label>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-12 mt-3">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ti ti-device-floppy me-1"></i> {{ __('Create Reminder') }}
                                    </button>
                                    <a href="{{ route('tenant.reminder.index') }}" class="btn btn-light">
                                        <i class="ti ti-arrow-left me-1"></i> {{ __('Cancel') }}
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
@endsection

@push('script-page')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const reminderTypeSelect = document.getElementById('reminder_type');
        const documentFields = document.querySelectorAll('.document-fields');
        const personalFields = document.querySelectorAll('.personal-fields');
        const isRecurringCheckbox = document.getElementById('is_recurring');
        const recurrenceFields = document.querySelector('.recurrence-fields');
        
        // Handle reminder type change
        function handleReminderTypeChange() {
            const selectedType = reminderTypeSelect.value;
            
            if (selectedType === 'document_expiry') {
                documentFields.forEach(field => field.style.display = 'block');
                personalFields.forEach(field => field.style.display = 'none');
                recurrenceFields.style.display = 'none';
            } else if (selectedType === 'personal') {
                documentFields.forEach(field => field.style.display = 'none');
                personalFields.forEach(field => field.style.display = 'block');
                handleRecurringChange();
            } else {
                documentFields.forEach(field => field.style.display = 'none');
                personalFields.forEach(field => field.style.display = 'none');
                recurrenceFields.style.display = 'none';
            }
        }
        
        // Handle recurring checkbox change
        function handleRecurringChange() {
            if (isRecurringCheckbox.checked) {
                recurrenceFields.style.display = 'flex';
            } else {
                recurrenceFields.style.display = 'none';
            }
        }
        
        // Initial state setup
        handleReminderTypeChange();
        
        // Event listeners
        reminderTypeSelect.addEventListener('change', handleReminderTypeChange);
        isRecurringCheckbox.addEventListener('change', handleRecurringChange);
    });
</script>
@endpush
