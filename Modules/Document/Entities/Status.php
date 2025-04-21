<?php

namespace Modules\Document\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\BaseModel;
use App\Traits\Localizable;
class Status extends BaseModel
{
    use HasFactory ,Localizable;
    
    protected $table = 'statuses';
    protected $fillable = [
        'name_ar',
        'name_en',
        'type',
        'code',
        'badge'
    ];
    protected $appends = ['name'];


    public function getNameAttribute()
    {
        return $this->getLocalizedAttribute('name');
    }
}