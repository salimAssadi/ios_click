<?php

namespace Modules\Tenant\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Tenant\Models\TenantModel;
use Modules\Tenant\Traits\Searchable;
use Modules\Tenant\Traits\Localizable;

class Sample extends TenantModel
{
    use HasFactory, Localizable;

    protected $connection = 'iso_dic';
    protected $table = 'samples';

    protected $fillable = [
        'sample_name_ar',
        'sample_name_en',
        'description_ar',
        'description_en',
        'is_optional',
        'procedure_id',
        'template_path',
        'status',
        'has_menual_config',
        'enable_upload_file',
        'enable_editor',
        'blade_view',
        'content'
    ];

    public function getSampleNameAttribute()
    {
        return $this->getLocalizedAttribute('sample_name');
    }
    public function procedure()
    {
        return $this->belongsTo(Procedure::class);
    }

    public function sampleAttachments()
    {
        return $this->hasMany(SampleAttachment::class);
    }

    public function getDescriptionAttribute()
    {
        return $this->getLocalizedAttribute('description');
    }
    protected static function booted()
    {
        static::deleting(function ($sample) {
            foreach($sample->attachments as $attachment) {
                $attachment->delete();
            }
        });
    }
}
