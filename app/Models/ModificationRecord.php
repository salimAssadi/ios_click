<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModificationRecord extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'version_number', 'issue_date', 'modification_description', 'modified_by'];

    // Relationship to User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
