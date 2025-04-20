<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

abstract class BaseModel extends Model
{
    use HasFactory, BelongsToTenant;

    protected static function boot()
    {
        parent::boot();

        // static::creating(function ($model) {
        //     if (auth('tenant')->check()) {
        //         $model->created_by = auth('tenant')->user()->id;
        //     }
        // });

        // static::updating(function ($model) {
        //     if (auth('tenant')->check()) {
        //         $model->updated_by = auth('tenant')->user()->id;
        //     }
        // });
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}





