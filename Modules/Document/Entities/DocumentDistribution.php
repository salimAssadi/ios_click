<?php

namespace Modules\Document\Entities;

use App\Traits\Localizable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\BaseModel;

class DocumentDistribution extends BaseModel
{
    use HasFactory, Localizable;

    protected $fillable = [
        'document_id', 'user_id', 'distributed_at', 'comments', 'is_read'
    ];

    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    public function distributedFor()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
   

   
}
