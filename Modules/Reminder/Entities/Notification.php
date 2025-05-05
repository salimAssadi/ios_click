<?php

namespace Modules\Reminder\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\BaseModel;

class Notification extends Model
{
    use HasFactory;
    
    protected $table = 'reminder_notifications';
    
    protected $fillable = [
        'reminder_id',
        'channel',
        'status'
    ];
    
    /**
     * Get the reminder that this notification belongs to.
     */
    public function reminder()
    {
        return $this->belongsTo(Reminder::class, 'reminder_id');
    }
}
