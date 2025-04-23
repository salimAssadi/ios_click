<?php

namespace Modules\Document\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Document\Entities\Document;
use Modules\Document\Entities\DocumentRequest;
use Modules\Document\Entities\RequestType;
use Modules\Document\Entities\Status;

class DocumentRequestController extends Controller
{
    public function index()
    {

        $requests = DocumentRequest::with(['document', 'requestType', 'requestStatus', 'creator', 'assignedTo'])
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
        $validated = $request->validate([
            'document_id' => 'required|exists:documents,id',
            'request_type_id' => 'required|exists:request_types,id',
            'request_details' => 'required|string',
        ]);

        $documentRequest = DocumentRequest::create([
            'document_id' => $validated['document_id'],
            'request_type_id' => $validated['request_type_id'],
            'notes' => $validated['request_details'],
            'requested_by' => Auth::id(),
            'status_id' => DocumentRequest::DEFAULT_REQUEST_STATUS,

        ]);

        return redirect()->route('tenant.document.requests.my')
            ->with('success', __('Request submitted successfully'));
    }

    public function show($id)
    {
        $documentRequest = DocumentRequest::with(['document', 'requestType', 'creator', 'assignedTo'])
            ->findOrFail($id);
        $requestStatus = Status::where('type', 'request')->get();
        return view('document::requests.show', compact('documentRequest', 'requestStatus'));
    }

    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|exists:statuses,id',
            'response' => 'required|string',
        ]);

        $documentRequest = DocumentRequest::findOrFail($id);

        $documentRequest->update([
            'status_id' => $validated['status'],
            'response' => $validated['response'],
            'action_by' => Auth::id(),
            'action_at' => now(),
        ]);

        return redirect()->back()
            ->with('success', __('Request status updated successfully'));
    }
}
