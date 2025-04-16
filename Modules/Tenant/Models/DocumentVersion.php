<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentVersion extends Model
{
    protected $connection = 'iso_dic';
    protected $fillable = [
        'document_id',
        'version_number',
        'changes_description',
        'file_path',
        'google_doc_id',
        'created_by',
        'status'
    ];

    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    public function approvals()
    {
        return $this->hasMany(DocumentApproval::class);
    }

    public function googleDocMapping()
    {
        return $this->hasOne(GoogleDocMapping::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
