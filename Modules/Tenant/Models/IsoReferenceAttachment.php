<?php

namespace Modules\Tenant\Models;

use Modules\Tenant\Models\TenantModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IsoReferenceAttachment extends TenantModel
{
    use HasFactory;

    protected $connection = 'iso_dic';

    protected $fillable = [
        'reference_id',
        'file_path',
        'original_name',
        'mime_type',
        'file_size'
    ];

    public function reference()
    {
        return $this->belongsTo(IsoReference::class, 'reference_id');
    }
}
