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
use Modules\Document\Entities\DocumentVersion;
use Modules\Reminder\Services\ReminderService;

class SupportingDocumentController extends BaseModuleController
{
    /**
     * @var ReminderService
     */
    protected $reminderService;
    
    public function __construct(ReminderService $reminderService)
    {
        parent::__construct();
        $this->viewPath = 'document::document.supporting-documents';
        $this->routePrefix = 'document.supporting-documents';
        $this->moduleName = 'Supporting Documents';
        $this->reminderService = $reminderService;
    }

    /**
     * Display a listing of the supporting documents.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $categories = Category::where('type','supporting')
            ->whereNotNull('parent_id')
            ->withCount('documents')
            ->get();
            
        // Check if it's an AJAX request for refresh without page reload
        if (request()->ajax()) {
            return view($this->viewPath . '.partials.categories-list', compact('categories'))->render();
        }
        
        return view($this->viewPath . '.main', compact('categories'));
    }
    
    public function categoryDetail($id)
    {
        // $supportingDocuments = Document::where('document_type', 'supporting')
        //     ->where('category_id', $id)
        //     ->with(['category'])
        //     ->orderBy('created_at', 'desc')
        //     ->paginate(10);
       $category = Category::where('id',$id)->first();
       if($category->type != 'supporting') {
           return redirect()->back()->with('error', __('Category type is not supporting'));
       }
        return view($this->viewPath . '.index', compact('category'));
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
            'issue_date' => 'required|date',
            'expiry_date' => 'required|date|after:issue_date',
            'reminder_days' => 'required|integer|min:1|max:365',
            'file' => 'required|file|mimes:pdf|max:10240',
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

            $filePath = $file->storeAs(getTenantRoot() . '/supporting_documents', $fileName, 'tenants');
            
            // Generate document number
            $documentNumber = 'SD-' . date('YmdHis');
            
            // Create the document
            $document = new Document();
            $document->title_ar = $request->input('title_ar');
            $document->title_en = $request->input('title_en');
            $document->description_ar = $request->input('description_ar');
            $document->description_en = $request->input('description_en');
            $document->category_id = $request->input('category_id');
            $document->document_number = $documentNumber;
            $document->document_type = 'supporting';
            $document->status_id = 11; // Active by default
            $document->creation_date = now();
            $document->created_by = auth('tenant')->user()->id;
            $document->save();
            
            // Create the initial document version
            $version = new DocumentVersion();
            $version->document_id = $document->id;
            $version->version = '1.0';
            $version->issue_date = $request->input('issue_date');
            $version->expiry_date = $request->input('expiry_date');
            $version->status_id = 17; // Active status
            $version->file_path = $filePath;
            $version->storage_path = 'public/' . $filePath;
            $version->is_active = true;
            $version->created_by = auth('tenant')->user()->id;
            $version->save();
            
            // Schedule the reminder using the new reminder system
            $this->scheduleReminder($version, $request->input('reminder_days'));
            
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

    /**
     * Schedule reminder for document expiration using the new reminder system
     *
     * @param DocumentVersion $version
     * @param int $reminderDays
     * @return void
     */
    private function scheduleReminder(DocumentVersion $version, $reminderDays)
    {
        try {
            // Create reminder options
            $options = [
                'recipients' => [$version->document->created_by], // Default to document creator
                'notification_channels' => 'email,system',
            ];
            
            // Create a reminder using the new reminder service
            $reminder = $this->reminderService->createDocumentExpiryReminder(
                $version,
                $reminderDays,
                $options
            );
            
            if ($reminder) {
                Log::info("Reminder created for document version ID {$version->id}, reminder ID: {$reminder->id}");
            } else {
                Log::warning("Failed to create reminder for document version ID {$version->id}");
            }
        } catch (Exception $e) {
            Log::error("Failed to schedule reminder for document version ID {$version->id}: " . $e->getMessage());
        }
    }
}
