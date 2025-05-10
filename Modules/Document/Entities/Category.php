<?php

namespace Modules\Document\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\BaseModel;
use Modules\Tenant\Traits\Localizable;

class Category extends BaseModel
{
    use HasFactory,Localizable;
    public $fillable=[
        'title_ar',
        'title_en',
        'type',
        'parent_id',
    ];

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function scopeSupportingDocuments($query)
    {
        return $query->where('type', 'supporting');
    }
    
    public function documents()
    {
        return $this->hasMany(Document::class, 'category_id');
    }
    
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }
    
    public function getTitleAttribute()
    {
        return $this->getLocalizedAttribute('title');
    }

}
