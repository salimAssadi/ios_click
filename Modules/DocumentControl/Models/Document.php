<?php

namespace Modules\DocumentControl\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Document extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'title',
        'type',
        'version',
        'status',
        'content',
        'category_id',
        'created_by',
        'approved_by',
        'approval_date',
        'review_date',
        'archived'
    ];

    protected $casts = [
        'approval_date' => 'datetime',
        'review_date' => 'datetime',
        'archived' => 'boolean'
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(DocumentCategory::class);
    }

    public function versions(): HasMany
    {
        return $this->hasMany(DocumentVersion::class);
    }

    public function approvals(): HasMany
    {
        return $this->hasMany(DocumentApproval::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
