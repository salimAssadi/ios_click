<?php

namespace Modules\Document\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\BaseModel;
use App\Traits\Localizable;

class RequestType extends BaseModel
{
    use HasFactory ,Localizable;

    protected $fillable = [
        'code',
        'name_ar',
        'name_en',
        'description_ar',
        'description_en',
    ];
    
    public function getNameAttribute()
    {
        return $this->getLocalizedAttribute('name');
    }

   
    public function requests()
    {
        return $this->hasMany(DocumentRequest::class);
    }
}
