<?php

namespace Modules\Document\Entities;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Document\Entities\DocumentVersion;
use Modules\Tenant\Entities\User;
use App\Models\BaseModel;
class DocumentRevision extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $table = 'document_revisions';

    protected $fillable = [
        'version_id',
        'revision_number',
        'reviewer_id',
        'reviewed_at',
        'changes_summary',
        'file_path',
    ];

    
    protected $dates = [
        'reviewed_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function version()
    {
        return $this->belongsTo(DocumentVersion::class, 'version_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }
}
