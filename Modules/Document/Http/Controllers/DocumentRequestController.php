<?php

namespace Modules\Document\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Document\Entities\Document;
use Modules\Document\Entities\DocumentRequest;
use Modules\Document\Entities\RequestType;
use Illuminate\Support\Facades\Auth;

class DocumentRequestController extends Controller
{
    public function index()
    {   
       
        $requests = DocumentRequest::with(['document', 'requestType', 'creator', 'assignedTo'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('document::requests.index', compact('requests'));
    }

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
            'status_id' => DocumentRequest::STATUS_UNDER_REVIEW

        ]);

        return redirect()->route('tenant.document.requests.my')
            ->with('success', __('Request submitted successfully'));
    }

    public function show($id)
    {
        $documentRequest = DocumentRequest::with(['document', 'requestType', 'creator', 'assignedTo'])
            ->findOrFail($id);
            
        return view('document::requests.show', compact('documentRequest'));
    }

    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:in_progress,approved,rejected,completed',
            'response' => 'required|string',
        ]);

        $documentRequest = DocumentRequest::findOrFail($id);
        
        $documentRequest->update([
            'status' => $validated['status'],
            'response' => $validated['response'],
            'completed_at' => in_array($validated['status'], ['approved', 'rejected', 'completed']) ? now() : null,
        ]);

        return redirect()->back()
            ->with('success', __('Request status updated successfully'));
    }
}
