<?php

namespace Modules\Document\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DocumentHistoryLog extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'document_id',
        'version_id',
        'action_type',
        'performed_by',
        'notes',
        'change_summary'
    ];
    
    protected static function newFactory()
    {
        return \Modules\Document\Database\factories\DocumentHistoryLogFactory::new();
    }

    public function performer()
    {
        return $this->belongsTo(\App\Models\User::class, 'performed_by');
    }

    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    public function version()
    {
        return $this->belongsTo(DocumentVersion::class, 'version_id');
    }
}
