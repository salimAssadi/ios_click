<?php

namespace Modules\Document\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DocumentRequestModification extends Notification
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
            'title' => $this->data['title'] ?? __('Document Modification Requested'),
            'message' => $this->data['message'] ?? __('Your document needs modifications'),
            'document_title' => $this->data['document_title'] ?? '',
            'url' => $this->data['url'] ?? '',
            'icon' => $this->data['icon'] ?? 'edit',
            'type' => 'warning'
        ];
    }
}
