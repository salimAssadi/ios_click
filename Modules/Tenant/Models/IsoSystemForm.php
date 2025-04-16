<?php

namespace Modules\Tenant\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IsoSystemForm extends TenantModel
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'iso_system_forms';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'category_id',
        'iso_system_id',
        'procedure_id',
        'iso_system_procedure_id',
        'form_id',
        'form_coding',
        'description',
        'created_by',
        'parent_id',
    ];

    /**
     * Get the ISO system associated with this procedure.
     */
    public function isoSystem()
    {
        return $this->belongsTo(IsoSystem::class, 'iso_system_id');
    }

    public function form()
    {
        return $this->belongsTo(Sample::class, 'form_id');
    }
    public function procedure()
    {
        return $this->belongsTo(Procedure::class, 'procedure_id');
    }
    /**
     * Get the category associated with this procedure.
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

   
    /**
     * Get the user who created this procedure.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }


    
}