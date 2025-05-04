<?php

namespace Modules\Reminder\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Modules\Reminder\Entities\Reminder;

class ReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The reminder instance.
     *
     * @var \Modules\Reminder\Entities\Reminder
     */
    protected $reminder;
    
    /**
     * The notification channels to use.
     *
     * @var array
     */
    protected $channels;

    /**
     * Create a new notification instance.
     *
     * @param \Modules\Reminder\Entities\Reminder $reminder
     * @param array $channels
     * @return void
     */
    public function __construct(Reminder $reminder, array $channels = ['email', 'database'])
    {
        $this->reminder = $reminder;
        $this->channels = $channels;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return $this->channels;
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $mailMessage = (new MailMessage)
            ->subject($this->reminder->title)
            ->greeting(__('Hello :name', ['name' => $notifiable->name]))
            ->line($this->reminder->description);
            
        // Add additional information based on reminder type
        if ($this->reminder->reminder_type === 'document_expiry') {
            $this->addDocumentExpiryDetails($mailMessage);
        }
        
        // Add action button if relevant
        if ($this->hasActionUrl()) {
            $mailMessage->action($this->getActionText(), $this->getActionUrl());
        }
        
        // Add standard closing line
        $mailMessage->line(__('Thank you for using our application!'));
        
        return $mailMessage;
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        $data = [
            'id' => $this->reminder->id,
            'title' => $this->reminder->title,
            'description' => $this->reminder->description,
            'remind_at' => $this->reminder->remind_at->format('Y-m-d H:i:s'),
            'reminder_type' => $this->reminder->reminder_type,
            'is_recurring' => $this->reminder->is_recurring,
            'created_by' => $this->reminder->created_by,
            'metadata' => $this->reminder->metadata,
        ];
        
        // Add action URL if available
        if ($this->hasActionUrl()) {
            $data['action_url'] = $this->getActionUrl();
            $data['action_text'] = $this->getActionText();
        }
        
        return $data;
    }
    
    /**
     * Add document expiry specific details to the mail message
     *
     * @param \Illuminate\Notifications\Messages\MailMessage $mailMessage
     * @return void
     */
    protected function addDocumentExpiryDetails($mailMessage)
    {
        $metadata = $this->reminder->metadata;
        
        if (!empty($metadata) && isset($metadata['expiry_date'])) {
            $mailMessage->line(__('Document Expiry Date: :date', ['date' => $metadata['expiry_date']]));
        }
        
        if (!empty($metadata) && isset($metadata['days_before_expiry'])) {
            $mailMessage->line(__('This is a reminder :days days before document expiry.', ['days' => $metadata['days_before_expiry']]));
        }
        
        // Add document info if available
        if (!empty($metadata) && isset($metadata['document_info'])) {
            $docInfo = $metadata['document_info'];
            if (isset($docInfo['title'])) {
                $mailMessage->line(__('Document: :title', ['title' => $docInfo['title']]));
            }
        }
    }
    
    /**
     * Check if this reminder has an action URL
     *
     * @return bool
     */
    protected function hasActionUrl()
    {
        // For document expiry reminders, provide a link to the document
        if ($this->reminder->reminder_type === 'document_expiry' && $this->reminder->remindable) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Get the action URL for this reminder
     *
     * @return string|null
     */
    protected function getActionUrl()
    {
        if ($this->reminder->reminder_type === 'document_expiry' && $this->reminder->remindable) {
            $remindable = $this->reminder->remindable;
            
            // If this is a document version, get the document
            if (method_exists($remindable, 'document')) {
                $document = $remindable->document;
                return route('tenant.document.supporting-documents.show', $document->id);
            }
            
            // If this is a document
            if (get_class($remindable) === 'Modules\Document\Entities\Document') {
                return route('tenant.document.show', $remindable->id);
            }
        }
        
        return null;
    }
    
    /**
     * Get the action text for this reminder
     *
     * @return string
     */
    protected function getActionText()
    {
        if ($this->reminder->reminder_type === 'document_expiry') {
            return __('View Document');
        }
        
        return __('View Details');
    }
}
