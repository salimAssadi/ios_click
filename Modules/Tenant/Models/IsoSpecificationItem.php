<?php

namespace Modules\Tenant\Models;

use Modules\Tenant\Models\TenantModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IsoSpecificationItem extends TenantModel
{
    use HasFactory;
    protected $table = 'iso_specification_items';

    protected $fillable = [
        'iso_system_id',
        'parent_id',
        'item_number',
        'inspection_question_ar',
        'inspection_question_en',
        'sub_inspection_question_ar',
        'sub_inspection_question_en',
        'additional_text_ar',
        'additional_text_ar',
        'attachment',
        'is_published'
    ];

    public function isoSystem()
    {
        return $this->belongsTo(IsoSystem::class);
    }

    public function parent()
    {
        return $this->belongsTo(IsoSpecificationItem::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(IsoSpecificationItem::class, 'parent_id');
    }
    public function hasChild()
    {
        return $this->children()->exists();
    }

    public function getIsParentAttribute()
    {
        return $this->children()->exists();
    }
}
