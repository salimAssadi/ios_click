<?php

namespace Modules\Document\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DocumentRequestApproved extends Notification
{
    use Queueable;

    protected $data;

    /**
     * Create a new notification instance.
     *
     * @param array $data
     * @return void
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'title' => $this->data['title'] ?? __('Document Approved'),
            'message' => $this->data['message'] ?? __('Your document request has been approved'),
            'document_title' => $this->data['document_title'] ?? '',
            'url' => $this->data['url'] ?? '',
            'icon' => $this->data['icon'] ?? 'check-circle',
            'type' => 'success'
        ];
    }
}
