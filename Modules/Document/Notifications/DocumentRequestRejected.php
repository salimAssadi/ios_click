<?php

namespace Modules\Document\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DocumentRequestRejected extends Notification
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
            'title' => $this->data['title'] ?? __('Document Rejected'),
            'message' => $this->data['message'] ?? __('Your document request has been rejected'),
            'document_title' => $this->data['document_title'] ?? '',
            'url' => $this->data['url'] ?? '',
            'icon' => $this->data['icon'] ?? 'x-circle',
            'type' => 'danger'
        ];
    }
}
