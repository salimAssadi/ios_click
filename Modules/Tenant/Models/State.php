<?php

namespace Modules\Tenant\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Tenant\Models\TenantModel;

class State extends TenantModel
{
    use HasFactory;
    protected $table ='cities';
    protected $fillable = [
      'name_ar',
      'country_id',
  ];

}
