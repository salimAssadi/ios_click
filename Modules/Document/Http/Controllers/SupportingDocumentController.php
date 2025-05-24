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
use Modules\Document\Entities\SupportingDocument;

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
    
    public function categoryDetail($id=null)
    {
        $customColumns = [
            [
                'data' => 'issue_date',
                'title' => __('Issue Date'),
                'name' => 'lastVersion.issue_date',
                'orderable' => true,
                'searchable' => true,
                'raw' => false,
                'default' => '-'
            ],
            [
                'data' => 'expiry_date',
                'title' => __('Expiry Date'),
                'name' => 'lastVersion.expiry_date',
                'orderable' => true,
                'searchable' => true,
                'raw' => false,
                'default' => '-'
            ]
        ];
        $filters = [
            [
                'name' => 'expiry_filter',
                'type' => 'select',
                'options' => [
                    'expired' => __('Expired'),
                    'expiring_soon' => __('Expiring Soon (30 days)'),
                ],
                'custom_days' => false
            ],
        ];
       $category = Category::get();
       if($id) {
           $category = $category->where('id', $id)->first();
       }
        $category_id = $id;
        return view($this->viewPath . '.index', compact('category_id','filters','customColumns'));
    }

    /**
     * Show all supporting documents
     * 
     * @return \Illuminate\View\View
     */
    public function viewAll()
    {
        $customColumns = [
            [
                'data' => 'issue_date',
                'title' => __('Issue Date'),
                'name' => 'lastVersion.issue_date',
                'orderable' => true,
                'searchable' => true,
                'raw' => false,
                'default' => '-'
            ],
            [
                'data' => 'expiry_date',
                'title' => __('Expiry Date'),
                'name' => 'lastVersion.expiry_date',
                'orderable' => true,
                'searchable' => true,
                'raw' => false,
                'default' => '-'
            ]
        ];
        $filters = [
            [
                'name' => 'expiry_filter',
                'type' => 'select',
                'options' => [
                    'expired' => __('Expired'),
                    'expiring_soon' => __('Expiring Soon (30 days)'),
                ],
                'custom_days' => false
            ],
        ];
        $category_id = null;
        return view($this->viewPath . '.index', compact('category_id','filters','customColumns'));
    }
    
    /**
     * Show the form for creating a new supporting document.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
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
        $validator = Validator::make($request->all(), [
            'title_ar' => 'required|string|max:255',
            'title_en' => 'required|string|max:255',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'issue_date' => 'required|date',
            'expiry_date' => 'required|date|after:issue_date',
            'reminder_days' => 'required|integer|min:1|max:365',
            'file' => 'required|file|mimes:doc,docx,pdf,png,jpg,jpeg|max:10240',
        ]);

        if ($validator->fails()) {
            return redirect()->route('tenant.document.supporting-documents.create')
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
        try {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();

            $filePath = $file->storeAs(getTenantRoot() . '/supporting_documents', $fileName, 'tenants');
            
            $documentNumber = 'SD-' . date('YmdHis');
            
            $supportingDocument = SupportingDocument::create([
                'title_ar' => $request->input('title_ar'),
                'title_en' => $request->input('title_en'),
                'description_ar' => $request->input('description_ar'),
                'description_en' => $request->input('description_en'),
                'category_id' => $request->input('category_id'),
                'issue_date' => $request->input('issue_date'),
                'expiry_date' => $request->input('expiry_date'),
                'reminder_before' => $request->input('reminder_days'),
                'created_by' => auth('tenant')->user()->id,
            ]);

            $document = new Document();
            $document->title_ar = $request->input('title_ar');
            $document->title_en = $request->input('title_en');
            $document->description_ar = $request->input('description_ar');
            $document->description_en = $request->input('description_en');
            $document->category_id = $request->input('category_id');
            $document->document_number = $documentNumber;
            $document->document_type = 'supporting';
            $document->documentable_type = SupportingDocument::class;
            $document->documentable_id = $supportingDocument->id;
            $document->status_id = Document::DRAFT_DOCUMENT_STATUS_ID; // Active by default
            $document->creation_date = now();
            $document->created_by = auth('tenant')->user()->id;
            $document->save();
            
            $version = new DocumentVersion();
            $version->document_id = $document->id;
            $version->version = '1.0';
            $version->issue_date = $request->input('issue_date');
            $version->expiry_date = $request->input('expiry_date');
            $version->status_id = Document::NEW_VERSION_STATUS_ID; // Active status
            $version->file_path = $filePath;
            $version->storage_path = 'public/' . $filePath;
            $version->is_active = true;
            $version->created_by = auth('tenant')->user()->id;
            $version->save();
            
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

    public function edit($id)
    {
        $document = Document::with(['lastVersion'])->findOrFail($id);
        
        // Check if this is a supporting document
        if ($document->document_type !== 'supporting') {
            return redirect()->route('tenant.document.supporting-documents.index')
                ->with('error', __('This is not a supporting document'));
        }
        
        // Get categories appropriate for supporting documents
        $categories = Category::where('type', 'supporting')
            ->whereNotNull('parent_id')
            ->get()
            ->pluck('title', 'id');
        $categories->prepend(__('Select Category'), '');
        
        return view($this->viewPath . '.edit', compact('document', 'categories'));
    }


    public function update(Request $request, $id)
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
            'file' => 'nullable|file|mimes:pdf|max:10240', // Optional for update
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
       
        $document = Document::with(['lastVersion'])->where('id', $id)->first();
       
        // Check if this is a supporting document
        if ($document->document_type !== 'supporting') {
            return redirect()->route('tenant.document.supporting-documents.index')
                ->with('error', __('This is not a supporting document'));
        }
        
        DB::beginTransaction();
        
        try {
            $supportingDocument = SupportingDocument::findOrFail($document->documentable_id);
            $supportingDocument->update([
                'title_ar' => $request->input('title_ar'),
                'title_en' => $request->input('title_en'),
                'description_ar' => $request->input('description_ar'),
                'description_en' => $request->input('description_en'),
                'category_id' => $request->input('category_id'),
                'issue_date' => $request->input('issue_date'),
                'expiry_date' => $request->input('expiry_date'),
                'reminder_before' => $request->input('reminder_days'),
            ]);
            // Update the document
            $document->title_ar = $request->input('title_ar');
            $document->title_en = $request->input('title_en');
            $document->description_ar = $request->input('description_ar');
            $document->description_en = $request->input('description_en');
            $document->category_id = $request->input('category_id');
            $document->save();
            
            $version = $document->lastVersion;
            
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $fileName = time() . '_' . $file->getClientOriginalName();
                
                $filePath = $file->storeAs(getTenantRoot() . '/supporting_documents', $fileName, 'tenants');
                
                if ($version->file_path && Storage::disk('tenants')->exists($version->file_path)) {
                    Storage::disk('tenants')->delete($version->file_path);
                }
                
                // Update file path
                $version->file_path = $filePath;
                $version->storage_path = 'public/' . $filePath;
            }
            
            // Update version dates
            $version->issue_date = $request->input('issue_date');
            $version->expiry_date = $request->input('expiry_date');
            $version->save();
            
            // Update or create reminder
            $this->scheduleReminder($version, $request->input('reminder_days'));
            
            DB::commit();
            return redirect()->route('tenant.document.supporting-documents.index')
                ->with('success', __('Supporting document updated successfully!'));
                
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error updating supporting document: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', __('An error occurred while updating the supporting document.'))
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
