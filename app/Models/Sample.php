<?php

namespace App\Models;

use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sample extends Model
{
    use HasFactory, Searchable;
    protected $fillable =
    [
        'sample_name',
        'description',
        'template_path',
        'is_optional',
        'procedure_id',
        'content',
        'form_id',
        'status'
    ];
    public function form()
    {
        return $this->belongsTo(Form::class);
    }
    
    public function procedure()
    {
        return $this->belongsTo(Procedure::class,'procedure_id');
    }
    
}
