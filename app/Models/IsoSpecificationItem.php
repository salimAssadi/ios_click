<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IsoSpecificationItem extends Model
{
    use HasFactory;
    protected $table = 'iso_specification_items';

    protected $fillable = [
        'iso_system_id',
        'parent_id',
        'item_number',
        'inspection_question',
        'sub_inspection_question',
        'additional_text',
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
