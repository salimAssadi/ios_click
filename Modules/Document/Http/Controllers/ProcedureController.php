<?php

namespace Modules\Document\Http\Controllers;

use App\Constants\Status;
use App\Http\Controllers\BaseModuleController;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Mpdf\Mpdf;
use Modules\Document\Entities\Category;
use Modules\Document\Entities\IsoSystem;
use Modules\Document\Entities\Procedure;
use Modules\Document\Entities\Document;
use Modules\Document\Entities\DocumentVersion;
use Modules\Document\Entities\IsoSystemProcedure;
use Modules\Document\Entities\ProcedureAttachment;
use Modules\Setting\Entities\Department;
use Modules\Setting\Entities\Position;
use Meneses\LaravelMpdf\Facades\LaravelMpdf;
use Illuminate\Support\Facades\File;
use Modules\Tenant\Entities\User;
use Modules\Setting\Entities\Employee;

class ProcedureController extends BaseModuleController
{
    /**
     * Display a listing of the resource.
     */
    public function __construct()
    {
        parent::__construct();
        $this->viewPath = 'document::document.procedures';
        $this->routePrefix = 'document.procedures';
        $this->moduleName = 'Procedures';
    }
    
    public function index()
    {
        // $procedures = Procedure::searchable(['name'])->with(['form', 'attachments', 'document.category'])->paginate(10);
        // return view($this->viewPath . '.index', compact('procedures'));
    }

    public function mainProcedures()
    {
        $orginal_procedures = Procedure::where('category_id', '1')->paginate(20);
        $system_id = getSettingsValByName('current_iso_system');
        $category_id = '1';
        $used_procedures = IsoSystemProcedure::where('iso_system_id',$system_id)->where('data','<>',null)->with(['isoSystem','procedure'])->get();
        
        // تعريف الأعمدة المخصصة للجدول
        $customColumns = [
            [
                'data' => 'procedure_coding',
                'title' => __('Procedure Code'),
                'name' => 'documentable.procedure_coding',
                'orderable' => true,
                'searchable' => true,
                'raw' => false,
                'default' => '-'
            ]
        ];
        
        return view($this->viewPath . '.main', compact('used_procedures', 'orginal_procedures', 'category_id', 'customColumns'));
    }

    public function publicProcedures()
    {
        $procedures = Procedure::where('category_id', '2')->paginate(20);
        $category_id = '2';
        $customColumns = [
            [
                'data' => 'procedure_coding',
                'title' => __('Procedure Code'),
                'name' => 'documentable.procedure_coding',
                'orderable' => true,
                'searchable' => true,
                'raw' => false,
                'default' => '-'
            ]
        ];
        return view($this->viewPath . '.public', compact('procedures','category_id','customColumns'));
    }

    public function privateProcedures()
    {
        $procedures = Procedure::where('category_id', '3')->paginate(20);
        $category_id = '3';
        $customColumns = [
            [
                'data' => 'procedure_coding',
                'title' => __('Procedure Code'),
                'name' => 'documentable.procedure_coding',
                'orderable' => true,
                'searchable' => true,
                'raw' => false,
                'default' => '-'
            ]
        ];
        return view($this->viewPath . '.private', compact('procedures','category_id','customColumns'));
    }

    /**
     * Show the form for creating a new resource.
     */

    public function create(Request $request)
    {
        $category_id = $request->category_id;
        $users = User::whereHas('employee')->get()->pluck('name', 'id');
        $procedureCodeing =getSettingsValByName('company_symbol').'-'.generateProcedureCoding(getIsoSystemSymbol(currentISOSystem()),null);
        $categories = Category::where('id',$category_id)->get()->pluck('title', 'id');
        $redirectUrl = url()->previous(); // Correct way
        return view($this->viewPath . '.create', compact('categories','users','procedureCodeing','redirectUrl'));
    }

    
    public function previewPDF($procedure, $contentData, $documentNumber , $department_name_ar, $department_name_en)
    {
        $jobRoles = Position::get();
        $departments = Department::get();

        $viewData = [
            'department_name_ar' => $department_name_ar,
            'department_name_en' => $department_name_en,
            'procedure_name' => $procedure->procedure_name_ar,
            'procedure_coding' => $procedure->procedure_coding,
            'pageTitle' => $procedure->procedure_name,
            'procedure' => $procedure,
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
         $request->procedure_setup_data = json_encode([]);
        $users = Employee::where('user_id','!=',null)->get();
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
            $procedure->blade_view = '';
            $procedure->save();
            $iso_system_Procedure = new IsoSystemProcedure();
            $iso_system_Procedure->iso_system_id = currentISOSystem();
            $iso_system_Procedure->procedure_id = $procedure->id;
            $iso_system_Procedure->category_id = $request->input('category_id');
            $iso_system_Procedure->procedure_coding = getSettingsValByName('company_symbol').'-'.generateProcedureCoding(getIsoSystemSymbol(currentISOSystem()),$procedure->id);
            $iso_system_Procedure->data =  json_encode([]);
            $iso_system_Procedure->created_by = auth('tenant')->user()->id;
            $iso_system_Procedure->parent_id = auth('tenant')->user()->id;
            $iso_system_Procedure->save();
            $document=[
                'prepared_by' => $request->prepared_by,
                'approved_by' => $request->approved_by,
                'reviewer_ids' => $request->has('reviewers') ? json_encode($request->input('reviewers')) : null,
                'issue_date' => $request->issue_date,
                'expiry_date' => $request->expiry_date,
                'reminder_days' => $request->reminder_days,
            ];
            $check_type=$request->category_id;
            if($check_type == '2'){
                $this->SaveDocument($request,$procedure->id,'public',$document);
            }else{
                $this->SaveDocument($request,$procedure->id,'private',$document);
            }
            
            DB::commit();
            
            // If it's an AJAX request, return JSON
            if ($request->ajax()) {
                // Prepare config data for the view
                $jobRoles = Position::get();
                $departments = Department::get();
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
                    'users' => $users,
                ])->render();
                
                return response()->json([
                    'success' => true, 
                    'message' => __('Procedure created successfully!'),
                    'procedure_id' => $procedure->id,
                    'config_html' => $configView
                ]);
            }
            
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
    public function show(string $id)
    {
        $id = Crypt::decrypt($id);
        $procedure = Procedure::find($id);
        $categories = Category::get()->pluck('title', 'id');

        if ($procedure) {
            $procedure->load('attachments');
            $form = $procedure->form()->where('act', 'procedure_' . $id)->first();
            $pageTitle = $procedure->procedure_name;
            $identifier = 'procedure_' . $id;
            return view($this->iso_dic_path . '.procedure_view', compact('pageTitle', 'form', 'categories', 'identifier'));
        }
        return redirect()->back()->with('error', __('Not Found'));
    }

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
                'procedure_id' => $id,
                'category_id' => $category_id,
                'iso_system_id' => currentISOSystem()
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
                                getSettingsValByName('company_symbol').'-'.generateProcedureCoding(getIsoSystemSymbol(currentISOSystem()), $procedure->id);
            
            $users = Employee::where('user_id','!=',null)->get();
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
                    $contentData = (array)$iso_system_Procedure->data;
                }
            }
            // $contentData = $iso_system_Procedure->data??[];
            
            // For debugging
            \Log::info('Content Data Structure:', ['data' => $contentData]);
            
            $categories = Category::where('id', $category_id)->get()->pluck('title', 'id');
            
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
                'document' => $document,
                'users' => $users,
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
            'procedure_name_en' => 'required|string|max:255',
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
            'issue_date' => 'required|date',
            'expiry_date' => 'required|date|after:issue_date',
            'reminder_days' => 'required|integer|min:1|max:365',
            'attachments.*' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx|max:10240',
            'iso_system_procedure_id' => 'required|exists:iso_system_procedures,id',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'errors' => $validator->errors()
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
            // $procedure->procedure_code = $request->input('procedure_code');
            $procedure->save();

            // Update document version if exists
            $document = Document::where('documentable_id', $iso_system_Procedure->id)->where('documentable_type', IsoSystemProcedure::class)->first();
            
            if ($document && $document->lastVersion) {
                $document->update([
                    'reviewer_ids' => $request->has('reviewers') ? json_encode($request->input('reviewers')) : null,
                    'approver_id' => $request->input('approved_by'),
                    'preparer_id' => $request->input('prepared_by'),
                ]);
                
                $version = $document->lastVersion;
                $version->issue_date = $request->input('issue_date');
                $version->expiry_date = $request->input('expiry_date');
                $version->reminder_days = $request->input('reminder_days');
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
        $contentData = $procedure->content??[];
        $pageTitle = __('Configure') . ' ' . $procedure->procedure_name;
        $users = Employee::where('user_id','!=',null)->get();
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
            'users' => $users,
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
            if($category_id == 1){
                // $this->SaveMainProcedure($request,$id);
                $this->SaveDocument($request,$id,'main',null);
            }elseif($category_id == 2){
                $this->SaveDocument($request,$id,'public',null);
            }elseif($category_id == 3){
                $this->SaveDocument($request,$id,'private',null);
            }
           
           
            return response()->json(['message' => 'تم حفظ بيانات الإجراء بنجاح']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'حدث خطأ أثناء حفظ البيانات: ' . $e->getMessage()], 500);
        }
    }

    public function SaveMainProcedure($request,$id)
    {
        try {
            $system_id = getSettingsValByName('current_iso_system');
            $procedureData = json_decode($request->procedure_setup_data, true);
            $procedCofig= IsoSystemProcedure::where('procedure_id', $id)->where('iso_system_id', $system_id)->first();
            if($procedCofig){
                $procedCofig->data = $procedureData;
                $procedCofig->save();
            }else{
                return response()->json(['message' => __('Error while saving data')], 500);
            }
            
            // Generate and save PDF
            $docucment_number = 'MP-'.date('YmdHis');
            $pdfPath = $this->generatePDF($procedCofig->procedure,$procedCofig->data,'main', $docucment_number);
            
            // Check if a document already exists for this procedure
            $existingDocument = Document::where('documentable_type', 'Modules\Document\Entities\IsoSystemProcedure')
                                    ->where('documentable_id', $procedCofig->id)
                                    ->first();
            
            if ($existingDocument) {
                // Update existing document
                $existingDocument->title_ar = $procedCofig->procedure->procedure_name_ar;
                $existingDocument->title_en = $procedCofig->procedure->procedure_name_en;
                $existingDocument->description_ar = $procedCofig->procedure->description_ar;
                $existingDocument->description_en = $procedCofig->procedure->description_en;
                $existingDocument->category_id = $request->category_id;
                $existingDocument->save();
                
                // Create a new version for the existing document
                $latestVersion = DocumentVersion::where('document_id', $existingDocument->id)
                                    ->orderBy('version', 'desc')
                                    ->first();
                
                $newVersionNumber = $latestVersion ? (float)$latestVersion->version + 0.1 : 1.0;
                
                $version = new DocumentVersion();
                $version->document_id = $existingDocument->id;
                $version->version = (string)$newVersionNumber;
                $version->issue_date = null;
                $version->expiry_date = null;
                $version->status_id = 17; // Active status
                $version->file_path = $pdfPath;
                $version->storage_path = $pdfPath;
                $version->is_active = true;
                $version->created_by = auth('tenant')->user()->id;
                $version->save();
                
                // Set previous versions to inactive
                DocumentVersion::where('document_id', $existingDocument->id)
                    ->where('id', '!=', $version->id)
                    ->update(['is_active' => false]);
                
            } else {
                // Create new document
                $document = new Document();
                $document->title_ar = $procedCofig->procedure->procedure_name_ar;
                $document->title_en = $procedCofig->procedure->procedure_name_en;
                $document->description_ar = $procedCofig->procedure->description_ar;
                $document->description_en = $procedCofig->procedure->description_en;
                $document->category_id = $request->category_id;
                $document->document_number = $docucment_number;
                $document->documentable_type = 'Modules\Document\Entities\IsoSystemProcedure';
                $document->documentable_id = $procedCofig->id;
                $document->document_type = 'procedure';
                $document->status_id = 11; // Active by default
                $document->creation_date = now();
                $document->created_by = auth('tenant')->user()->id;
                $document->save();
                
                $procedCofig->document()->save($document);

                // Create the initial document version
                $version = new DocumentVersion();
                $version->document_id = $document->id;
                $version->version = '1.0';
                $version->issue_date = null;
                $version->expiry_date = null;
                $version->status_id = 17; // Active status
                $version->file_path = $pdfPath;
                $version->storage_path = $pdfPath;
                $version->is_active = true;
                $version->created_by = auth('tenant')->user()->id;
                $version->save();
            }
            
            return response()->json([
                'status' => 'success'
            ]);

        } catch (\Exception $e) {
            return response()->json(['message' => 'حدث خطأ أثناء حفظ البيانات: ' . $e->getMessage()], 500);
        }
    }

    public function SaveDocument($request,$procedure_id,$type,$documentdata=[])
    {
        try {
            $system_id = getSettingsValByName('current_iso_system');
            $procedureData = json_decode($request->procedure_setup_data, true);
            $iso_system_Procedure= IsoSystemProcedure::where('procedure_id', $procedure_id)->where('iso_system_id', $system_id)->with('procedure')->first();
            if($iso_system_Procedure){
                $iso_system_Procedure->data = $procedureData;
                $iso_system_Procedure->save();
            }
            $procedure= $iso_system_Procedure->procedure;
          
            $procedure->procedure_coding =  $iso_system_Procedure->procedure_coding;
            

            if($type == 'public'){
                $docucment_number = 'PP-'.date('YmdHis');
            }elseif($type == 'private'){
                $docucment_number = 'PRP-'.date('YmdHis');
            }elseif($type == 'main'){
                $docucment_number = 'MP-'.date('YmdHis');
            }
            
            // Generate and save PDF
            $pdfPath = $this->generatePDF($procedure,$procedureData, $type,$docucment_number);
            
            // Check if a document already exists for this procedure
            $existingDocument = Document::where('documentable_type', 'Modules\Document\Entities\IsoSystemProcedure')
                                    ->where('documentable_id', $iso_system_Procedure->id)
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
                $latestVersion = DocumentVersion::where('document_id', $existingDocument->id)
                                    ->orderBy('version', 'desc')
                                    ->first();
                
                $newVersionNumber = $latestVersion ? (float)$latestVersion->version + 0.1 : 1.0;
                
                $version = new DocumentVersion();
                $version->document_id = $existingDocument->id;
                $version->version = (string)$newVersionNumber;
                $version->issue_date = $documentdata['issue_date']??null;
                $version->expiry_date = $documentdata['expiry_date']??null;
                $version->reminder_days = $documentdata['reminder_days']??null;
                $version->status_id = 17; // Active status
                $version->file_path = $pdfPath;
                $version->storage_path = $pdfPath;
                $version->is_active = true;
                $version->created_by = auth('tenant')->user()->id;
                $version->save();
                
                // Set previous versions to inactive
                DocumentVersion::where('document_id', $existingDocument->id)
                    ->where('id', '!=', $version->id)
                    ->update(['is_active' => false]);
                
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
                $document->reviewer_ids = $documentdata['reviewer_ids']??[];
                $document->preparer_id = $documentdata['prepared_by']??null;
                $document->approver_id = $documentdata['approved_by']??null;
                $document->document_type = 'procedure';
                $document->status_id = 11; // Active by default
                $document->creation_date = now();
                $document->created_by = auth('tenant')->user()->id;
                $document->save();
                
                $iso_system_Procedure->document()->save($document);

                // Create the initial document version
                $version = new DocumentVersion();
                $version->document_id = $document->id;
                $version->version = '1.0';
                $version->issue_date = $documentdata['issue_date']??null;
                $version->expiry_date = $documentdata['expiry_date']??null;
                $version->reminder_days = $documentdata['reminder_days']??null;
                $version->status_id = 17; // Active status
                $version->file_path = $pdfPath;
                $version->storage_path = $pdfPath;
                $version->is_active = true;
                $version->created_by = auth('tenant')->user()->id;
                $version->save();
            }
            
            return response()->json([
                'status' => 'success'
            ]);

        } catch (\Exception $e) {
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
    private function generatePDF($procedure, $contentData, $type, $documentNumber)
    {
        try {
            $jobRoles = Position::get();
            $departments = Department::get();
    
            $viewData = [
                'pageTitle' => $procedure->procedure_name,
                'procedure' => $procedure,
                'procedure_name' => $procedure->procedure_name,
                'procedure_code' => $procedure->procedure_coding,
                'Department_name_ar' =>'الإدارة العليا',
                'Department_name_en' =>'Top Management',
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
            if ($e instanceof \ErrorException && strpos($e->getMessage(), 'Undefined index') !== false) {
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

}
