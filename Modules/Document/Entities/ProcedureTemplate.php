<?php

namespace Modules\Document\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\BaseModel;

class ProcedureTemplate extends BaseModel
{
    use HasFactory;
    protected $connection = 'iso_dic';
    protected $table = 'procedure_template';
    protected $fillable = ['title', 'content','procedure_id','parent_id'];
    protected $casts = [
        'content' => 'array',
    ];
}
