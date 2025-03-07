<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcedureTemplate extends Model
{
    use HasFactory;
    protected $connection = 'iso_dic';
    protected $table = 'procedure_template';
    protected $fillable = ['title', 'content','procedure_id','parent_id'];
    protected $casts = [
        'content' => 'array',
    ];
}
