<?php

namespace Modules\Tenant\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Tenant\Models\TenantModel;
use Modules\Tenant\Traits\Localizable;

class IsoSystem extends TenantModel
{
    use HasFactory;

    protected $table = 'iso_systems'; 

    protected $fillable = [
        'name_ar',
        'name_en',
        'symbole',
        'code',
        'specification',
        'version',
        'image',
        'is_published'
    ];

    public function attachments()
    {
        return $this->hasMany(IsoAttachment::class);
    }
    public function getNameAttribute()
    {
        return $this->getLocalizedAttribute('name');
    }
    public function procedures()
    {
        return $this->hasMany(IsoSystemProcedure::class);
    }
    public function forms()
    {
        return $this->hasMany(IsoSystemForm::class);
    }

    public function specificationItems()
    {
        return $this->hasMany(IsoSpecificationItem::class);
    }
}
