<?php

namespace Modules\Document\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\BaseModel;

class Category extends BaseModel
{
    use HasFactory;
    public $fillable=[
        'title',
        'parent_id',
    ];

}
