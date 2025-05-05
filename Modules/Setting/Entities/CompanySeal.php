<?php

namespace Modules\Setting\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CompanySeal extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_ar',
        'name_en',
        'type',
        'file_path',
        'is_active'
    ];
    
    /**
     * Get the localized name based on current application locale
     *
     * @return string
     */
    public function getNameAttribute()
    {
        $locale = app()->getLocale();
        return $locale == 'ar' ? $this->name_ar : $this->name_en;
    }
    
    /**
     * Scope a query to only include active seals.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    
   
}
