<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductScope extends Model
{
    protected $table = 'product_scope'; // Table name
    protected $fillable = [
        'name',
        'parent_id',
    ];
    public function children()
    {
        return $this->hasMany(ProductScope::class, 'parent_id', 'id');
    }

    public function parent()
    {
        return $this->belongsTo(ProductScope::class, 'parent_id', 'id');
    }
}