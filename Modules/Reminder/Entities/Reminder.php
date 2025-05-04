<?php

namespace Modules\Reminder\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BaseModel;
use Carbon\Carbon;
use Modules\Tenant\Entities\User;

class Reminder extends BaseModel
{
    use HasFactory, SoftDeletes;
    
    protected $table = 'reminders';
    
    protected $fillable = [
        'title', 'description', 'remind_at', 'reminder_type',
        'is_recurring', 'recurrence_pattern', 'recurrence_interval', 'recurrence_end_date',
        'status', 'last_sent_at', 'created_by', 'recipients',
        'notification_channels', 'is_active', 'remindable_type', 'remindable_id',
        'metadata'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'remind_at' => 'datetime',
        'recurrence_end_date' => 'datetime',
        'last_sent_at' => 'datetime',
        'is_recurring' => 'boolean',
        'is_active' => 'boolean',
        'recipients' => 'array',
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    /**
     * Get the parent remindable model (polymorphic relationship).
     */
    public function remindable()
    {
        return $this->morphTo();
    }

    /**
     * Get the user who created this reminder
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the users who will receive this reminder
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getRecipientsAttribute($value)
    {
        if (!$value) {
            return collect([$this->created_by]); // Default to creator
        }
        
        $recipientIds = json_decode($value, true);
        return User::whereIn('id', $recipientIds)->get();
    }

    /**
     * Check if this reminder is due to be sent
     * 
     * @return bool
     */
    public function isDue()
    {
        if (!$this->is_active || $this->status === 'cancelled') {
            return false;
        }
        
        $now = Carbon::now();
        
        // For normal reminders, check if it's time to send
        if ($now->greaterThanOrEqualTo($this->remind_at) && $this->status === 'pending') {
            return true;
        }
        
        // For recurring reminders, check if we should send again
        if ($this->is_recurring && $this->status === 'sent') {
            // If recurrence has an end date and we're past it, don't send
            if ($this->recurrence_end_date && $now->greaterThan($this->recurrence_end_date)) {
                return false;
            }
            
            // Check if enough time has passed since last send based on recurrence pattern
            if ($this->last_sent_at) {
                $nextDueDate = $this->calculateNextDueDate();
                return $now->greaterThanOrEqualTo($nextDueDate);
            }
        }
        
        return false;
    }
    
    /**
     * Calculate the next due date based on recurrence pattern
     * 
     * @return \Carbon\Carbon
     */
    public function calculateNextDueDate()
    {
        if (!$this->is_recurring || !$this->last_sent_at) {
            return $this->remind_at;
        }
        
        $lastSent = clone $this->last_sent_at;
        $interval = $this->recurrence_interval ?: 1;
        
        switch ($this->recurrence_pattern) {
            case 'daily':
                return $lastSent->addDays($interval);
            case 'weekly':
                return $lastSent->addWeeks($interval);
            case 'monthly':
                return $lastSent->addMonths($interval);
            case 'yearly':
                return $lastSent->addYears($interval);
            default:
                return $lastSent->addDays($interval);
        }
    }
    
    /**
     * Mark reminder as sent
     * 
     * @return $this
     */
    public function markSent()
    {
        $this->status = 'sent';
        $this->last_sent_at = Carbon::now();
        $this->save();
        
        return $this;
    }
    
    /**
     * Cancel this reminder
     * 
     * @return $this
     */
    public function cancel()
    {
        $this->status = 'cancelled';
        $this->is_active = false;
        $this->save();
        
        return $this;
    }
    
    /**
     * Schedule next occurrence of this reminder if it's recurring
     * 
     * @return Reminder|null
     */
    public function scheduleNext()
    {
        if (!$this->is_recurring) {
            return null;
        }
        
        // Don't schedule next if we've reached the end date
        if ($this->recurrence_end_date && Carbon::now()->greaterThan($this->recurrence_end_date)) {
            return null;
        }
        
        $nextDueDate = $this->calculateNextDueDate();
        
        // Update this reminder for the next occurrence
        $this->remind_at = $nextDueDate;
        $this->status = 'pending';
        $this->save();
        
        return $this;
    }
    
    /**
     * Get all reminders that need to be sent
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getDueReminders()
    {
        return self::where('is_active', true)
            ->where(function($query) {
                $query->where('status', 'pending')
                      ->where('remind_at', '<=', Carbon::now());
            })
            ->orWhere(function($query) {
                $query->where('is_recurring', true)
                      ->where('status', 'sent')
                      ->whereNotNull('last_sent_at');
                // Actual due date calculation happens in isDue() method
            })
            ->get()
            ->filter(function($reminder) {
                return $reminder->isDue();
            });
    }
    
    /**
     * Create a document expiry reminder
     * 
     * @param mixed $document Document or DocumentVersion
     * @param int $daysBeforeExpiry
     * @param array $recipients
     * @return Reminder
     */
    public static function createDocumentExpiryReminder($document, $daysBeforeExpiry, $recipients = null)
    {
        // Determine if this is a Document or DocumentVersion
        $modelType = get_class($document);
        $modelId = $document->id;
        
        // Get expiry date
        $expiryDate = null;
        if (method_exists($document, 'getExpiryDateAttribute')) {
            $expiryDate = $document->expiry_date;
        } elseif (property_exists($document, 'expiry_date')) {
            $expiryDate = $document->expiry_date;
        } elseif (isset($document->expiry_date)) {
            $expiryDate = $document->expiry_date;
        }
        
        if (!$expiryDate) {
            throw new \Exception("Cannot create expiry reminder: no expiry date found");
        }
        
        // Calculate remind date (X days before expiry)
        $remindAt = (clone $expiryDate)->subDays($daysBeforeExpiry);
        
        // Get document title
        $title = '';
        if (property_exists($document, 'title_en')) {
            $title = $document->title_en;
        } elseif (property_exists($document, 'document') && property_exists($document->document, 'title_en')) {
            $title = $document->document->title_en;
        } elseif (method_exists($document, 'getTitleAttribute')) {
            $title = $document->title;
        } elseif (isset($document->document) && isset($document->document->title_en)) {
            $title = $document->document->title_en;
        }
        
        // Create metadata
        $metadata = [
            'days_before_expiry' => $daysBeforeExpiry,
            'expiry_date' => $expiryDate->format('Y-m-d'),
            'document_info' => [
                'id' => $document->id,
                'title' => $title,
            ]
        ];
        
        // Create the reminder
        return self::create([
            'title' => __('Document Expiry: :title', ['title' => $title]),
            'description' => __('Reminder for document expiring on :date', ['date' => $expiryDate->format('Y-m-d')]),
            'remind_at' => $remindAt,
            'reminder_type' => 'document_expiry',
            'is_recurring' => false,
            'status' => 'pending',
            'created_by' => auth('tenant')->check() ? auth('tenant')->user()->id : 1,
            'recipients' => $recipients,
            'notification_channels' => 'email,system',
            'is_active' => true,
            'remindable_type' => $modelType,
            'remindable_id' => $modelId,
            'metadata' => $metadata
        ]);
    }
    
    /**
     * Create a personal reminder
     * 
     * @param string $title
     * @param string $description
     * @param \Carbon\Carbon $remindAt
     * @param array $options Additional options (is_recurring, recurrence_pattern, etc.)
     * @return Reminder
     */
    public static function createPersonalReminder($title, $description, $remindAt, array $options = [])
    {
        $data = [
            'title' => $title,
            'description' => $description,
            'remind_at' => $remindAt,
            'reminder_type' => 'personal',
            'is_recurring' => $options['is_recurring'] ?? false,
            'recurrence_pattern' => $options['recurrence_pattern'] ?? null,
            'recurrence_interval' => $options['recurrence_interval'] ?? null,
            'recurrence_end_date' => $options['recurrence_end_date'] ?? null,
            'status' => 'pending',
            'created_by' => auth('tenant')->check() ? auth('tenant')->user()->id : 1,
            'recipients' => $options['recipients'] ?? [auth('tenant')->check() ? auth('tenant')->user()->id : 1],
            'notification_channels' => $options['notification_channels'] ?? 'email,system',
            'is_active' => true,
            'metadata' => $options['metadata'] ?? null
        ];
        
        if (isset($options['remindable_type']) && isset($options['remindable_id'])) {
            $data['remindable_type'] = $options['remindable_type'];
            $data['remindable_id'] = $options['remindable_id'];
        }
        
        return self::create($data);
    }
}
