<?php

namespace Modules\Document\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Document\Entities\Document;
use Modules\Document\Entities\DocumentHistoryLog;
use Modules\Document\Entities\DocumentRequest;
use Modules\Document\Entities\RequestType;
use Modules\Document\Entities\Status;
use Yajra\DataTables\Facades\DataTables;

class WorkflowController extends Controller
{
    // public function index()
    // {
    //     $activeTab = request('tab', 'draft');

    //     // Get counts for each status

    //     $statuses = Status::where('type', 'approval')->get();
    //     foreach ($statuses as $status) {
    //         $counts[$status->code] = $this->getDocumentCount($status->code);
    //     }
    //     return view('document::workflow.index', compact('activeTab', 'counts', 'statuses'));
    // }

    public function index()
    {
        $activeTab = request('tab', 'under_review');

        // جميع الحالات المرتبطة بدورة الموافقة
        $statuses = Status::where('type', 'approval')->orderBy('step')->get();
        $requestTypes = RequestType::get();

        // عدّاد الحالات من جدول الطلبات
        $counts = [];
        foreach ($statuses as $status) {
            $counts[$status->code] = DocumentRequest::whereHas('approvalStatus', function ($q) use ($status) {
                $q->where('code', $status->code);
            })->count();
        }

        return view('document::workflow.index', compact('activeTab', 'counts', 'statuses', 'requestTypes'));
    }

    private function getDocumentCount($status)
    {
        return Document::whereHas('lastVersion', function ($query) use ($status) {
            $query->whereHas('status', function ($q) use ($status) {
                $q->where('code', $status);
            });
        })
        ->groupBy('document_id')
        ->count();
    }

    private function getRequestTypeId($type)
    {
        return RequestType::where('code', $type)->first()->id;
    }
    public function data()
    {
        $activeTab = request('tab', 'under_review');
        $statusCodes = Status::where('type', 'approval')->pluck('code')->toArray();
        
        $requests = DocumentRequest::with([
            'document',
            'document.lastVersion',
            'approvalStatus',
            'document.creator',
            'creator',
            'assignedTo',
        ])
            ->whereHas('approvalStatus', function ($q) use ($activeTab, $statusCodes) {
                if (in_array($activeTab, $statusCodes)) {
                    $q->where('code', $activeTab);
                } else {
                    $q->where('code', 'under_review');
                }
            })
            ->groupBy('document_id') ;

        return DataTables::of($requests)
            ->addColumn('title', function ($request) {
                return '<a href="' . route('tenant.document.show', $request->document_id) . '">' . $request->document->title . '</a>';
            })
            ->addColumn('version', function ($request) {
                return 'v' . optional($request->document->lastVersion)->version;
            })
            ->addColumn('status', function ($request) {
                return $request->approval_status_badge;
            })
            ->addColumn('created_by', function ($request) {
                return optional($request->creator)->name;
            })
            ->addColumn('assigned_to', function ($request) {
                return optional($request->assignedTo)->name ?? '-';
            })
            ->addColumn('actions', function ($request) {
                $actions = '<div class="btn-group">';
                $actions .= '<button type="button" class="btn btn-primary dropdown-toggle btn-sm" data-bs-toggle="dropdown">' . __('Actions') . '</button>';
                $actions .= '<ul class="dropdown-menu">';
                $actions .= '<li><a class="dropdown-item" href="' . route('tenant.document.show', $request->document_id) . '"><i class="ti ti-eye"></i> ' . __('View Document') . '</a></li>';
                if ($request->approvalStatus->code == 'draft') {
                    $actions .= '<li><a href="#" class="dropdown-item status-update" data-bs-toggle="modal" data-bs-target="#statusModal"
                        data-document-id="' . $request->document_id . '" data-status="under_review" data-request-type-code="approve"><i class="ti ti-send"></i> ' . __('Send for Review') . '</a></li>';
                }
                if ($request->approvalStatus->code == 'under_review') {
                    $actions .= '<li><a href="#" class="dropdown-item status-update" data-bs-toggle="modal" data-bs-target="#statusModal"
                        data-document-id="' . $request->document_id . '" data-status="pending_approval" data-request-type-code="approve"><i class="ti ti-check"></i> ' . __('Send for Approval') . '</a></li>';
                }
                if ($request->approvalStatus->code == 'pending_approval') {
                    $actions .= '<li><a href="#" class="dropdown-item status-update" data-bs-toggle="modal" data-bs-target="#statusModal"
                        data-document-id="' . $request->document_id . '" data-status="approved"><i class="ti ti-check-double"></i> ' . __('Approve') . '</a></li>';
                }
                $actions .= '</ul>

                </div>';
                return $actions;
            })
            ->rawColumns(['title', 'status', 'actions'])
            ->make(true);
    }

    public function updateStatus($id)
    {
        $document = Document::findOrFail($id);
        $status = request('status');
        $notes = request('notes');
        $request_type_code = request('request_type_code');

        $statusRecord = Status::where('code', $status)
            ->where('type', 'approval')
            ->firstOrFail();
        try {
            DB::beginTransaction();
            // Create a new version with the updated status
           

            // Create a document request for tracking
                DocumentRequest::create([
                'document_id' => $document->id,
                'version_id' => $newVersion->id,
                'request_type_id' => $this->getRequestTypeId($request_type_code),
                'requested_by' => Auth::id(),
                'status_id' => $statusRecord->id,
                'notes' => $notes,
                'action_at' => now(),
                'action_by' => Auth::id(),
            ]);

            // Log the status change
            DocumentHistoryLog::create([
                'document_id' => $document->id,
                'version_id' => $newVersion->id,
                'action_type' => $statusRecord->name,
                'performed_by' => Auth::id(),
                'notes' => $notes,
                'change_summary' => 'Document status changed to ' . $statusRecord->name,
            ]);

            DB::commit();

            return response()->json(['success' => true, 'message' => __('Document status updated successfully')]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => true, 'message' => __('An error occurred while updating the status' . $e->getMessage())]);
        }
    }
}
