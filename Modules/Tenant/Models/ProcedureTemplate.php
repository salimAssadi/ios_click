<?php

namespace Modules\Tenant\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Tenant\Models\TenantModel;

class ProcedureTemplate extends TenantModel
{
    use HasFactory;
    protected $connection = 'iso_dic';
    protected $table = 'procedure_template';
    protected $fillable = ['title', 'content','procedure_id','parent_id'];
    protected $casts = [
        'content' => 'array',
    ];
}
