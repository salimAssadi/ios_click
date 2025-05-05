<?php

namespace Modules\Reminder\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReminderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        // Convert checkbox values from "on" to boolean true
        $checkboxFields = ['show_recipients', 'is_recurring'];
        foreach ($checkboxFields as $field) {
            if ($this->has($field) && $this->$field === 'on') {
                $this->merge([$field => true]);
            }
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        // Basic validation rules
        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'reminder_type' => 'required|string|in:personal,document_expiry',
            'remind_date' => 'required|date',
            'remind_time' => 'nullable|string',
            'is_recurring' => 'nullable|boolean',
            'recurrence_pattern' => 'required_if:is_recurring,1,true|string',
            'recurrence_interval' => 'required_if:is_recurring,1,true|integer|min:1',
            'recurrence_end_date' => 'nullable|date|after:remind_date',
            'notification_channels' => 'nullable|array',
            'notification_channels.*' => 'string',
            'show_recipients' => 'nullable|boolean',
            'recipients' => 'nullable|array|required_if:show_recipients,1,true',
            'recipients.*' => 'exists:users,id',
        ];
        
        // Add specific rules for document expiry reminders
        if ($this->reminder_type == 'document_expiry') {
            $rules['document_id'] = 'required|exists:documents,id';
            $rules['days_before_expiry'] = 'required|integer|min:1|max:365';
        }
        
        return $rules;
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'title.required' => 'Reminder title is required',
            'reminder_type.required' => 'Reminder type is required',
            'remind_date.required' => 'Reminder date is required',
            'document_id.required' => 'Please select a document when creating a document expiry reminder',
            'days_before_expiry.required' => 'Please specify the number of days before expiry',
            'recipients.array' => 'Recipients must be a valid list',
            'recipients.*.exists' => 'One of the selected recipients does not exist',
            'recipients.required_if' => 'At least one recipient must be specified when sending reminder to others'
        ];
    }
}
