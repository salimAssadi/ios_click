<?php

namespace Modules\Document\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Document\Entities\Document;
use Modules\Document\Entities\DocumentRequest;
use Modules\Document\Entities\RequestType;
use Modules\Document\Entities\Status;
use Modules\Document\Notifications\DocumentRequestAssigned;
use Modules\Document\Notifications\DocumentRequestApproved;
use Modules\Document\Notifications\DocumentRequestRejected;
use Modules\Document\Notifications\DocumentRequestModification;
use Modules\Setting\Entities\Employee;
use Modules\Tenant\Entities\User;
use Modules\Document\Entities\DocumentHistoryLog;
use Modules\Document\Events\NotificationReceived;

class DocumentRequestController extends Controller
{
    public function index()
    {

        $requests = DocumentRequest::with(['document', 'requestType', 'requestStatus', 'creator.employee', 'assignedTo'])
            ->whereHas('requestStatus', function ($query) {
                $query->where('type', 'request');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        return view('document::requests.index', compact('requests'));
    }

    // public function index()
    // {

    //     $requests = DocumentRequest::with(['document', 'requestType', 'Status', 'creator', 'assignedTo'])
    //         ->whereHas('Status', function ($query) {
    //             $query->where(function ($q) {
    //                 $q->where('type', 'request')
    //                     ->orWhere(function ($q2) {
    //                         $q2->where('type', 'approval')
    //                             ->where('code', 'under_review');
    //                     });
    //             });
    //         })
    //         ->orderBy('created_at', 'desc')
    //         ->paginate(10);
    //     return view('document::requests.index', compact('requests'));
    // }

    public function myRequests()
    {
        $requests = DocumentRequest::with(['document', 'requestType', 'creator', 'assignedTo'])
            ->where('requested_by', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('document::requests.my', compact('requests'));
    }

    public function create($documentId)
    {
        $employees = Employee::whereHas('user')->get();
        $document = Document::findOrFail($documentId);
        $requestTypes = RequestType::get();

        return view('document::requests.create', compact('document', 'requestTypes', 'employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'document_id' => 'required|exists:documents,id',
            'assigned_to' => 'required|exists:users,id',
            'request_details' => 'required|string'
        ]);

        DB::beginTransaction();
        try {
            $documentRequest = DocumentRequest::create([
                'document_id' => $request->document_id,
                'request_type_id' => $request->request_type_id,
                'request_details' => $request->request_details,
                'requested_by' => auth('tenant')->id(),
                'assigned_to' => $request->assigned_to,
                'status_id' => Status::where('type', 'request')->where('code', 'pending')->first()->id,

            ]);
            DocumentHistoryLog::create([
                'document_id' => $documentRequest->document_id,
                'version_id' => $documentRequest->document->lastVersion->id,
                'action_type' => 'Request',
                'performed_by' => auth('tenant')->id(),
                'notes' => $request->request_details,
                'change_summary' => 'Document request created',
            ]);

            // Send notification to assigned user
            $assignedUser = User::find($request->assigned_to);
            
            // Send notification
            $assignedUser->notify(new DocumentRequestAssigned($documentRequest));
            
            // Get the latest notification
            $latestNotification = $assignedUser->notifications()->latest()->first();

            
            // Trigger the notification event
            // if ($latestNotification) {
            //     event(new NotificationReceived(
            //         $latestNotification,
            //         $assignedUser->id
            //     ));
            // }
            
            DB::commit();
            return redirect()->route('tenant.document.requests.my')
                ->with('success', __('Request submitted successfully'));
        } catch (\Exception $e) {
            throw $e;
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', __('Error creating document request: ') . $e->getMessage());
        }
    }

    public function show($id)
    {
        $documentRequest = DocumentRequest::with(['document', 'requestType', 'approvalStatus','creator', 'assignedTo'])
            ->findOrFail($id);
        $reviewPosition= getSettingsValByName('review_position_id');
        $employees = Employee::where('position_id', $reviewPosition)->with('user')->get();
        $requestStatus = Status::where('type', 'request')->get();
        return view('document::requests.show', compact('documentRequest', 'requestStatus', 'employees'));
    }

    public function updateStatus(Request $request, $id)
    {   
        $approvedStatusId = Status::where('code', 'approved')->where('type', 'request')->value('id');

        $validated = $request->validate([
            'status' => 'required|exists:statuses,id',
            'response' => 'required|string',
            'request_details' => 'required_if:status,' . $approvedStatusId,
            'assigned_to' => 'required_if:status,' . $approvedStatusId . '|array',
            'assigned_to.*' => 'exists:employees,id'
        ]);

        DB::beginTransaction();
        try {
            $documentRequest = DocumentRequest::findOrFail($id);
            
            // Get the selected status
         
            // Update the original request
            $documentRequest->update([
                'status_id' => $approvedStatusId,
                'response' => $validated['response'],
                'action_at' => now(),
                'action_by' => auth('tenant')->id(),
            ]);
            // $status = Status::where('code', 'under_review')->where('type', 'approval')->value('id');
            // If this is a review request and status is approved
            if ($documentRequest->requestType->code === 'review'  && !empty($validated['assigned_to'])) {
                $pendingStatus = Status::where('code', 'under_review')->where('type', 'approval')->first();
                
                if (!$pendingStatus) {
                    throw new \Exception(__('Status not found: pending'));
                }

                foreach ($validated['assigned_to'] as $employeeId) {
                    $employee = Employee::with('user')->find($employeeId);
                    
                    if (!$employee || !$employee->user) {
                        continue;
                    }
                    // Create review request
                    $reviewRequest = DocumentRequest::create([
                        'document_id' => $documentRequest->document_id,
                        'request_type_id' => $documentRequest->request_type_id,
                        'assigned_to' => $employee->user->id,
                        'status_id' => $pendingStatus->id,
                        'parent_request_id' => $documentRequest->id,
                        'request_details' => $validated['request_details'],
                        'requested_by' => auth('tenant')->id(),
                    ]);
                    // Send notification
                    $employee->user->notify(new DocumentRequestAssigned($reviewRequest));
                }
            }

            DB::commit();
            return redirect()->back()->with('success', __('Request status updated successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', __('Error updating request status: ') . $e->getMessage());
        }
    }
    
    /**
     * Approve a document request
     * 
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approve(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $documentRequest = DocumentRequest::findOrFail($id);
            
            // Get the approved status
            $approvedStatus = Status::where('code', 'approved')
                ->where('type', 'approval')
                ->first();
            
            if (!$approvedStatus) {
                throw new \Exception(__('Approval status not found'));
            }
            
            // Update the request
            $documentRequest->update([
                'status_id' => $approvedStatus->id,
                'response' => $request->notes,
                'action_at' => now(),
                'action_by' => auth('tenant')->id(),
            ]);
              // Log the status change
              DocumentHistoryLog::create([
                'document_id' => $documentRequest->document_id,
                'version_id' => $documentRequest->version_id,
                'action_type' => 'approved',
                'performed_by' => auth('tenant')->id(),
                'response' => $request->notes,
                'change_summary' => 'Document status changed to approved',
            ]);
            // Update the document status if needed
            // $this->updateDocumentStatus($documentRequest->document_id, 'approved');
            
            // Notify the document owner
            $this->notifyRequestUpdate($documentRequest, 'approved');
            
            DB::commit();
            return redirect()->back()->with('success', __('Document request approved successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', __('Error approving document request: ') . $e->getMessage());
        }
    }
    
    /**
     * Reject a document request
     * 
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'notes' => 'required|string',
        ]);
        
        DB::beginTransaction();
        try {
            $documentRequest = DocumentRequest::findOrFail($id);
            
            // Get the rejected status
            $rejectedStatus = Status::where('code', 'rejected')
                ->where('type', 'approval')
                ->first();
            
            if (!$rejectedStatus) {
                throw new \Exception(__('Rejection status not found'));
            }
            
            // Update the request
            $documentRequest->update([
                'status_id' => $rejectedStatus->id,
                'notes' => $request->notes,
                'action_at' => now(),
                'action_by' => auth('tenant')->id(),
            ]);
            
            // Update the document status if needed
            $this->updateDocumentStatus($documentRequest->document_id, 'rejected');
            
            // Notify the document owner
            $this->notifyRequestUpdate($documentRequest, 'rejected');
            
            DB::commit();
            return redirect()->back()->with('success', __('Document request rejected successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', __('Error rejecting document request: ') . $e->getMessage());
        }
    }
    
    /**
     * Request modifications for a document
     * 
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function requestModification(Request $request, $id)
    {
        $request->validate([
            'notes' => 'required|string',
        ]);
        
        DB::beginTransaction();
        try {
            $documentRequest = DocumentRequest::findOrFail($id);
            
            // Get the needs_modification status
            $modificationStatus = Status::where('code', 'needs_modification')
                ->where('type', 'approval')
                ->first();
            
            if (!$modificationStatus) {
                throw new \Exception(__('Modification status not found'));
            }
            
            // Update the request
            $documentRequest->update([
                'status_id' => $modificationStatus->id,
                'notes' => $request->notes,
                'action_at' => now(),
                'action_by' => auth('tenant')->id(),
            ]);
            
            // Update the document status if needed
            $this->updateDocumentStatus($documentRequest->document_id, 'needs_modification');
            
            // Notify the document owner
            $this->notifyRequestUpdate($documentRequest, 'needs_modification');
            
            DB::commit();
            return redirect()->back()->with('success', __('Modification requested successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', __('Error requesting modification: ') . $e->getMessage());
        }
    }
    
    /**
     * Update document status based on approval decision
     * 
     * @param int $documentId
     * @param string $statusCode
     * @return void
     */
    private function updateDocumentStatus($documentId, $statusCode)
    {
        $document = Document::find($documentId);
        if (!$document) {
            return;
        }
        
        // Get the corresponding document status
        $status = null;
        switch ($statusCode) {
            case 'approved':
                $status = Status::where('code', 'approved')->where('type', 'document')->first();
                break;
            case 'rejected':
                $status = Status::where('code', 'rejected')->where('type', 'document')->first();
                break;
            case 'needs_modification':
                $status = Status::where('code', 'needs_modification')->where('type', 'document')->first();
                break;
        }
        
        if ($status) {
            $document->update(['status_id' => $status->id]);
        }
    }
    
    /**
     * Send notification about request update
     * 
     * @param DocumentRequest $documentRequest
     * @param string $action
     * @return void
     */
    private function notifyRequestUpdate(DocumentRequest $documentRequest, $action)
    {
        // Find the document owner or creator
        $owner = $documentRequest->document->creator;
        
        if (!$owner) {
            return;
        }
        
        // Create notification data
        $notificationData = [
            'title' => __('Document Request Update'),
            'message' => __('Your document request has been :action', ['action' => __($action)]),
            'document_title' => $documentRequest->document->title,
            'url' => route('tenant.document.requests.show', $documentRequest->id),
            'icon' => $action === 'approved' ? 'check-circle' : ($action === 'rejected' ? 'x-circle' : 'edit')
        ];
        
        // Send notification based on the action
        switch ($action) {
            case 'approved':
                $owner->notify(new DocumentRequestApproved($notificationData));
                break;
            case 'rejected':
                $owner->notify(new DocumentRequestRejected($notificationData));
                break;
            case 'needs_modification':
                $owner->notify(new DocumentRequestModification($notificationData));
                break;
        }
    }
}
