<?php

namespace Modules\Tenant\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Tenant\Models\TenantModel;

class LoggedHistory extends TenantModel
{
    use HasFactory;
    public $fillable=[
        'user_id',
        'ip',
        'date',
        'details',
        'type',
        'parent_id',
    ];

    public function user(){
        return $this->hasOne('App\Models\User','id','user_id');
    }
}
