<?php

namespace Modules\Tenant\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Tenant\Models\TenantModel;

class ModificationRecord extends TenantModel
{
    use HasFactory;

    protected $fillable = ['user_id', 'version_number', 'issue_date', 'modification_description', 'modified_by'];

    // Relationship to User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
