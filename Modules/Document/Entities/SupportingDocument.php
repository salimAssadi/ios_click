<?php

namespace Modules\Document\Entities;

use App\Models\BaseModel;
use App\Traits\Localizable;
use Modules\Setting\Entities\Employee;
use Modules\Tenant\Entities\User;

class SupportingDocument extends BaseModel
{
    use Localizable;

    protected $table = 'supporting_documents';
    
    protected $fillable = [
        'title_ar', 'title_en', 'description_ar', 'description_en', 'category_id', 'issue_date', 'expiry_date', 'reminder_before',
        'created_by'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the localized title based on current app locale
     */
    public function getTitleAttribute(){
        return $this->getLocalizedAttribute('title');
    }
    
    /**
     * Get the localized description based on current app locale
     */
    public function getDescriptionAttribute(){
        return $this->getLocalizedAttribute('description');
    }
    
    /**
     * Get the category relationship
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the creator user
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    
    /**
     * Get the parent documentable model (Procedure, Policy, etc).
     * This is a polymorphic relationship.
     */
    public function documentable()
    {
        return $this->morphTo();
    }
    

}
