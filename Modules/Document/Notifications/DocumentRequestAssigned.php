<?php

namespace Modules\Document\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Modules\Document\Entities\DocumentRequest;

class DocumentRequestAssigned extends Notification
{
    use Queueable;

    protected $documentRequest;

    public function __construct(DocumentRequest $documentRequest)
    {
        $this->documentRequest = $documentRequest;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'message' => __('You have been assigned a new document request'),
            'request_id' => $this->documentRequest->id,
            'document_title' => $this->documentRequest->document->title,
            'requester_name' => $this->documentRequest->creator->name,
            'url' => route('tenant.document.requests.show', $this->documentRequest->id)
        ];
    }
}
