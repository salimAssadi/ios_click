<?php

namespace Modules\Document\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Searchable;
use App\Traits\Localizable;
use Modules\Document\Entities\Procedure;
use App\Models\BaseModel;
class Sample extends BaseModel
{
    use HasFactory, Searchable, Localizable;

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
