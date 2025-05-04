<?php

namespace Modules\Document\Http\Controllers;

use App\Http\Controllers\BaseModuleController;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Modules\Document\Entities\Category;
use Modules\Document\Entities\Document;

class SupportingDocumentController extends BaseModuleController
{
    public function __construct()
    {
        parent::__construct();
        $this->viewPath = 'document::document.supporting-documents';
        $this->routePrefix = 'document.supporting-documents';
        $this->moduleName = 'Supporting Documents';
    }

    /**
     * Display a listing of the supporting documents.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $supportingDocuments = Document::where('document_type', 'supporting')
            ->with(['category'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view($this->viewPath . '.index', compact('supportingDocuments'));
    }

    /**
     * Show the form for creating a new supporting document.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        // Get categories appropriate for supporting documents
        $categories = Category::where('type', 'supporting')
        ->whereNotNull('parent_id')
        ->get()
        ->pluck('title', 'id');
        $categories->prepend(__('Select Category'), '');
        
        return view($this->viewPath . '.create', compact('categories'));
    }

    /**
     * Store a newly created supporting document in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'title_ar' => 'required|string|max:255',
            'title_en' => 'required|string|max:255',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx|max:10240',
        ]);

        if ($validator->fails()) {
            return redirect()->route('tenant.document.supporting-documents.create')
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
        try {
            // Store the file
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('supporting_documents', $fileName, 'public');
            
            // Create the document
            $document = new Document();
            $document->title_ar = $request->input('title_ar');
            $document->title_en = $request->input('title_en');
            $document->description_ar = $request->input('description_ar');
            $document->description_en = $request->input('description_en');
            $document->category_id = $request->input('category_id');
            $document->file_path = $filePath;
            $document->original_filename = $file->getClientOriginalName();
            $document->mime_type = $file->getMimeType();
            $document->file_size = $file->getSize();
            $document->type = 'supporting';
            $document->status = 1; // Active by default
            $document->save();
            
            DB::commit();
            return redirect()->route('tenant.document.supporting-documents.index')
                ->with('success', __('Supporting document created successfully!'));
                
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error creating supporting document: ' . $e->getMessage());
            return redirect()->route('tenant.document.supporting-documents.create')
                ->with('error', __('An error occurred while creating the supporting document.'))
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $document = Document::findOrFail($id);
        return view($this->viewPath . '.show', compact('document'));
    }

    /**
     * Download the specified document.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function download($id)
    {
        $document = Document::findOrFail($id);
        return Storage::disk('public')->download($document->file_path, $document->original_filename);
    }
}
