<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IsoPolicyAttachment extends Model
{
    use HasFactory;

    protected $connection = 'iso_dic';

    protected $fillable = [
        'policy_id',
        'file_path',
        'original_name',
        'mime_type',
        'file_size'
    ];

    public function policy()
    {
        return $this->belongsTo(IsoPolicy::class);
    }
}
