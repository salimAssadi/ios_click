<?php

namespace Modules\Document\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Document\Entities\Document;
use Modules\Document\Entities\DocumentHistoryLog;

class DocumentHistoryController extends Controller
{
    /**
     * Display approval history for a document
     *
     * @param  int $documentId
     * @return \Illuminate\View\View
     */
    public function approvalHistory($documentId)
    {
        // Get the document with its latest version
        $document = Document::with('lastVersion')->findOrFail($documentId);
        
        // Get the history logs for this document, ordered by creation date
        $historyLogs = DocumentHistoryLog::with('performer')
            ->where('document_id', $documentId)
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('document::documents.approval_history', compact('document', 'historyLogs'));
    }
    
    /**
     * Get approval timeline data for AJAX requests (for charts or dynamic updates)
     *
     * @param  int $documentId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getApprovalTimelineData($documentId)
    {
        $historyLogs = DocumentHistoryLog::with('performer')
            ->where('document_id', $documentId)
            ->orderBy('created_at', 'asc')
            ->get();
            
        $timelineData = $historyLogs->map(function ($log) {
            return [
                'id' => $log->id,
                'action_type' => $log->action_type,
                'performed_by' => $log->performer ? $log->performer->name : 'System',
                'performed_at' => $log->created_at->format('Y-m-d H:i:s'),
                'response' => $log->response,
                'change_summary' => $log->change_summary,
                'version' => $log->version_id ? $log->version->version_number : null,
            ];
        });
        
        return response()->json($timelineData);
    }
}
