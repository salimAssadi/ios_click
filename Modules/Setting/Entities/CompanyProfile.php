<?php

namespace Modules\Setting\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use \App\Models\BaseModel;

class CompanyProfile extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'company_name',
        'logo',
        'email',
        'phone',
        'address',
        'city',
        'country',
        'postal_code',
        'description',
        'website',
        'tax_number',
        'registration_number',
        'tenant_id'
    ];

   
}
