<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Localizable;

class Category extends Model
{
    use HasFactory ,Localizable;
    public $fillable=[
        'title_ar',
        'title_en',
        'type',
        'parent_id',
    ];
     // CATEGORY TYPE
     const CATEGORY_MAIN = 1;
     const CATEGORY_PUBLIC = 2;
     const CATEGORY_PRIVATE = 3;

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
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
