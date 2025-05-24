<?php

namespace Modules\Document\Http\Controllers;

use App\Http\Controllers\BaseModuleController;
use App\Services\Cache\CacheLoaderService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Meneses\LaravelMpdf\Facades\LaravelMpdf;
use Modules\Document\Entities\Category;
use Modules\Document\Entities\Document;
use Modules\Document\Entities\DocumentVersion;
use Modules\Document\Entities\IsoSystem;
use Modules\Document\Entities\IsoSystemProcedure;
use Modules\Document\Entities\Procedure;
use Modules\Document\Entities\ProcedureAttachment;
use Modules\Document\Entities\Status;
use Modules\Setting\Entities\Department;
use Modules\Setting\Entities\Employee;
use Modules\Setting\Entities\Position;
use Modules\Tenant\Entities\User;
use Illuminate\Support\Str;

class ProcedureController extends BaseModuleController
{
    /**
     * Display a listing of the resource.
     */
    protected $procedureCacheService;
    public function __construct()
    {
        parent::__construct();
        $this->viewPath = 'document::document.procedures';
        $this->routePrefix = 'document.procedures';
        $this->moduleName = 'Procedures';
        $this->procedureCacheService = new CacheLoaderService();
    }

    public function index()
    {
        
    }

    public function mainProcedures()
    {

        $orginal_procedures = $this->procedureCacheService->getOriginalProcedures();
        $system_id = currentISOSystem();
        $currentSystemName = getIsoSystem($system_id)->name;
        $category_id = Category::CATEGORY_MAIN;
        $used_procedures = IsoSystemProcedure::where('iso_system_id', $system_id)->where('data', '<>', null)->with(['isoSystem', 'procedure'])->get();
        $status = Status::where('type', 'document')->get()->pluck('name', 'id');
        $customColumns = [
            [
                'data' => 'procedure_coding',
                'title' => __('Procedure Code'),
                'name' => 'documentable.procedure_coding',
                'orderable' => true,
                'searchable' => true,
                'raw' => false,
                'default' => '-',
            ],
        ];

        $filters = [
            [
                'name' => 'expiry_filter',
                'type' => 'select',
                'label' => __('Expiry Filter'),
                'options' => [
                    'expired' => __('Expired Documents'),
                    'expiring_soon' => __('Expiring Soon (30 days)'),
                ],
                'custom_days' => false,
            ],
            [
                'name' => 'status_filter',
                'type' => 'select',
                'label' => __('Document Status'),
                'options' => $status->toArray(),

                'custom_days' => false,
            ],

        ];
        return view($this->viewPath . '.main', compact('used_procedures', 'orginal_procedures', 'category_id', 'customColumns', 'currentSystemName', 'filters'));
    }

    public function publicProcedures()
    {
        $procedures = Procedure::where('category_id', Category::CATEGORY_PUBLIC)->paginate(20);
        $category_id = Category::CATEGORY_PUBLIC;
        $customColumns = [
            [
                'data' => 'procedure_coding',
                'title' => __('Procedure Code'),
                'name' => 'documentable.procedure_coding',
                'orderable' => true,
                'searchable' => true,
                'raw' => false,
                'default' => '-',
            ],
        ];
        $filters = [];
        return view($this->viewPath . '.public', compact('procedures', 'category_id', 'customColumns', 'filters'));
    }

    public function privateProcedures()
    {
        $procedures = Procedure::where('category_id', Category::CATEGORY_PRIVATE)->paginate(20);
        $category_id = Category::CATEGORY_PRIVATE;
        $customColumns = [
            [
                'data' => 'procedure_coding',
                'title' => __('Procedure Code'),
                'name' => 'documentable.procedure_coding',
                'orderable' => true,
                'searchable' => true,
                'raw' => false,
                'default' => '-',
            ],
        ];
        $filters = [];
        return view($this->viewPath . '.private', compact('procedures', 'category_id', 'customColumns', 'filters'));
    }

    /**
     * Show the form for creating a new resource.
     */

    public function create(Request $request)
    {
        $procedureData = session('procedure_create_data');
        $category_id = decrypt($request->category_id);

        if (!$category_id && $procedureData && isset($procedureData['category_id'])) {
            $category_id = $procedureData['category_id'];
        }
        $users = User::whereHas('employee')->get()->pluck('name', 'id');
        $procedureCodeing = getSettingsValByName('company_symbol') . '-' . generateProcedureCoding(getIsoSystemSymbol(currentISOSystem()), null);
        $categories = Category::where('id', $category_id)->get()->pluck('title', 'id');
        $redirectUrl = url()->previous(); // Correct way
        return view($this->viewPath . '.create', compact('categories', 'users', 'procedureCodeing', 'redirectUrl', 'procedureData'));
    }


    public function previewpdf($document_id)
    {
        $document_id = decrypt($document_id);
        $document = Document::where('id', $document_id)->with(['lastVersion','documentable'])->first();
        if(!$document){
            return redirect()->back()->with('error', __('Document not found'));
        }
        $procedure=$document->documentable?->procedure;
        $isoProcedure=$document->documentable;
        $department_name_ar="الادارة العليا";
        $department_name_en="Top Managerment";
        $contentData =$document->documentable?->data??[];
        $jobRoles = Position::get();
        $departments = Department::get();
        $preparers = $document->preparerlist;
        $reviewers = $document->reviewerslist;
        $approver = $document->approver;
        $users=Employee::whereNotNull('user_id')->get();

        $viewData = [
            'department_name_ar' => $department_name_ar,
            'department_name_en' => $department_name_en,
            'logo' => base64_encode_image(getSettingsValByName('company_logo'),'tenantPublic'),
            'procedure_name' => $document->title,
            'procedure_coding' => $isoProcedure->procedure_coding,
            'pageTitle' => $document->title,
            'procedure' => $procedure,
            'jobRoles' => $jobRoles,
            'departments' => $departments,
            'purposes' => ($contentData['purpose'] ?? []),
            'scopes' => ($contentData['scope'] ?? []),
            'responsibilities' => ($contentData['responsibility'] ?? []),
            'definitions' => ($contentData['definitions'] ?? []),
            'forms' => ($contentData['forms'] ?? []),
            'procedures' => ($contentData['procedures'] ?? []),
            'risk_matrix' => ($contentData['risk_matrix'] ?? []),
            'kpis' => ($contentData['kpis'] ?? []),
            'references' => ($contentData['references'] ?? []),
            'preparers' => $preparers,
            'reviewers' => $reviewers,
            'approver' => $approver,
            'users'=>$users
        ];
        // إنشاء الـ PDF
        $pdf = LaravelMpdf::loadView('template.procedures.procedure_template', $viewData);

        // عرض الـ PDF في المتصفح بدون تحميل
        return $pdf->stream('preview-procedure.pdf');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Define validation rules
        $validator = Validator::make($request->all(), [
            'procedure_name_en' => 'required|string|max:255',
            'procedure_name_ar' => 'required|string|max:255',
            'category_id' => 'required',
            'procedure_description_en' => 'nullable|string|max:1000',
            'procedure_description_ar' => 'nullable|string|max:1000',
            'is_optional' => 'nullable|boolean',
            'status' => 'required|boolean',
            'procedure_code' => 'required|string|max:50',
            'prepared_by' => 'required|exists:users,id',
            'approved_by' => 'required|exists:users,id',
            'reviewers' => 'nullable|array',
            'issue_date' => 'required|date',
            'expiry_date' => 'required|date|after:issue_date',
            'reminder_days' => 'required|integer|min:1|max:365',
            'attachments.*' => 'file|mimes:pdf,doc,docx,xls,xlsx|max:10240',
        ]);

        $users = Employee::where('user_id', '!=', null)->get();
        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }
            return redirect()->route('tenant.document.procedures.private')->withInput()->with('error', $validator->errors());
        }

        DB::beginTransaction();
        try {
            $procedure = new Procedure();
            $procedure->category_id = $request->input('category_id');
            $procedure->uuid =isset($request->uuid) ? $request->input('uuid') : Str::uuid();
            $procedure->procedure_name_ar = $request->input('procedure_name_ar');
            $procedure->procedure_name_en = $request->input('procedure_name_en');
            $procedure->description_ar = $request->input('procedure_description_ar');
            $procedure->description_en = $request->input('procedure_description_en');
            $procedure->is_optional = $request->input('is_optional');
            $procedure->template_path = "";
            $procedure->status = $request->input('status');
            $procedure->has_menual_config = $request->has('has_menual_config') ? 1 : 0;
            $procedure->enable_upload_file = $request->has('enable_upload_file') ? 1 : 0;
            $procedure->enable_editor = $request->has('enable_editor') ? 1 : 0;
            $procedure->content = $request->has('content') ? json_decode($request->content) : json_decode([]);
            $procedure->blade_view = '';
            $procedure->save();
            $iso_system_Procedure = new IsoSystemProcedure();
            $iso_system_Procedure->iso_system_id = currentISOSystem();
            $iso_system_Procedure->procedure_id = $procedure->id;
            $iso_system_Procedure->category_id = $request->input('category_id');
            $iso_system_Procedure->procedure_coding = getSettingsValByName('company_symbol') . '-' . generateProcedureCoding(getIsoSystemSymbol(currentISOSystem()), $procedure->id);
            $iso_system_Procedure->data = $request->has('content') ? json_decode($request->content) : json_decode([]);
            $iso_system_Procedure->created_by = auth('tenant')->user()->id;
            $iso_system_Procedure->parent_id = auth('tenant')->user()->id;
            $iso_system_Procedure->save();
            $document = [
                'prepared_by' => $request->has('prepared_by') ? json_encode($request->input('prepared_by')) : null,
                'approved_by' => $request->approved_by,
                'reviewer_ids' => $request->has('reviewers') ? json_encode($request->input('reviewers')) : null,
                'issue_date' => $request->issue_date,
                'expiry_date' => $request->expiry_date,
                'reminder_days' => $request->reminder_days,
            ];
            $check_type = $request->category_id;
            if ($check_type == Category::CATEGORY_PUBLIC) {
                $this->SaveDocument($request, $procedure->id, 'public', $document);
            } 
            elseif ($check_type == Category::CATEGORY_PRIVATE) {
                $this->SaveDocument($request, $procedure->id, 'private', $document);
            }
            elseif ($check_type == Category::CATEGORY_MAIN) {
                $this->SaveDocument($request, $procedure->id, 'main', $document);
            }

            // If it's an AJAX request, return JSON
            if ($request->ajax()) {
                // Prepare config data for the view
                $jobRoles = Position::get();
                $departments = Department::get();
                $iso_system_references = $this->procedureCacheService->getIsoSystemReference();

                $contentData = $iso_system_Procedure->data ?? [];

                // Render the config view as HTML
                $configView = view($this->viewPath . '.config.procedure', [
                    'procedure' => $procedure,
                    'iso_system_Procedure' => $iso_system_Procedure,
                    'jobRoles' => $jobRoles,
                    'departments' => $departments,
                    'purposes' => ($contentData['purpose'] ?? []),
                    'scopes' => ($contentData['scope'] ?? []),
                    'responsibilities' => ($contentData['responsibility'] ?? []),
                    'definitions' => ($contentData['definitions'] ?? []),
                    'forms' => ($contentData['forms'] ?? []),
                    'procedures' => ($contentData['procedures'] ?? []),
                    'risk_matrix' => ($contentData['risk_matrix'] ?? []),
                    'kpis' => ($contentData['kpis'] ?? []),
                    'references' => ($contentData['references'] ?? []),
                    'users' => $users,
                    'iso_system_references' => $iso_system_references
                ])->render();
                session()->forget('procedure_create_data');
                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => __('Procedure created successfully!'),
                    'procedure_id' => $procedure->id,
                    'config_html' => $configView,
                ]);
            }
            session()->forget('procedure_create_data');
            DB::commit();

            // For regular requests, redirect
            return redirect()->route('tenant.document.procedures.configure', $procedure->id)
                ->with('success', __('Procedure created successfully! Now you can configure it.'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error creating procedure: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return redirect()->route('tenant.document.procedures.private')->with('error', 'error');
        }
    }

    /**
     * Display the specified resource.
     */
    // public function show(string $id)
    // {
    //     $id = Crypt::decrypt($id);
    //     $procedure = Procedure::find($id);
    //     $categories = Category::get()->pluck('title', 'id');

    //     if ($procedure) {
    //         $procedure->load('attachments');
    //         $form = $procedure->form()->where('act', 'procedure_' . $id)->first();
    //         $pageTitle = $procedure->procedure_name;
    //         $identifier = 'procedure_' . $id;
    //         return view($this->iso_dic_path . '.procedure_view', compact('pageTitle', 'form', 'categories', 'identifier'));
    //     }
    //     return redirect()->back()->with('error', __('Not Found'));
    // }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id, $category_id = null)
    {
        try {

            $id = Crypt::decrypt($id);
            $category_id = Crypt::decrypt($category_id);

            // Get the IsoSystemProcedure with its related procedure
            $iso_system_Procedure = IsoSystemProcedure::where([
                'id' => $id,
                'category_id' => $category_id,
                'iso_system_id' => currentISOSystem(),
            ])->with('procedure')->first();

            if (!$iso_system_Procedure || !$iso_system_Procedure->procedure) {
                return redirect()->back()->with('error', __('Not Found'));
            }

            $procedure = $iso_system_Procedure->procedure;

            // Get related document
            $document = Document::where('documentable_id', $iso_system_Procedure->id)
                ->where('documentable_type', 'Modules\\Document\\Entities\\IsoSystemProcedure')
                ->with('documentable', 'lastVersion')
                ->first();

            $procedureCodeing = $iso_system_Procedure->procedure_coding ??
            getSettingsValByName('company_symbol') . '-' . generateProcedureCoding(getIsoSystemSymbol(currentISOSystem()), $procedure->id);

            $users = Employee::where('user_id', '!=', null)->get();
            $jobRoles = Position::get();
            $departments = Department::get();

            // Make sure data is in the correct format
            $contentData = [];
            if (!empty($iso_system_Procedure->data)) {
                if (is_string($iso_system_Procedure->data)) {
                    $contentData = json_decode($iso_system_Procedure->data, true) ?: [];
                } else if (is_array($iso_system_Procedure->data)) {
                    $contentData = $iso_system_Procedure->data;
                } else if (is_object($iso_system_Procedure->data)) {
                    $contentData = (array) $iso_system_Procedure->data;
                }
            }
            // $contentData = $iso_system_Procedure->data??[];

            // For debugging
            \Log::info('Content Data Structure:', ['data' => $contentData]);

            $categories = Category::where('id', $category_id)->get()->pluck('title', 'id');
            $iso_system_references = $this->procedureCacheService->getIsoSystemReference();
            return view($this->viewPath . '.edit', [
                'procedure' => $procedure,
                'iso_system_Procedure' => $iso_system_Procedure,
                'categories' => $categories,
                'procedureCodeing' => $procedureCodeing,
                'selectedCategoryId' => $procedure->category_id,
                'jobRoles' => $jobRoles,
                'departments' => $departments,
                'purposes' => ($contentData['purpose'] ?? []),
                'scopes' => ($contentData['scope'] ?? []),
                'responsibilities' => ($contentData['responsibility'] ?? []),
                'definitions' => ($contentData['definitions'] ?? []),
                'forms' => ($contentData['forms'] ?? []),
                'procedures' => ($contentData['procedures'] ?? []),
                'risk_matrix' => ($contentData['risk_matrix'] ?? []),
                'kpis' => ($contentData['kpis'] ?? []),
                'references' => ($contentData['references'] ?? []),
                'document' => $document,
                'prepared_by' => json_decode($document->preparer_id),
                'approved_by' => $document->approver_id ?? null,
                'reviewers' => json_decode($document->reviewer_ids),
                'users' => $users,
                'iso_system_references' => $iso_system_references
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in edit method: ' . $e->getMessage());
            return redirect()->back()->with('error', __('An error occurred. Please try again.'));
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Define validation rules
        $validator = Validator::make($request->all(), [
            'procedure_name_en' => 'string|max:255',
            'procedure_name_ar' => 'required|string|max:255',
            'procedure_description_en' => 'nullable|string|max:1000',
            'procedure_description_ar' => 'nullable|string|max:1000',
            'is_optional' => 'required|boolean',
            'category_id' => 'required',
            'status' => 'required|boolean',
            'procedure_code' => 'required|string|max:50',
            'prepared_by' => 'required|exists:users,id',
            'approved_by' => 'required|exists:users,id',
            'reviewers' => 'nullable|array',
            'issue_date' => 'date',
            'expiry_date' => 'date|after:issue_date',
            'reminder_days' => 'integer|min:1|max:365',
            'attachments.*' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx|max:10240',
            'iso_system_procedure_id' => 'required|exists:iso_system_procedures,id',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'errors' => $validator->errors(),
                ], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {

            $procedure = Procedure::findOrFail($id);
            $iso_system_Procedure = IsoSystemProcedure::findOrFail($request->input('iso_system_procedure_id'));
            // Update the procedure data
            $procedure->category_id = $request->input('category_id');
            $procedure->procedure_name_ar = $request->input('procedure_name_ar');
            $procedure->procedure_name_en = $request->input('procedure_name_en');
            $procedure->description_ar = $request->input('procedure_description_ar');
            $procedure->description_en = $request->input('procedure_description_en');
            $procedure->is_optional = $request->input('is_optional');
            $procedure->status = $request->input('status');
            $procedure->save();
            $iso_system_Procedure->procedure_coding = $request->input('procedure_code');
            $iso_system_Procedure->save();

            // Update document version if exists
            $existingDocument = Document::where('documentable_id', $iso_system_Procedure->id)->where('documentable_type', IsoSystemProcedure::class)->first();

            if ($existingDocument && $existingDocument->lastVersion) {
                $existingDocument->update([
                    'reviewer_ids' => $request->has('reviewers') ? json_encode($request->input('reviewers')) : null,
                    'approver_id' => $request->input('approved_by'),
                    'preparer_id' => $request->has('prepared_by') ? json_encode($request->input('prepared_by')) : null,
                ]);

                $version = $existingDocument->lastVersion;
                $version->issue_date = $request->input('issue_date');
                $version->expiry_date = $request->input('expiry_date');
                $version->reminder_days = $request->input('reminder_days');
                $version->save();
            } else {
                $docucment_number = 'MP-' . date('YmdHis');
                $document = new Document();
                $document->title_ar = $procedure->procedure_name_ar;
                $document->title_en = $procedure->procedure_name_en;
                $document->description_ar = $procedure->description_ar;
                $document->description_en = $procedure->description_en;
                $document->category_id = $request->category_id;
                $document->document_number = $docucment_number;
                $document->documentable_type = 'Modules\Document\Entities\IsoSystemProcedure';
                $document->documentable_id = $iso_system_Procedure->id;
                $document->reviewer_team = $request->has('reviewers') ? json_encode($request->input('reviewers')) : null;
                $document->preparer_id = $request->has('prepared_by') ? json_encode($request->input('prepared_by')) : null;
                $document->approver_id = $request->input('approved_by');
                $document->document_type = 'procedure';
                $document->status_id = Document::DRAFT_DOCUMENT_STATUS_ID; // DRAFT by default
                $document->creation_date = now();
                $document->created_by = auth('tenant')->user()->id;
                $document->save();

                $version = new DocumentVersion();
                $version->document_id = $document->id;
                $version->version = '1';
                $version->issue_date = $request->input('issue_date');
                $version->expiry_date = $request->input('expiry_date');
                $version->reminder_days = $request->input('reminder_days');
                $version->status_id = Document::NEW_VERSION_STATUS_ID; // Active status
                $version->file_path = '';
                $version->storage_path = '';
                $version->is_active = true;
                $version->created_by = auth('tenant')->user()->id;
                $version->save();
            }

            // Handle file uploads

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => __('Procedure updated successfully'),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating procedure: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        if (!\Auth::check()) {
            return redirect()->back()->with('error', __('Unauthorized'));
        }
        try {
            $procedure = Procedure::findOrFail($id);

            // Delete attachments first
            foreach ($procedure->attachments as $attachment) {
                $attachment->delete();
            }

            // Then delete the procedure
            // $procedure->delete();

            return redirect()->route('iso_dic.procedures.index')->with('success', __('Procedure successfully deleted.'));
        } catch (\Exception $e) {
            Log::error('Error deleting procedure: ' . $e->getMessage());
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Force delete the procedure and its attachments
     */
    public function forceDelete($id)
    {
        if (!\Auth::check()) {
            return redirect()->back()->with('error', __('Unauthorized'));
        }

        try {
            DB::beginTransaction();

            $procedure = Procedure::withTrashed()->findOrFail($id);

            // Force delete attachments first
            foreach ($procedure->attachments()->withTrashed()->get() as $attachment) {
                $attachment->forceDelete();
            }

            // Then force delete the procedure
            $procedure->forceDelete();

            DB::commit();
            return redirect()->route('iso_dic.procedures.index')->with('success', __('Procedure permanently deleted.'));
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error force deleting procedure: ' . $e->getMessage());
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Restore a soft-deleted procedure
     */
    public function restore($id)
    {
        if (!\Auth::check()) {
            return redirect()->back()->with('error', __('Unauthorized'));
        }

        try {
            $procedure = Procedure::withTrashed()->findOrFail($id);
            $procedure->restore(); // This will trigger restore on both procedure and its attachments

            return redirect()->route('iso_dic.procedures.index')->with('success', __('Procedure successfully restored.'));
        } catch (\Exception $e) {
            Log::error('Error restoring procedure: ' . $e->getMessage());
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function configure($id)
    {

        $procedure = Procedure::findOrFail($id);
        $jobRoles = Position::get();
        $departments = Department::get();
        $contentData = $procedure->content ?? [];
        $pageTitle = __('Configure') . ' ' . $procedure->procedure_name;
        $users = Employee::where('user_id', '!=', null)->get();
        $iso_system_referencess = $this->procedureCacheService->getIsoSystemReference();
        // dd($contentData);
        return view('document::document.procedures.configure', [

            'pageTitle' => $pageTitle,
            'procedure' => $procedure,
            'jobRoles' => $jobRoles,
            'departments' => $departments,
            'purposes' => ($contentData['purpose'] ?? []),
            'scopes' => ($contentData['scope'] ?? []),
            'responsibilities' => ($contentData['responsibility'] ?? []),
            'definitions' => ($contentData['definitions'] ?? []),
            'forms' => ($contentData['forms'] ?? []),
            'procedures' => ($contentData['procedures'] ?? []),
            'risk_matrix' => ($contentData['risk_matrix'] ?? []),
            'kpis' => ($contentData['kpis'] ?? []),
            'references' => ($contentData['references'] ?? []),
            'users' => $users,
            'iso_system_referencess' => $iso_system_referencess
        ]);
    }

    public function saveTemplatePath(Request $request, $id)
    {
        // $id = Crypt::decrypt($cid);
        $procedure = Procedure::findOrFail($id);
        $procedure->template_path = $request->input('template_path', '');
        $procedure->save();
        return redirect()->back()->with('success', __('Procedure configured successfully'));
    }

    public function saveConfigure(Request $request, $id)
    {
        try {
            $request->validate([
                'procedure_setup_data' => 'required',
            ]);
            $category_id = $request->category_id;
            if ($category_id == Category::CATEGORY_MAIN) {
                $this->SaveDocument($request, $id, 'main', null);
            } elseif ($category_id == Category::CATEGORY_PUBLIC) {
                $this->SaveDocument($request, $id, 'public', null);
            } elseif ($category_id == Category::CATEGORY_PRIVATE) {
                $this->SaveDocument($request, $id, 'private', null);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'تم حفظ بيانات الإجراء بنجاح']);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ أثناء حفظ البيانات: ' . $e->getMessage(),
            ], 500);
        }
    }

   

    public function SaveDocument($request, $procedure_id, $type, $documentdata = [], $create_file = false)
    {
        try {

            $system_id = getSettingsValByName('current_iso_system');
            $procedureData = json_decode($request->procedure_setup_data, true);
            $iso_system_Procedure = IsoSystemProcedure::where('procedure_id', $procedure_id)->where('iso_system_id', $system_id)->with('procedure')->first();
            if ($iso_system_Procedure) {
                $iso_system_Procedure->data = $procedureData;
                $iso_system_Procedure->save();
            }
            $procedure = $iso_system_Procedure->procedure;

            $procedure->procedure_coding = $iso_system_Procedure->procedure_coding;

            if ($type == 'public') {
                $docucment_number = 'PP-' . date('YmdHis');
            } elseif ($type == 'private') {
                $docucment_number = 'PRP-' . date('YmdHis');
            } elseif ($type == 'main') {
                $docucment_number = 'MP-' . date('YmdHis');
            }
            
            DB::beginTransaction();
            // Check if a document already exists for this procedure
            $existingDocument = Document::where('documentable_type', 'Modules\Document\Entities\IsoSystemProcedure')
                ->where('documentable_id', $iso_system_Procedure->id)->with('lastVersion')
                ->first();
            if ($existingDocument) {
                // Update existing document
                $existingDocument->title_ar = $procedure->procedure_name_ar;
                $existingDocument->title_en = $procedure->procedure_name_en;
                $existingDocument->description_ar = $procedure->description_ar;
                $existingDocument->description_en = $procedure->description_en;
                $existingDocument->category_id = $request->category_id;
                $existingDocument->save();

                // Create a new version for the existing document
                $version = $existingDocument->lastVersion;
                $version->issue_date = $documentdata['issue_date'] ?? $version->issue_date;
                $version->expiry_date = $documentdata['expiry_date'] ?? $version->expiry_date;
                $version->reminder_days = $documentdata['reminder_days'] ?? $version->reminder_days;
                $version->is_active = true;
                $version->created_by = auth('tenant')->user()->id;
                $version->save();
                if($create_file){
                    $preparers = $existingDocument->preparerlist;
                    $reviewers = $existingDocument->reviewerslist;
                    $approver = $existingDocument->approver;
                    $pdfPath = $this->generatePDF($procedure, $procedureData, $type, $docucment_number, $preparers, $reviewers, $approver);
                    $version->file_path = $pdfPath ??'';
                    $version->storage_path = $pdfPath ?? '';
                    $version->save();
                }
                

            } else {
                // Create new document
                $document = new Document();
                $document->title_ar = $procedure->procedure_name_ar;
                $document->title_en = $procedure->procedure_name_en;
                $document->description_ar = $procedure->description_ar;
                $document->description_en = $procedure->description_en;
                $document->category_id = $request->category_id;
                $document->document_number = $docucment_number;
                $document->documentable_type = 'Modules\Document\Entities\IsoSystemProcedure';
                $document->documentable_id = $iso_system_Procedure->id;
                $document->reviewer_ids = $documentdata['reviewer_ids'] ?? [];
                $document->preparer_id = $documentdata['prepared_by'] ?? [];
                $document->approver_id = $documentdata['approved_by'] ?? null;
                $document->document_type = 'procedure';
                $document->status_id = Document::DRAFT_DOCUMENT_STATUS_ID; // Active by default
                $document->creation_date = now();
                $document->created_by = auth('tenant')->user()->id;
                $document->save();

                $iso_system_Procedure->document()->save($document);

                // Create the initial document version
                $version = new DocumentVersion();
                $version->document_id = $document->id;
                $version->version = '1.0';

                $version->issue_date = $documentdata['issue_date'] ?? $version->issue_date;
                $version->expiry_date = $documentdata['expiry_date'] ?? $version->expiry_date;
                $version->reminder_days = $documentdata['reminder_days'] ?? $version->reminder_days;
                $version->status_id = Document::NEW_VERSION_STATUS_ID; // Active status
                $version->file_path = '';
                $version->storage_path = '';
                $version->is_active = true;
                $version->created_by = auth('tenant')->user()->id;
                $version->save();
                if($create_file){
                    $preparers = $document->preparerlist;
                    $reviewers = $document->reviewerslist;
                    $approver = $document->approver;
                    $pdfPath = $this->generatePDF($procedure, $procedureData, $type, $docucment_number, $preparers, $reviewers, $approver);
                    $version->file_path = $pdfPath ??'';
                    $version->storage_path = $pdfPath ?? '';
                    $version->save();
                }
            }

            // Generate and save PDF
           
            DB::commit();
            return response()->json([
                'status' => 'success',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
            return response()->json(['message' => 'حدث خطأ أثناء حفظ البيانات: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Generate PDF from procedure data
     *
     * @param IsoSystemProcedure $procedure
     * @param string $documentNumber
     * @return string The path to the saved PDF
     */
    private function generatePDF($procedure, $contentData, $type, $documentNumber , $preparers, $reviewers, $approver)
    {
        try {
            $jobRoles = Position::get();
            $departments = Department::get();
            $users= Employee::whereNotNull('user_id')->with('user','position','department')->get();
            
            $viewData = [
                'pageTitle' => $procedure->procedure_name,
                'procedure' => $procedure,
                'procedure_name' => $procedure->procedure_name,
                'procedure_code' => $procedure->procedure_coding,
                'Department_name_ar' => 'الإدارة العليا',
                'Department_name_en' => 'Top Management',
                'document_number' => $documentNumber,
                'jobRoles' => $jobRoles,
                'departments' => $departments,
                'purposes' => ($contentData['purpose'] ?? []),
                'scopes' => ($contentData['scope'] ?? []),
                'responsibilities' => ($contentData['responsibility'] ?? []),
                'definitions' => ($contentData['definitions'] ?? []),
                'forms' => ($contentData['forms'] ?? []),
                'procedures' => ($contentData['procedures'] ?? []),
                'risk_matrix' => ($contentData['risk_matrix'] ?? []),
                'kpis' => ($contentData['kpis'] ?? []),
                'references' => ($contentData['references'] ?? []),
                'users' => $users,
                'preparers' => $preparers,
                'reviewers' => $reviewers,
                'approver' => $approver,
            ];

            // Generate PDF
            $pdf = LaravelMpdf::loadView('template.procedures.procedure_template', $viewData);

            // Prepare file name and path
            $fileName = 'procedure_' . $procedure->procedure_coding . '-' . date('YmdHis') . '.pdf';
            $relativePath = getTenantRoot() . '/procedures/' . $type . '/' . $fileName;

            // Get full path to save
            $fullDiskPath = Storage::disk('tenants')->path($relativePath);

            // Ensure directory exists
            File::ensureDirectoryExists(dirname($fullDiskPath));

            // Save PDF to full path
            $pdf->save($fullDiskPath);

            // Return relative path (or full path if needed)
            return $relativePath;

        } catch (\Exception $e) {
            \Log::error('PDF Generation Error: ' . $e->getMessage());
            if ($e->getMessage() === 'TCPDF ERROR: Some data has already been output, can\'t send PDF file') {
                \Log::error('PDF Output Buffer Error: ' . ob_get_contents());
            }
            if ($e instanceof \ErrorException  && strpos($e->getMessage(), 'Undefined index') !== false) {
                \Log::error('PDF Data Error: ' . json_encode($viewData));
            }
            throw $e;
        }
    }

    // public function saveConfigure($id)
    // {
    //     $procedure          = Procedure::findOrFail($id);
    //     $formProcessor  = new FormProcessor();
    //     $generate       = $formProcessor->generate('procedure_' . $procedure->id, true);
    //     $procedure->form_id = @$generate->id ?? 0;
    //     $procedure->save();
    //     return redirect()->back()->with('success', __('Procedure configured successfully'));
    // }

    public function downloadAttachment(ProcedureAttachment $attachment)
    {
        return Storage::download($attachment->file_path, $attachment->original_name);
    }

    public function deleteAttachment($id)
    {
        try {
            $attachment = ProcedureAttachment::findOrFail($id);

            // Delete the file from storage
            Storage::disk('public')->delete($attachment->file_path);

            // Delete the record from database
            $attachment->delete();

            return response()->json(['success' => true, 'message' => __('Attachment deleted successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function status($id)
    {
        return Form::changeStatus($id);
    }

    // public function checkOrCreate(Request $request)
    // {
    //     $procedure_uuid = $request->input('procedure_uuid');
    //     $isoSystemId = currentISOSystem();

    //     $originalProcedure = $this->procedureCacheService
    //         ->getOriginalProcedures()
    //         ->firstWhere('uuid', $procedure_uuid);

    //     if (!$originalProcedure || $originalProcedure->isoSystems->isEmpty()) {
    //         return response()->json([
    //             'status' => 'error',
    //             'title' => __('Not found'),
    //             'message' => __('Procedure not found in ISO Dictionary')
    //         ]);
    //     }

    //     $procedureModel = Procedure::where('uuid', $procedure_uuid)->first();

    //     $existsInLinkTable = IsoSystemProcedure::where('procedure_id', $procedureModel?->id)
    //         ->where('iso_system_id', $isoSystemId)
    //         ->exists();

    //     if ($procedureModel && $existsInLinkTable) {
    //         return response()->json([
    //             'status' => 'exists',
    //             'title' => __('Already exists'),
    //             'message' => __('Procedure already exists in system.')
    //         ]);
    //     }

    //     try {
    //         $user = auth('tenant')->user();
    //         $userId = $user->id;
    //         $parentId = $user->id;
    //         DB::beginTransaction();

    //         if (!$procedureModel) {
    //             $procedureModel = Procedure::create([
    //                 'uuid' => $originalProcedure->uuid,
    //                 'procedure_name_ar' => $originalProcedure->procedure_name_ar,
    //                 'procedure_name_en' => $originalProcedure->procedure_name_en,
    //                 'category_id' =>  $originalProcedure->category_id,
    //                 'description_ar' => $originalProcedure->description_ar,
    //                 'description_en' => $originalProcedure->description_en,
    //                 'template_path' => $originalProcedure->template_path,
    //                 'is_optional' => $originalProcedure->is_optional,
    //                 'form_id' => $originalProcedure->form_id,
    //                 'content' => $originalProcedure->content ?? [],
    //                 'enable_upload_file' => $originalProcedure->enable_upload_file,
    //                 'enable_editor' => $originalProcedure->enable_editor,
    //                 'has_menual_config' => $originalProcedure->has_menual_config,
    //                 'blade_view' => $originalProcedure->blade_view,
    //                 'status' => $originalProcedure->status,
    //             ]);
    //         }

    //         if (!$existsInLinkTable) {
    //             $isoSystemProcedure = IsoSystemProcedure::create([
    //                 'category_id' =>  $originalProcedure->category_id,
    //                 'iso_system_id' => $isoSystemId,
    //                 'procedure_id' => $procedureModel->id,
    //                 'procedure_coding' => $originalProcedure->procedure_coding,
    //                 'created_by' => $userId,
    //                 'parent_id' => $parentId,
    //                 'data' =>  $originalProcedure->content ?? [],
    //             ]);
    //         }

    //         $category_id = $originalProcedure->category_id;
    //         if(empty($category_id) || empty($isoSystemProcedure->id )){
    //             DB::rollBack();
    //             return response()->json([
    //                 'status' => 'error',
    //                 'title' => __('error'),
    //                 'message' => __('Something went wrong. Please try again later.')
    //             ]);
    //         }

    //         $editUrl = route('tenant.document.procedures.edit', [
    //             'id' => encrypt($isoSystemProcedure->id ?? null),
    //             'category_id' => encrypt($category_id)
    //         ]);

    //         DB::commit();
    //         return response()->json([
    //             'status' => 'added',
    //             'edit_url' => $editUrl,
    //             'title' => __('Success'),
    //             'message' => __('Procedure added successfully')
    //         ]);
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         \Log::error('checkOrCreate error: ' . $e->getMessage());

    //         return response()->json([
    //             'status' => 'error',
    //             'title' => __('Error'),
    //             'message' => __('Something went wrong. Please try again later.')
    //         ], 500);
    //     }
    // }

    public function checkOrCreate(Request $request)
    {
        try {
        $procedure_uuid = $request->input('procedure_uuid');
        $isoSystemId = currentISOSystem();

        $originalProcedure = $this->procedureCacheService
            ->getOriginalProcedures()
            ->firstWhere('uuid', $procedure_uuid);

        if (!$originalProcedure || $originalProcedure->isoSystems->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'title' => __('Not found'),
                'message' => __('Procedure not found in ISO Dictionary'),
            ]);
        }

        $procedureModel = Procedure::where('uuid', $procedure_uuid)->first();

        $existsInLinkTable = IsoSystemProcedure::where('procedure_id', $procedureModel?->id)
            ->where('iso_system_id', $isoSystemId)
            ->exists();

        if ($procedureModel && $existsInLinkTable) {
            return response()->json([
                'status' => 'exists',
                'title' => __('Already exists'),
                'message' => __('Procedure already exists in system.'),
            ]);
        }

        // Store in session instead of DB
        $sessionData = [
            'uuid' => $originalProcedure->uuid,
            'procedure_name_ar' => $originalProcedure->procedure_name_ar,
            'procedure_name_en' => $originalProcedure->procedure_name_en,
            'category_id' => $originalProcedure->category_id,
            'description_ar' => $originalProcedure->description_ar,
            'description_en' => $originalProcedure->description_en,
            'template_path' => $originalProcedure->template_path,
            'is_optional' => $originalProcedure->is_optional,
            'form_id' => $originalProcedure->form_id,
            'content' => $originalProcedure->content ?? '',
            'enable_upload_file' => $originalProcedure->enable_upload_file,
            'enable_editor' => $originalProcedure->enable_editor,
            'has_menual_config' => $originalProcedure->has_menual_config,
            'blade_view' => $originalProcedure->blade_view,
            'status' => $originalProcedure->status,
            'procedure_coding' => $originalProcedure->procedure_coding,
        ];

        // Store in session (you may use a key like 'procedure_create_data')
        session(['procedure_create_data' => $sessionData]);

        // Redirect to create page (adjust route name accordingly)
        $createUrl = route('tenant.document.procedures.create', encrypt($originalProcedure->category_id));

        return response()->json([
            'status' => 'added',
            'edit_url' => $createUrl,
            'title' => __('Success'),
            'message' => __('Procedure added successfully'),
        ]);
        } catch (\Exception $e) {
            session()->forget('procedure_create_data');
            \Log::error('checkOrCreate error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'title' => __('Error'),
                'message' => __('Something went wrong. Please try again later.'),
            ], 500);
        }
    }

}
