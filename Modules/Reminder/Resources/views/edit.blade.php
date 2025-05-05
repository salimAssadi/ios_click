@extends('tenant::layouts.app')

@section('page-title', __('Edit Reminder'))
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('tenant.dashboard') }}">{{ __('Dashboard') }}</a></li>
<li class="breadcrumb-item"><a href="{{ route('tenant.reminder.index') }}">{{ __('Reminders') }}</a></li>
<li class="breadcrumb-item">{{ __('Edit') }}</li>
@endsection

@section('content')
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
                        
                        <div class="col-md-12 mb-3">
                            <div class="form-group">
                                <label class="form-label">{{ __('Reminder Type') }} <span class="text-danger">*</span></label>
                                <select name="reminder_type" id="reminder_type" class="form-control @error('reminder_type') is-invalid @enderror" required>
                                    <option value="personal" {{ old('reminder_type', $reminder->reminder_type) == 'personal' ? 'selected' : '' }}>{{ __('Personal Reminder') }}</option>
                                    <option value="document_expiry" {{ old('reminder_type', $reminder->reminder_type) == 'document_expiry' ? 'selected' : '' }}>{{ __('Document Expiry') }}</option>
                                </select>
                                @error('reminder_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- Document selection section - shown/hidden with JS -->
                        <div id="document_section" class="row" style="{{ $reminder->reminder_type == 'document_expiry' ? '' : 'display: none;' }}">
                            <div class="col-md-8 mb-3">
                                <div class="form-group">
                                    <label class="form-label">{{ __('Document') }} <span class="text-danger">*</span></label>
                                    <select name="document_id" class="form-control @error('document_id') is-invalid @enderror">
                                        <option value="">{{ __('Select Document') }}</option>
                                        @foreach($documents as $document)
                                            <option value="{{ $document->id }}" {{ old('document_id', $reminder->document_id) == $document->id ? 'selected' : '' }}>
                                                {{ $document->name }} ({{ $document->expiry_date }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('document_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <div class="form-group">
                                    <label class="form-label">{{ __('Days Before Expiry') }} <span class="text-danger">*</span></label>
                                    <input type="number" name="days_before_expiry" class="form-control @error('days_before_expiry') is-invalid @enderror" value="{{ old('days_before_expiry', $reminder->days_before_expiry) }}" min="1" max="365">
                                    @error('days_before_expiry')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="form-label">{{ __('Remind Date') }} <span class="text-danger">*</span></label>
                                <input type="date" name="remind_date" class="form-control @error('remind_date') is-invalid @enderror" value="{{ old('remind_date', $reminder->remind_date ?? $reminder->remind_at->format('Y-m-d')) }}">
                                @error('remind_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="form-label">{{ __('Remind Time') }}</label>
                                <input type="time" name="remind_time" class="form-control @error('remind_time') is-invalid @enderror" value="{{ old('remind_time', $reminder->remind_time ?? $reminder->remind_at->format('H:i')) }}">
                                @error('remind_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <div class="form-check form-switch">
                                <input type="checkbox" name="is_recurring" id="is_recurring" class="form-check-input" {{ old('is_recurring', $reminder->is_recurring ?? false) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_recurring">{{ __('Recurring Reminder') }}</label>
                            </div>
                        </div>
                        
                        <div class="row recurrence-fields" style="{{ old('is_recurring', $reminder->is_recurring ?? false) ? '' : 'display: none;' }}">
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label class="form-label">{{ __('Recurrence Pattern') }}</label>
                                    <select name="recurrence_pattern" class="form-control @error('recurrence_pattern') is-invalid @enderror">
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
                                    <small class="text-muted">{{ __('How often the reminder should recur (1 = every time, 2 = every other time, etc.)') }}</small>
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
                        
                        <div class="col-md-12 mb-3">
                            <div class="form-group">
                                <label class="form-label">{{ __('Notification Channels') }}</label>
                                @php
                                    // Get notification channels from related models
                                    $notificationChannels = $reminder->notifications->pluck('channel')->toArray();
                                @endphp
                                <div class="form-check">
                                    <input type="checkbox" name="notification_channels[]" id="channel_email" class="form-check-input" value="email" {{ in_array('email', old('notification_channels', $notificationChannels)) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="channel_email">{{ __('Email') }}</label>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" name="notification_channels[]" id="channel_system" class="form-check-input" value="system" {{ in_array('system', old('notification_channels', $notificationChannels)) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="channel_system">{{ __('System Notification') }}</label>
                                </div>
                                @error('notification_channels')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-12 mb-3 recipients-container" style="display: {{ old('show_recipients', $reminder->recipients->count() > 0) ? 'block' : 'none' }};">
                            <div class="form-group">
                                <label class="form-label">{{ __('Recipients') }}</label>
                                <p class="text-muted small mb-2">{{ __('Select users who should receive this reminder.') }}</p>
                                
                                <div class="select-all-recipients mb-2">
                                    <div class="form-check">
                                        <input type="checkbox" id="select_all_recipients" class="form-check-input">
                                        <label class="form-check-label fw-bold" for="select_all_recipients">{{ __('Select All') }}</label>
                                    </div>
                                </div>
                                
                                <div class="recipient-list" style="max-height: 200px; overflow-y: auto; border: 1px solid #eee; padding: 10px; border-radius: 5px;">
                                    @if(isset($users) && count($users) > 0)
                                        @foreach($users as $user)
                                            @if($user->id != auth('tenant')->user()->id) {{-- Skip current user --}}
                                            <div class="form-check">
                                                <input type="checkbox" name="recipients[]" id="recipient_{{ $user->id }}" class="form-check-input recipient-checkbox" value="{{ $user->id }}" {{ in_array($user->id, old('recipients', $reminder->recipients->pluck('user_id')->toArray())) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="recipient_{{ $user->id }}">
                                                    {{ $user->name }} ({{ $user->email }})
                                                </label>
                                            </div>
                                            @endif
                                        @endforeach
                                    @else
                                        <p class="text-muted mb-0">{{ __('No users available') }}</p>
                                    @endif
                                </div>
                                @error('recipients')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
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
@endsection

@push('script-page')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Selección de elementos
        const reminderTypeSelect = document.getElementById('reminder_type');
        const documentSection = document.getElementById('document_section');
        const isRecurringCheckbox = document.getElementById('is_recurring');
        const recurrenceFields = document.querySelector('.recurrence-fields');
        const showRecipientsCheckbox = document.getElementById('show_recipients');
        const recipientsSection = document.getElementById('recipients_section');
        
        // Función para mostrar/ocultar sección de documentos
        function handleReminderTypeChange() {
            if (reminderTypeSelect && documentSection) {
                if (reminderTypeSelect.value === 'document_expiry') {
                    documentSection.style.display = 'flex';
                } else {
                    documentSection.style.display = 'none';
                }
            }
        }
        
        // Función para mostrar/ocultar campos de recurrencia
        function handleRecurringChange() {
            if (isRecurringCheckbox && recurrenceFields) {
                if (isRecurringCheckbox.checked) {
                    recurrenceFields.style.display = 'flex';
                } else {
                    recurrenceFields.style.display = 'none';
                }
            }
        }
        
        // Función para mostrar/ocultar sección de destinatarios
        function handleShowRecipientsChange() {
            if (showRecipientsCheckbox && recipientsSection) {
                if (showRecipientsCheckbox.checked) {
                    recipientsSection.style.display = 'block';
                } else {
                    recipientsSection.style.display = 'none';
                }
            }
        }
        
        // Asignar event listeners
        if (reminderTypeSelect) {
            reminderTypeSelect.addEventListener('change', handleReminderTypeChange);
            // Inicializar al cargar
            handleReminderTypeChange();
        }
        
        if (isRecurringCheckbox) {
            isRecurringCheckbox.addEventListener('change', handleRecurringChange);
        }
        
        if (showRecipientsCheckbox) {
            showRecipientsCheckbox.addEventListener('change', handleShowRecipientsChange);
        }
    });
</script>
@endpush
