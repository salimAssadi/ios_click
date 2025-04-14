<?php

namespace App\Traits;

trait BelongsToTenant
{
    protected static function bootBelongsToTenant()
    {
        static::creating(function ($model) {
            if (request()->has('tenant')) {
                $model->setConnection('tenant');
            }
        });
    }

    public function getConnectionName()
    {
        if (request()->has('tenant')) {
            return 'tenant';
        }

        return parent::getConnectionName();
    }
}
