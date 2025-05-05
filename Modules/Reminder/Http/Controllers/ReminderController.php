<?php

namespace Modules\Reminder\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Modules\Document\Entities\Document;
use Modules\Reminder\Entities\Recipient;
use Modules\Reminder\Entities\Reminder;
use Modules\Reminder\Http\Requests\StoreReminderRequest;
use Modules\Reminder\Http\Requests\UpdateReminderRequest;
use Modules\Reminder\Services\ReminderService;
use Modules\Task\Entities\Task;
use Modules\Tenant\Entities\User;

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
            $query->where(function ($q) {
                $q->where('created_by', auth()->id())
                    ->orWhereHas('recipients', function ($q) {
                        $q->where('user_id', auth()->id());
                    });
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
            'other' => __('Other'),
        ];

        $recurrencePatterns = [
            'daily' => __('Daily'),
            'weekly' => __('Weekly'),
            'monthly' => __('Monthly'),
            'yearly' => __('Yearly'),
        ];

        $notificationChannels = [
            'email' => __('Email'),
            'system' => __('System Notification'),
            'email,system' => __('Both Email and System'),
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
            DB::beginTransaction();
            // Create the reminder
            $reminder = new Reminder();
            $reminder->title = $validated['title'];
            $reminder->description = $validated['description'] ?? null;
            $reminder->reminder_type = $validated['reminder_type'];
            $reminder->created_by = auth('tenant')->id();

            // Set remind_at datetime from date and optional time
            if (isset($validated['remind_date'])) {
                $date = $validated['remind_date'];
                $time = $validated['remind_time'] ?? '00:00:00';
                $reminder->remind_at = $date . ' ' . $time;
            }

            // Set remindable relationship based on reminder type
            if ($reminder->reminder_type == 'document_expiry' && isset($validated['document_id'])) {
                // Get the document and its latest version
                $document = Document::findOrFail($validated['document_id']);
                $latestVersion = $document->lastversion; // Assuming this relationship exists

                if ($latestVersion) {
                    // Set the remindable to point to the document version
                    $reminder->remindable_type = 'Modules\Document\Entities\DocumentVersion';
                    $reminder->remindable_id = $latestVersion->id;
                } else {
                    // Fallback to document if no version exists
                    $reminder->remindable_type = Document::class;
                    $reminder->remindable_id = $document->id;
                }

                $reminder->metadata = json_encode([
                    'days_before_expiry' => $validated['days_before_expiry'] ?? 7,
                    'document_id' => $document->id,
                ]);
            } elseif ($reminder->reminder_type == 'task' && isset($validated['task_id'])) {
                $reminder->remindable_type = Task::class;
                $reminder->remindable_id = $validated['task_id'];
            } elseif ($reminder->reminder_type == 'personal') {
                // For personal reminders, the remindable is the user who created it
                $reminder->remindable_type = User::class;
                $reminder->remindable_id = auth('tenant')->id();

                // For personal reminders, add creator as a recipient automatically
                $creatorAsRecipient = true;
            }

            // Handle recurrence settings
            if (isset($validated['is_recurring']) && $validated['is_recurring']) {
                $reminder->is_recurring = true;
                $reminder->recurrence_pattern = $validated['recurrence_pattern'];
                $reminder->recurrence_interval = $validated['recurrence_interval'];
                $reminder->recurrence_end_date = $validated['recurrence_end_date'] ?? null;
            }

            // Set notification channels
            if (isset($validated['notification_channels']) && is_array($validated['notification_channels'])) {
                $reminder->notification_channels = implode(',', $validated['notification_channels']);
            }

            $reminder->save();

            // Handle recipients using the dedicated recipients table
            if ((isset($validated['show_recipients']) && $validated['show_recipients'] && isset($validated['recipients'])) || isset($creatorAsRecipient)) {
                // For personal reminders, ensure the creator is added as a recipient
                if (isset($creatorAsRecipient) && $reminder->reminder_type == 'personal') {
                    $creatorId = auth('tenant')->id();

                    $recipient = new Recipient();
                    $recipient->reminder_id = $reminder->id;
                    $recipient->user_id = $creatorId;
                    $recipient->save();

                    // Send notification to the creator
                    $user = User::find($creatorId);
                    if ($user) {
                        // $user->notify(new ReminderNotification($reminder, $reminder->getNotificationChannels()));
                    }
                }

                // Add other recipients if specified
                if (isset($validated['show_recipients']) && $validated['show_recipients'] && isset($validated['recipients'])) {
                    foreach ($validated['recipients'] as $userId) {
                        // Skip if this is the creator and already added for personal reminder
                        if (isset($creatorAsRecipient) && $userId == auth('tenant')->id()) {
                            continue;
                        }

                        $recipient = new Recipient();
                        $recipient->reminder_id = $reminder->id;
                        $recipient->user_id = $userId;
                        $recipient->save();

                        // Send notification to this user
                        $user = User::find($userId);
                        if ($user) {
                            // $user->notify(new ReminderNotification($reminder, $reminder->getNotificationChannels()));
                        }
                    }
                }
            }
            DB::commit();
            return redirect()->route('tenant.reminder.index')
                ->with('success', 'Reminder created successfully');
        } catch (\Exception $e) {
            DB::rollBack();
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
            'other' => __('Other'),
        ];

        $recurrencePatterns = [
            'daily' => __('Daily'),
            'weekly' => __('Weekly'),
            'monthly' => __('Monthly'),
            'yearly' => __('Yearly'),
        ];

        $notificationChannels = [
            'email' => __('Email'),
            'system' => __('System Notification'),
            'email,system' => __('Both Email and System'),
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
            DB::beginTransaction();
            $reminder = Reminder::findOrFail($id);

            // Check if user is authorized to update this reminder
            $userId = auth('tenant')->id();
            $isCreator = $reminder->created_by == $userId;
            $isRecipient = $reminder->recipients()->where('user_id', $userId)->exists();
            $isAdmin = auth('tenant')->user()->hasRole('super admin');

            if (!$isCreator && !$isRecipient && !$isAdmin) {
                return redirect()->route('tenant.reminder.index')
                    ->with('error', 'You do not have permission to update this reminder');
            }

            // Update reminder fields
            $reminder->title = $validated['title'];
            $reminder->description = $validated['description'] ?? null;
            $reminder->reminder_type = $validated['reminder_type'];

            // Set remind_at datetime from date and optional time
            if (isset($validated['remind_date'])) {
                $date = $validated['remind_date'];
                $time = $validated['remind_time'] ?? '00:00:00';
                $reminder->remind_at = $date . ' ' . $time;
            }

            // Set remindable relationship based on reminder type
            if ($reminder->reminder_type == 'document_expiry' && isset($validated['document_id'])) {
                // Get the document and its latest version
                $document = Document::findOrFail($validated['document_id']);
                $latestVersion = $document->lastversion; // Assuming this relationship exists

                if ($latestVersion) {
                    // Set the remindable to point to the document version
                    $reminder->remindable_type = 'Modules\Document\Entities\DocumentVersion';
                    $reminder->remindable_id = $latestVersion->id;
                } else {
                    // Fallback to document if no version exists
                    $reminder->remindable_type = Document::class;
                    $reminder->remindable_id = $document->id;
                }

                $reminder->metadata = json_encode([
                    'days_before_expiry' => $validated['days_before_expiry'] ?? 7,
                    'document_id' => $document->id,
                ]);
            } elseif ($reminder->reminder_type == 'task' && isset($validated['task_id'])) {
                $reminder->remindable_type = Task::class;
                $reminder->remindable_id = $validated['task_id'];
            } elseif ($reminder->reminder_type == 'personal') {
                // For personal reminders, the remindable is the user who created it
                $reminder->remindable_type = User::class;
                $reminder->remindable_id = auth('tenant')->id();

                // For personal reminders, add creator as a recipient automatically
                $creatorAsRecipient = true;
            } else {
                $reminder->remindable_type = null;
                $reminder->remindable_id = null;
                $reminder->metadata = null;
            }

            // Handle recurrence settings
            if (isset($validated['is_recurring']) && $validated['is_recurring']) {
                $reminder->is_recurring = true;
                $reminder->recurrence_pattern = $validated['recurrence_pattern'];
                $reminder->recurrence_interval = $validated['recurrence_interval'];
                $reminder->recurrence_end_date = $validated['recurrence_end_date'] ?? null;
            } else {
                $reminder->is_recurring = false;
                $reminder->recurrence_pattern = null;
                $reminder->recurrence_interval = null;
                $reminder->recurrence_end_date = null;
            }

            // Set notification channels
            if (isset($validated['notification_channels']) && is_array($validated['notification_channels'])) {
                $reminder->notification_channels = implode(',', $validated['notification_channels']);
            } else {
                $reminder->notification_channels = 'email'; // Default
            }

            $reminder->save();

            // Handle recipients using the dedicated recipients table
            // First delete existing recipients
            Recipient::where('reminder_id', $reminder->id)->delete();

            // Then add new ones if show_recipients is enabled
            if ((isset($validated['show_recipients']) && $validated['show_recipients'] && isset($validated['recipients'])) || isset($creatorAsRecipient)) {
                // For personal reminders, ensure the creator is added as a recipient
                if (isset($creatorAsRecipient) && $reminder->reminder_type == 'personal') {
                    $creatorId = auth('tenant')->id();

                    $recipient = new Recipient();
                    $recipient->reminder_id = $reminder->id;
                    $recipient->user_id = $creatorId;
                    $recipient->save();

                    // Send notification to the creator
                    $user = User::find($creatorId);
                    if ($user) {
                        // $user->notify(new ReminderNotification($reminder, $reminder->getNotificationChannels()));
                    }
                }

                // Add other recipients if specified
                if (isset($validated['show_recipients']) && $validated['show_recipients'] && isset($validated['recipients'])) {
                    foreach ($validated['recipients'] as $userId) {
                        // Skip if this is the creator and already added for personal reminder
                        if (isset($creatorAsRecipient) && $userId == auth('tenant')->id()) {
                            continue;
                        }

                        $recipient = new Recipient();
                        $recipient->reminder_id = $reminder->id;
                        $recipient->user_id = $userId;
                        $recipient->save();

                        // Send notification to this user
                        $user = User::find($userId);
                        if ($user) {
                            // $user->notify(new ReminderNotification($reminder, $reminder->getNotificationChannels()));
                        }
                    }
                }
            }
            DB::commit();
            return redirect()->route('tenant.reminder.index')
                ->with('success', 'Reminder updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
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
        $reminder = Reminder::with(['recipients', 'recipients.user'])->findOrFail($id);

        // Check if user is authorized to view this reminder
        $userId = auth('tenant')->id();
        $isCreator = $reminder->created_by == $userId;
        $isRecipient = $reminder->recipients()->where('user_id', $userId)->exists();
        $isAdmin = auth('tenant')->user()->hasRole('super admin');

        if (!$isCreator && !$isRecipient && !$isAdmin) {
            return redirect()->route('tenant.reminder.index')
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

        // Check if user is authorized to delete this reminder
        $userId = auth('tenant')->id();
        $isCreator = $reminder->created_by == $userId;
        $isAdmin = auth('tenant')->user()->hasRole('super admin');

        if (!$isCreator && !$isAdmin) {
            return redirect()->route('tenant.reminder.index')
                ->with('error', 'You do not have permission to delete this reminder');
        }

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
        $userId = auth('tenant')->id();

        // Get IDs of relevant reminders (once)
        $relevantReminderIds = Reminder::query()
            ->where('is_active', true)
            ->where(function ($query) use ($userId) {
                $query->where('created_by', $userId)
                    ->orWhereHas('recipients', function ($q) use ($userId) {
                        $q->where('user_id', $userId);
                    });
            })
            ->pluck('id');

        // Now filter upcoming & recent using IDs
        $upcoming = Reminder::whereIn('id', $relevantReminderIds)
            ->where('remind_at', '>=', now())
            ->orderBy('remind_at')
            ->take(5)
            ->get();

        $recent = Reminder::whereIn('id', $relevantReminderIds)
            ->where('remind_at', '<', now())
            ->orderByDesc('remind_at')
            ->take(5)
            ->get();

        // Stats
        $statistics = [
            'total' => $relevantReminderIds->count(),
            'upcoming' => Reminder::whereIn('id', $relevantReminderIds)
                ->where('remind_at', '>=', now())->count(),
            'document' => Reminder::whereIn('id', $relevantReminderIds)
                ->where('reminder_type', 'document_expiry')->count(),
        ];

        return view('reminder::my-reminders', compact('upcoming', 'recent', 'statistics'));
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
            'recipients.*' => 'exists:users,id',
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
                'recipients' => $request->recipients,
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
