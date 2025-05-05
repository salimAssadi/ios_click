<?php

namespace Modules\Reminder\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\BaseModel;
use Modules\Tenant\Entities\User;

class Recipient extends Model
{
    use HasFactory;
    
    protected $table = 'reminder_recipients';
    
    protected $fillable = [
        'reminder_id', 
        'user_id'
    ];
    
    /**
     * Get the reminder that this recipient belongs to.
     */
    public function reminder()
    {
        return $this->belongsTo(Reminder::class, 'reminder_id');
    }
    
    /**
     * Get the user that this recipient represents.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
