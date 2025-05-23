<?php

namespace Modules\Tenant\Models;

use Modules\Tenant\Models\TenantModel;

class DocumentHistory extends TenantModel
{
    public $fillable=[
        'document',
        'action',
        'action_user',
        'description',
        'parent_id',
    ];

    public function actionUser(){
        return $this->hasOne('App\Models\User','id','action_user');
    }

    public function documents(){
        return $this->hasOne('App\Models\Document','id','document');
    }

    public static function history($request){
        $history=new DocumentHistory();
        $history->document=$request['document_id'];
        $history->action=$request['action'];
        $history->action_user=\Auth::user()->id;
        $history->description=$request['description'];
        $history->parent_id=parentId();
        $history->save();
        return $history;
    }
}
