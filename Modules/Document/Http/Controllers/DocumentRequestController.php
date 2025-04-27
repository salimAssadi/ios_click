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
use Modules\Setting\Entities\Employee;

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
        $document = Document::findOrFail($documentId);
        $requestTypes = RequestType::get();

        return view('document::requests.create', compact('document', 'requestTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'document_id' => 'required|exists:documents,id',
            'assigned_to' => 'required|exists:users,id',
            'request_details' => 'required|string',
        ]);

        DB::beginTransaction();
        try {
            $documentRequest = DocumentRequest::create([
                'document_id' => $request->document_id,
                'request_type_id' => $request->request_type_id,
                'notes' => $request->request_details,
                'requested_by' => auth('tenant')->id(),
                'assigned_to' => $request->assigned_to,
                'status_id' => Status::where('type', 'request')->where('code', 'pending')->first()->id,

            ]);
            // Send notification to assigned user
            $assignedUser = User::find($request->assigned_to);
            $assignedUser->notify(new DocumentRequestAssigned($documentRequest));

            DB::commit();
            return redirect()->route('tenant.document.requests.my')
                ->with('success', __('Request submitted successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', __('Error creating document request: ') . $e->getMessage());
        }
    }

    public function show($id)
    {
        $documentRequest = DocumentRequest::with(['document', 'requestType', 'creator', 'assignedTo'])
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
            throw $e;
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', __('Error updating request status: ') . $e->getMessage());
        }
    }
    
}
