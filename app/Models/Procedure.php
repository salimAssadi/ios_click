<?php

namespace App\Models;

use App\Traits\Localizable;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Procedure extends Model
{
    use HasFactory, Searchable, Localizable;
    protected $connection = 'iso_dic';
    
    protected $fillable = [
        'uuid',
        'procedure_name_ar',
        'procedure_name_en',
        'category_id',
        'description_ar',
        'description_en',
        'template_path',
        'is_optional',
        'form_id',
        'content',
        'enable_upload_file',
        'enable_editor',
        'has_menual_config',
        'blade_view',
        'status',
    ];

    protected $casts = [
        'content' => 'array',
    ];

    public function form()
    {
        return $this->belongsTo(Form::class);
    }

    public function getProcedureNameAttribute()
    {
        return $this->getLocalizedAttribute('procedure_name');
    }
    public function getDescriptionAttribute()
    {
        return $this->getLocalizedAttribute('description');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function isoSystems()
    {
        return $this->belongsToMany(IsoSystem::class, 'iso_system_procedures', 'procedure_id', 'iso_system_id')->withPivot(['procedure_coding','iso_system_id']);
    }

    public function document()
    {
        return $this->hasOne(Document::class, 'reference_id')->where('remark', 'procedure');
    }

    public function attachments()
    {
        return $this->hasMany(ProcedureAttachment::class);
    }

    // public function getCodeAttribute(): string
    // {
    //     $companySymbol = getCompanySymbol();
    //     $procedureCode = $this->procedure->code;
    //     $sampleId = str_pad($this->id, 2, '0', STR_PAD_LEFT);

    //     return "{$procedureCode}-{$sampleId}";
    // }
}
