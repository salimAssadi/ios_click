<?php

namespace Modules\Document\Models;

use App\Traits\Localizable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\BaseModel;

class Procedure extends BaseModel
{
    use HasFactory, Localizable;
    
    protected $fillable = [
        'procedure_name_ar',
        'procedure_name_en',
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

    public function form()
    {
        return $this->belongsTo(Form::class);
    }

    public function getProcedureNameAttribute()
    {
        return $this->getLocalizedAttribute('procedure_name');
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
