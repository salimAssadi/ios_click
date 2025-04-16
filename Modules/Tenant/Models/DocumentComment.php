<?php

namespace Modules\Tenant\Models;

use Modules\Tenant\Models\TenantModel;

class DocumentComment extends TenantModel
{
    public $fillable=[
        'comment',
        'user_id',
        'document_id',
        'parent_id',
    ];

    public function user(){
        return $this->hasOne('App\Models\User','id','user_id');
    }
}
