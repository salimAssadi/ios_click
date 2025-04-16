<?php

namespace Modules\Setting\Models;

use App\Models\BaseModel;

class Setting extends BaseModel
{
    protected $fillable = [
        'key',
        'value',
        'group',
        'type',
        'description'
    ];

    protected $casts = [
        'value' => 'json'
    ];

    public static function get($key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    public static function set($key, $value, $group = 'general')
    {
        return static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'group' => $group
            ]
        );
    }
}
