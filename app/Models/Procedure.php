<?php

namespace App\Models;

use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Procedure extends Model
{
    use HasFactory, Searchable;
    protected $fillable =
    [
        'procedure_name',
        'description',
        'template_path',
        'is_optional',
        'form_id',
        'status'
    ];
    public function form()
    {
        return $this->belongsTo(Form::class);
    }
    public function document()
    {
        return $this->hasOne(Document::class,'reference_id')->where('remark', 'procedure');
    }
}
