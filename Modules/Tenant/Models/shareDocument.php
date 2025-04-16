<?php

namespace Modules\Tenant\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Tenant\Models\TenantModel;

class shareDocument extends TenantModel
{
    use HasFactory;
    public $fillable=[
        'user_id',
        'document_id',
        'start_date',
        'end_date',
        'parent_id',
    ];

    public function user(){
        return $this->hasOne('App\Models\User','id','user_id');
    }
}
