<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Distribution extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'department', 'code', 'copies'];

    // Relationship to User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
