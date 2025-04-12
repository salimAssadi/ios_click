<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IsoPolicyVersion extends Model
{
    use HasFactory;

    protected $connection = 'iso_dic';

    protected $fillable = [
        'policy_id',
        'version',
        'content',
        'name_ar',
        'name_en',
        'description_ar',
        'description_en',
        'created_by',
        'changes'
    ];

    protected $casts = [
        'changes' => 'array'
    ];

    public function policy()
    {
        return $this->belongsTo(IsoPolicy::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
