<?php

namespace Modules\Report\Models;

use App\Models\BaseModel;

class Report extends BaseModel
{
    protected $fillable = [
        'title',
        'type',
        'parameters',
        'description',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'parameters' => 'json'
    ];

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function getDataAttribute()
    {
        switch ($this->type) {
            case 'document':
                return $this->getDocumentStats();
            case 'audit':
                return $this->getAuditStats();
            case 'training':
                return $this->getTrainingStats();
            case 'risk':
                return $this->getRiskStats();
            default:
                return [];
        }
    }

    protected function getDocumentStats()
    {
        // Implement document statistics logic
        return [
            'total' => \Modules\DocumentControl\Models\Document::count(),
            'pending_approval' => \Modules\DocumentControl\Models\Document::where('status', 'pending_approval')->count(),
            'approved' => \Modules\DocumentControl\Models\Document::where('status', 'approved')->count(),
            'archived' => \Modules\DocumentControl\Models\Document::where('archived', true)->count()
        ];
    }

    protected function getAuditStats()
    {
        // Implement audit statistics logic
        return [
            'total' => \Modules\AuditManagement\Models\Audit::count(),
            'completed' => \Modules\AuditManagement\Models\Audit::where('status', 'completed')->count(),
            'pending' => \Modules\AuditManagement\Models\Audit::where('status', 'pending')->count()
        ];
    }

    protected function getTrainingStats()
    {
        // Implement training statistics logic
        return [
            'total' => \Modules\TrainingManagement\Models\Training::count(),
            'completed' => \Modules\TrainingManagement\Models\Training::where('status', 'completed')->count(),
            'in_progress' => \Modules\TrainingManagement\Models\Training::where('status', 'in_progress')->count()
        ];
    }

    protected function getRiskStats()
    {
        // Implement risk statistics logic
        return [
            'total' => \Modules\RiskManagement\Models\Risk::count(),
            'high' => \Modules\RiskManagement\Models\Risk::where('severity', 'high')->count(),
            'medium' => \Modules\RiskManagement\Models\Risk::where('severity', 'medium')->count(),
            'low' => \Modules\RiskManagement\Models\Risk::where('severity', 'low')->count()
        ];
    }
}
