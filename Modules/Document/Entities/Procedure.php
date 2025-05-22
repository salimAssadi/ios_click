<?php

namespace Modules\Document\Entities;

use App\Traits\Localizable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\BaseModel;

class Procedure extends BaseModel
{
    use HasFactory, Localizable;
    
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

    /**
     * Get all documents related to this procedure via polymorphic relationship
     */
    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }
    
    /**
     * Get the main document for this procedure (for backward compatibility)
     */
    public function document()
    {
        return $this->morphOne(Document::class, 'documentable')->latest();
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
