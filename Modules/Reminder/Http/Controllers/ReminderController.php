<?php

namespace Modules\Reminder\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Reminder\Entities\Reminder;
use Modules\Reminder\Services\ReminderService;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Modules\Tenant\Entities\User;
use Illuminate\Support\Facades\Auth;
use Modules\Document\Entities\Document;

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
        
        $users = User::with('employee')->orderBy('id')->get();
        $documents = Document::orderBy('id')->get();
        return view('reminder::create', compact('reminderTypes', 'recurrencePatterns', 'notificationChannels', 'users', 'documents'));
    }

    /**
     * Store a newly created reminder in storage.
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'reminder_type' => 'required|string',
            'remind_at' => 'required|date',
            'is_recurring' => 'boolean',
            'recurrence_pattern' => 'required_if:is_recurring,1|string',
            'recurrence_interval' => 'required_if:is_recurring,1|integer|min:1',
            'recurrence_end_date' => 'nullable|date|after:remind_at',
            'notification_channels' => 'required|string',
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
        // Prepare data for ReminderService
        $remindAt = Carbon::parse($request->remind_at);
        $options = [
            'is_recurring' => $request->has('is_recurring'),
            'recurrence_pattern' => $request->recurrence_pattern,
            'recurrence_interval' => $request->recurrence_interval,
            'notification_channels' => $request->notification_channels,
            'recipients' => $request->recipients
        ];
        
        if ($request->has('recurrence_end_date')) {
            $options['recurrence_end_date'] = Carbon::parse($request->recurrence_end_date);
        }
        
        // Add remindable if provided
        if ($request->has('remindable_type') && $request->has('remindable_id')) {
            $options['remindable_type'] = $request->remindable_type;
            $options['remindable_id'] = $request->remindable_id;
        }
        
        // Create the reminder
        $this->reminderService->createPersonalReminder(
            $request->title,
            $request->description,
            $remindAt,
            $options
        );
        DB::commit();
        
        return redirect()->route('reminder.index')
            ->with('success', __('Reminder created successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', __('Failed to create reminder: ' . $e->getMessage()));
        }
    }

    /**
     * Show the specified reminder.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        $reminder = Reminder::with('creator')->findOrFail($id);
        
        // Make sure user can view this reminder
        $this->authorize('view', $reminder);
        
        return view('reminder::show', compact('reminder'));
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
        $this->authorize('update', $reminder);
        
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
        
        $users = User::orderBy('name')->get();
        
        return view('reminder::edit', compact('reminder', 'reminderTypes', 'recurrencePatterns', 'notificationChannels', 'users'));
    }

    /**
     * Update the specified reminder in storage.
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $reminder = Reminder::findOrFail($id);
        
        // Make sure user can update this reminder
        $this->authorize('update', $reminder);
        
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'remind_at' => 'required|date',
            'is_recurring' => 'boolean',
            'recurrence_pattern' => 'required_if:is_recurring,1|string',
            'recurrence_interval' => 'required_if:is_recurring,1|integer|min:1',
            'recurrence_end_date' => 'nullable|date|after:remind_at',
            'notification_channels' => 'required|string',
            'recipients' => 'nullable|array',
            'recipients.*' => 'exists:users,id',
            'is_active' => 'boolean'
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        // Update reminder
        $reminder->title = $request->title;
        $reminder->description = $request->description;
        $reminder->remind_at = Carbon::parse($request->remind_at);
        $reminder->is_recurring = $request->has('is_recurring');
        $reminder->notification_channels = $request->notification_channels;
        $reminder->is_active = $request->has('is_active');
        
        if ($reminder->is_recurring) {
            $reminder->recurrence_pattern = $request->recurrence_pattern;
            $reminder->recurrence_interval = $request->recurrence_interval;
            $reminder->recurrence_end_date = $request->has('recurrence_end_date') 
                ? Carbon::parse($request->recurrence_end_date) 
                : null;
        } else {
            $reminder->recurrence_pattern = null;
            $reminder->recurrence_interval = null;
            $reminder->recurrence_end_date = null;
        }
        
        if ($request->has('recipients')) {
            $reminder->recipients = $request->recipients;
        }
        
        // If marked as inactive, cancel the reminder
        if (!$reminder->is_active && $reminder->status != 'cancelled') {
            $reminder->status = 'cancelled';
        }
        // If reactivating a cancelled reminder, set to pending
        else if ($reminder->is_active && $reminder->status == 'cancelled') {
            $reminder->status = 'pending';
        }
        
        $reminder->save();
        
        return redirect()->route('reminder.index')
            ->with('success', __('Reminder updated successfully'));
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
        $this->authorize('delete', $reminder);
        
        $reminder->delete();
        
        return redirect()->route('reminder.index')
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
        $this->authorize('update', $reminder);
        
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
        
        return redirect()->back()
            ->with('success', __('Document expiry reminder created successfully'));
    }
}
