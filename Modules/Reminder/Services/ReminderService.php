<?php

namespace Modules\Reminder\Services;

use Modules\Reminder\Entities\Reminder;
use Illuminate\Support\Facades\Notification;
use Modules\Reminder\Notifications\ReminderNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ReminderService
{
    /**
     * Process all due reminders
     *
     * @return array Statistics about processed reminders
     */
    public function processDueReminders()
    {
        $stats = [
            'processed' => 0,
            'sent' => 0,
            'errors' => 0,
            'rescheduled' => 0
        ];
        
        $dueReminders = Reminder::getDueReminders();
        $stats['processed'] = count($dueReminders);
        
        foreach ($dueReminders as $reminder) {
            try {
                $this->sendReminder($reminder);
                $stats['sent']++;
                
                // Handle recurring reminders
                if ($reminder->is_recurring) {
                    $reminder->scheduleNext();
                    $stats['rescheduled']++;
                }
            } catch (\Exception $e) {
                Log::error("Error sending reminder ID {$reminder->id}: " . $e->getMessage());
                $stats['errors']++;
            }
        }
        
        return $stats;
    }
    
    /**
     * Send a specific reminder
     *
     * @param Reminder $reminder
     * @return bool
     */
    public function sendReminder(Reminder $reminder)
    {
        // Get recipients
        $recipients = $this->getRecipients($reminder);
        
        if (empty($recipients)) {
            Log::warning("No recipients found for reminder ID {$reminder->id}");
            return false;
        }
        
        // Determine which notification channels to use
        $channels = explode(',', $reminder->notification_channels);
        
        // Send the notification through each requested channel
        Notification::send($recipients, new ReminderNotification($reminder, $channels));
        
        // Mark reminder as sent
        $reminder->markSent();
        
        // Log the event
        Log::info("Sent reminder ID {$reminder->id}, type: {$reminder->reminder_type}, title: {$reminder->title}");
        
        return true;
    }
    
    /**
     * Get recipients for a reminder
     *
     * @param Reminder $reminder
     * @return array
     */
    protected function getRecipients(Reminder $reminder)
    {
        // If recipients are explicitly set on the reminder, use those
        if (!empty($reminder->recipients) && is_array($reminder->recipients)) {
            return $reminder->recipients;
        }
        
        // Otherwise get recipients based on reminder type and remindable object
        $recipients = [];
        
        // Always include the creator
        $recipients[] = $reminder->creator;
        
        // For document expiry reminders, add document stakeholders
        if ($reminder->reminder_type === 'document_expiry' && $reminder->remindable) {
            // Get document object
            $document = $reminder->remindable;
            
            // If this is a document version, get the actual document
            if (method_exists($document, 'document')) {
                $document = $document->document;
            }
            
            // Add document owner/approvers/reviewers if available
            if (method_exists($document, 'reviewRequests') && $document->reviewRequests()->exists()) {
                foreach ($document->reviewRequests as $request) {
                    if ($request->assignee) {
                        $recipients[] = $request->assignee;
                    }
                }
            }
        }
        
        // Remove duplicates and null values
        return collect($recipients)->unique('id')->filter()->values()->all();
    }
    
    /**
     * Create a document expiry reminder
     *
     * @param mixed $document Document or DocumentVersion
     * @param int $daysBeforeExpiry
     * @param array $options Additional options like recipients, is_recurring, etc.
     * @return Reminder
     */
    public function createDocumentExpiryReminder($document, $daysBeforeExpiry, array $options = [])
    {
        return Reminder::createDocumentExpiryReminder($document, $daysBeforeExpiry, $options['recipients'] ?? null);
    }
    
    /**
     * Create a personal reminder
     *
     * @param string $title
     * @param string $description
     * @param \Carbon\Carbon|string $remindAt
     * @param array $options Additional options
     * @return Reminder
     */
    public function createPersonalReminder($title, $description, $remindAt, array $options = [])
    {
        // Convert string dates to Carbon
        if (is_string($remindAt)) {
            $remindAt = Carbon::parse($remindAt);
        }
        
        return Reminder::createPersonalReminder($title, $description, $remindAt, $options);
    }
}
