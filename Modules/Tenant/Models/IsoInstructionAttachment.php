<?php

namespace Modules\Tenant\Models;

use Modules\Tenant\Models\TenantModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IsoInstructionAttachment extends TenantModel
{
    use HasFactory;

    protected $connection = 'iso_dic';

    protected $fillable = [
        'instruction_id',
        'file_path',
        'original_name',
        'mime_type',
        'file_size'
    ];

    public function instruction()
    {
        return $this->belongsTo(IsoInstruction::class);
    }
}
