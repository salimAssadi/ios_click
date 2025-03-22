<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IsoSystemProcedure extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'iso_system_procedures';

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
        'procedure_coding',
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

    public function isoSystemProcedureForm()
    {
        return $this->hasMany(IsoSystemForm::class, 'iso_system_procedure_id');
    }

   
   
    /**
     * Get the user who created this procedure.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }


    
}