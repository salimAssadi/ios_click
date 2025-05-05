<?php

namespace Modules\Reminder\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Modules\Document\Entities\Document;
use Illuminate\Notifications\DatabaseNotification;
use Modules\Reminder\Entities\Recipient;
use Modules\Reminder\Entities\Reminder;
use Modules\Reminder\Http\Requests\StoreReminderRequest;
use Modules\Reminder\Http\Requests\UpdateReminderRequest;
use Modules\Task\Entities\Task;
use Modules\Reminder\Services\ReminderService;
use Carbon\Carbon;
use Modules\Tenant\Entities\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Reminder\Notifications\ReminderNotification;


class ReminderController extends Controller
{
    /**
     * The reminder service.
     *
     * @var \Modules\Reminder\Services\ReminderService
     */
    protected $reminderService;

    /**
     * Create a new controller instance.
     *
     * @param \Modules\Reminder\Services\ReminderService $reminderService
     * @return void
     */
    public function __construct(ReminderService $reminderService)
    {
        $this->reminderService = $reminderService;
    }

    /**
     * Display a listing of the reminders.
     * @return Renderable
     */
    public function index(Request $request)
    {
        $query = Reminder::with(['remindable']);
        
        // Apply filters
        if ($request->has('type')) {
            $query->where('reminder_type', $request->type);
        }
        
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        // By default, only show reminders created by current user or where they are a recipient
        // unless they have the 'view_all_reminders' permission
        if (!auth()->user()->can('view_all_reminders')) {
            $query->where(function($q) {
                $q->where('created_by', auth()->id())
                  ->orWhereJsonContains('recipients', (string) auth()->id());
            });
        }
        
        $reminders = $query->orderBy('remind_at', 'asc')->paginate(10);
        
        return view('reminder::index', compact('reminders'));
    }

    /**
     * Show the form for creating a new reminder.
     * @return Renderable
     */
    public function create()
    {
        $reminderTypes = [
            'personal' => __('Personal Reminder'),
            'document_expiry' => __('Document Expiry Reminder'),
            'task' => __('Task Reminder'),
            'meeting' => __('Meeting Reminder'),
            'other' => __('Other')
        ];
        
        $recurrencePatterns = [
            'daily' => __('Daily'),
            'weekly' => __('Weekly'),
            'monthly' => __('Monthly'),
            'yearly' => __('Yearly')
        ];
        
        $notificationChannels = [
            'email' => __('Email'),
            'system' => __('System Notification'),
            'email,system' => __('Both Email and System')
        ];
        
        $users = User::whereHas('employee')->with('employee')->orderBy('id')->get();
        $documents = Document::orderBy('id')->get();
        return view('reminder::create', compact('reminderTypes', 'recurrencePatterns', 'notificationChannels', 'users', 'documents'));
    }

    /**
     * Store a newly created reminder in storage.
     * @param StoreReminderRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreReminderRequest $request)
    {
        // Get the validated data
        $validated = $request->validated();
        
        try {
            // Create the reminder
            $reminder = new Reminder();
            $reminder->title = $validated['title'];
            $reminder->description = $validated['description'] ?? null;
            $reminder->reminder_type = $validated['reminder_type'];
            $reminder->user_id = auth()->id();
            
            if ($reminder->reminder_type == 'document_expiry') {
                $reminder->document_id = $validated['document_id'];
                $reminder->days_before_expiry = $validated['days_before_expiry'];
            }
            
            $reminder->remind_date = $validated['remind_date'];
            $reminder->remind_time = $validated['remind_time'] ?? null;
            
            // Handle recurrence settings
            if (isset($validated['is_recurring']) && $validated['is_recurring']) {
                $reminder->recurrence_pattern = $validated['recurrence_pattern'];
                $reminder->recurrence_interval = $validated['recurrence_interval'];
                $reminder->recurrence_end_date = $validated['recurrence_end_date'] ?? null;
            }
            
            $reminder->save();
            
            // Update notification preferences using Laravel's notification system
            if (isset($validated['notification_channels']) && is_array($validated['notification_channels'])) {
                $reminder->notification_channels = implode(',', $validated['notification_channels']);
                $reminder->save();
            } else {
                $reminder->notification_channels = null;
                $reminder->save();
            }
            
            // First we need to delete the old Notification objects since we're not using them anymore
            // This is only needed temporarily during the migration to the new notification system
            DB::table('reminder_notifications')->where('reminder_id', $reminder->id)->delete();
            
            // Handle recipients if show_recipients is enabled
            if (isset($validated['show_recipients']) && $validated['show_recipients'] && isset($validated['recipients'])) {
                foreach ($validated['recipients'] as $userId) {
                    $recipient = new Recipient();
                    $recipient->reminder_id = $reminder->id;
                    $recipient->user_id = $userId;
                    $recipient->save();
                    
                    // Send notification to this user
                    $user = User::find($userId);
                    if ($user) {
                        $user->notify(new ReminderNotification($reminder, $reminder->getNotificationChannels()));
                    }
                }
            }
            
            return redirect()->route('reminders.index')
                ->with('success', 'Reminder created successfully');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error creating reminder: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show the form for editing the specified reminder.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $reminder = Reminder::findOrFail($id);
        
        // Make sure user can edit this reminder
        // $this->authorize('update', $reminder);
        $reminderTypes = [
            'personal' => __('Personal Reminder'),
            'document_expiry' => __('Document Expiry Reminder'),
            'task' => __('Task Reminder'),
            'meeting' => __('Meeting Reminder'),
            'other' => __('Other')
        ];
        
        $recurrencePatterns = [
            'daily' => __('Daily'),
            'weekly' => __('Weekly'),
            'monthly' => __('Monthly'),
            'yearly' => __('Yearly')
        ];
        
        $notificationChannels = [
            'email' => __('Email'),
            'system' => __('System Notification'),
            'email,system' => __('Both Email and System')
        ];
        
        $users = User::whereHas('employee')->with('employee')->orderBy('id')->get();
        $documents = Document::orderBy('id')->get();
        
        return view('reminder::edit', compact('reminder', 'reminderTypes', 'recurrencePatterns', 'documents', 'notificationChannels', 'users'));
    }

    /**
     * Update the specified reminder in storage.
     * @param UpdateReminderRequest $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateReminderRequest $request, $id)
    {
        // Get the validated data
        $validated = $request->validated();
        
        try {
            $reminder = Reminder::findOrFail($id);
            
            // Verify ownership or admin permissions
            if ($reminder->user_id != auth('tenant')->id() && !auth('tenant')->user()->hasRole('super admin')) {
                return redirect()->route('tenant.reminder.index')
                    ->with('error', 'You do not have permission to update this reminder');
            }
            
            // Update reminder fields
            $reminder->title = $validated['title'];
            $reminder->description = $validated['description'] ?? null;
            $reminder->reminder_type = $validated['reminder_type'];
            
            if ($reminder->reminder_type == 'document_expiry') {
                $reminder->document_id = $validated['document_id'];
                $reminder->days_before_expiry = $validated['days_before_expiry'];
            } else {
                $reminder->document_id = null;
                $reminder->days_before_expiry = null;
            }
            
            $reminder->remind_date = $validated['remind_date'];
            $reminder->remind_time = $validated['remind_time'] ?? null;
            
            // Handle recurrence settings
            if (isset($validated['is_recurring']) && $validated['is_recurring']) {
                $reminder->recurrence_pattern = $validated['recurrence_pattern'];
                $reminder->recurrence_interval = $validated['recurrence_interval'];
                $reminder->recurrence_end_date = $validated['recurrence_end_date'] ?? null;
            } else {
                $reminder->recurrence_pattern = null;
                $reminder->recurrence_interval = null;
                $reminder->recurrence_end_date = null;
            }
            
            $reminder->save();
            
            // Update notification preferences
            // First delete existing notifications
            Notification::where('reminder_id', $reminder->id)->delete();
            
            // Then add the new ones
            if (isset($validated['notification_channels']) && is_array($validated['notification_channels'])) {
                foreach ($validated['notification_channels'] as $channel) {
                    $notification = new Notification();
                    $notification->reminder_id = $reminder->id;
                    $notification->channel = $channel;
                    $notification->save();
                }
            }
            
            // First we need to delete the old Notification objects since we're not using them anymore
            // This is only needed temporarily during the migration to the new notification system
            DB::table('reminder_notifications')->where('reminder_id', $reminder->id)->delete();
            
            // Handle recipients
            // First delete existing recipients
            Recipient::where('reminder_id', $reminder->id)->delete();
            
            // Then add new ones if show_recipients is enabled
            if (isset($validated['show_recipients']) && $validated['show_recipients'] && isset($validated['recipients'])) {
                foreach ($validated['recipients'] as $userId) {
                    $recipient = new Recipient();
                    $recipient->reminder_id = $reminder->id;
                    $recipient->user_id = $userId;
                    $recipient->save();
                    
                    // Send notification to this user
                    $user = User::find($userId);
                    if ($user) {
                        $user->notify(new ReminderNotification($reminder, $reminder->getNotificationChannels()));
                    }
                }
            }
            
            return redirect()->route('tenant.reminder.index')
                ->with('success', 'Reminder updated successfully');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating reminder: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show the specified reminder.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        $reminder = Reminder::with(['notifications', 'recipients', 'recipients.user'])->findOrFail($id);
        
        // Verify ownership or admin permissions
        if ($reminder->user_id != auth('tenant')->id() && !auth('tenant')->user()->hasRole('super admin')) {
            return redirect()->route('reminders.index')
                ->with('error', 'You do not have permission to view this reminder');
        }
        
        return view('reminder::show', compact('reminder'));
    }

    /**
     * Remove the specified reminder from storage.
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $reminder = Reminder::findOrFail($id);
        
        // Make sure user can delete this reminder
        // $this->authorize('delete', $reminder);
        
        $reminder->delete();
        
        return redirect()->route('tenant.reminder.index')
            ->with('success', __('Reminder deleted successfully'));
    }
    
    /**
     * Toggle the active status of a reminder.
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toggleActive($id)
    {
        $reminder = Reminder::findOrFail($id);
        
        // Make sure user can update this reminder
        // $this->authorize('update', $reminder);
        
        $reminder->is_active = !$reminder->is_active;
        
        // Update status based on active flag
        if (!$reminder->is_active && $reminder->status != 'cancelled') {
            $reminder->status = 'cancelled';
        }
        // If reactivating a cancelled reminder, set to pending
        else if ($reminder->is_active && $reminder->status == 'cancelled') {
            $reminder->status = 'pending';
        }
        
        $reminder->save();
        
        $status = $reminder->is_active ? __('activated') : __('deactivated');
        
        return redirect()->back()
            ->with('success', __('Reminder :status successfully', ['status' => $status]));
    }
    
    /**
     * Get the current user's personal reminders.
     * @return Renderable
     */
    public function myReminders()
    {
        $reminders = Reminder::where(function($query) {
                $query->where('created_by', Auth::id())
                      ->orWhereJsonContains('recipients', (string) Auth::id());
            })
            ->where('is_active', true)
            ->orderBy('remind_at', 'asc')
            ->paginate(10);
            
        return view('reminder::my-reminders', compact('reminders'));
    }
    
    /**
     * Create a document expiry reminder.
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function createDocumentExpiryReminder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'document_id' => 'required|integer',
            'days_before_expiry' => 'required|integer|min:1|max:365',
            'recipients' => 'nullable|array',
            'recipients.*' => 'exists:users,id'
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        DB::beginTransaction();
        try {
            // Get the document
            $documentClass = 'Modules\Document\Entities\Document';
            $document = $documentClass::findOrFail($request->document_id);
            
            // Create options array
            $options = [
                'recipients' => $request->recipients
            ];
            
            // Create the reminder
            $this->reminderService->createDocumentExpiryReminder(
                $document,
                $request->days_before_expiry,
                $options
            );
            
            DB::commit();
            
            return redirect()->back()
                ->with('success', __('Document expiry reminder created successfully'));
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', __('Failed to create document reminder: ') . $e->getMessage());
        }
    }
}
