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
        $used_procedures = IsoSystemProcedure::where('iso_system_id',$system_id)->where('data','<>',null)->with(['isoSystem','procedure'])->get();
        return view($this->viewPath . '.main', compact('used_procedures','orginal_procedures'));
    }

    public function publicProcedures()
    {
        $procedures = Procedure::where('category_id', '2')->paginate(20);
        return view($this->viewPath . '.public', compact('procedures'));
    }

    public function privateProcedures()
    {
        $procedures = Procedure::where('category_id', '3')->paginate(20);
        return view($this->viewPath . '.private', compact('procedures'));
    }

    /**
     * Show the form for creating a new resource.
     */

    public function create()
    {
        // $isoSystems = IsoSystem::where('status', STATUS::ENABLE)->get()->pluck('name', 'id');
        // $isoSystems->prepend(__('Select ISO System'), '');
        $categories = Category::where('type','tenant')->get()->pluck('title', 'id');
        $categories->prepend(__('Select Category'), '');
        return view($this->viewPath . '.create', compact('categories'));
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
            'attachments.*' => 'file|mimes:pdf,doc,docx,xls,xlsx|max:10240',

        ]);

        if ($validator->fails()) {
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
            $procedure->blade_view = $request->input('blade_view', '');
            $procedure->save();

            // Handle file uploads
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $fileName = time() . '_' . $file->getClientOriginalName();
                    $filePath = $file->storeAs('procedure_attachments', $fileName, 'public');
                    // Create new attachment without triggering the procedure's deleting event
                    ProcedureAttachment::create([
                        'procedure_id' => $procedure->id,
                        'file_name' => $fileName,
                        'original_name' => $file->getClientOriginalName(),
                        'file_path' => $filePath,
                        'mime_type' => $file->getMimeType(),
                        'file_size' => $file->getSize(),
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('tenant.document.procedures.private')->with('success', __('Procedure created successfully!'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error creating procedure: ' . $e->getMessage());
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
    public function edit(string $id)
    {
        $procedure = Procedure::with('document.category')->findOrFail($id);
        $categories = Category::pluck('title', 'id');
        $categories->prepend(__('Select Category'), '');
        $isoSystems = IsoSystem::pluck('name_ar', 'id');
        $isoSystems->prepend(__('Select ISO System'), '');
        return view($this->iso_dic_path . '.procedures.edit', [
            'procedure' => $procedure,
            'categories' => $categories,
            'isoSystems' => $isoSystems,
            'selectedCategoryId' => $procedure->document?->category_id, // Selected category ID
            'selectedIsoSystemId' => $procedure->document?->iso_system_id, // Selected ISO system ID
        ]);
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
            'attachments.*' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx|max:10240',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $procedure = Procedure::findOrFail($id);

            // Update the procedure data
            $procedure->category_id = $request->input('category_id');
            $procedure->procedure_name_ar = $request->input('procedure_name_ar');
            $procedure->procedure_name_en = $request->input('procedure_name_en');
            $procedure->description_ar = $request->input('procedure_description_ar');
            $procedure->description_en = $request->input('procedure_description_en');
            $procedure->is_optional = $request->input('is_optional');
            $procedure->status = $request->input('status');
            $procedure->has_menual_config = $request->has('has_menual_config') ? 1 : 0;
            $procedure->enable_upload_file = $request->has('enable_upload_file') ? 1 : 0;
            $procedure->enable_editor = $request->has('enable_editor') ? 1 : 0;
            $procedure->blade_view = $request->input('blade_view', '');
            $procedure->save();

            // Handle file uploads
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $fileName = time() . '_' . $file->getClientOriginalName();
                    $filePath = $file->storeAs('procedure_attachments', $fileName, 'public');

                    // Create new attachment without triggering the procedure's deleting event
                    ProcedureAttachment::create([
                        'procedure_id' => $procedure->id,
                        'file_name' => $fileName,
                        'original_name' => $file->getClientOriginalName(),
                        'file_path' => $filePath,
                        'mime_type' => $file->getMimeType(),
                        'file_size' => $file->getSize(),
                    ]);
                }
            }

            DB::commit();
            return redirect()->back()->with('success', __('Procedure updated successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating procedure: ' . $e->getMessage());
            return redirect()->back()->with('error', $e->getMessage());
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
            
        $contentData = $procedure->content;
        $pageTitle = __('Configure') . ' ' . $procedure->procedure_name;
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
            $this->SaveMainProcedure($request,$id);
           
           
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
                
            $docucment_number = 'MP-'.date('YmdHis');
            
            // Generate and save PDF
            $pdfPath = $this->generatePDF($procedCofig, $docucment_number);
            $document = new Document();
            $document->title_ar = $procedCofig->procedure->procedure_name_ar;
            $document->title_en = $procedCofig->procedure->procedure_name_en;
            $document->description_ar = $procedCofig->procedure->description_ar;
            $document->description_en = $procedCofig->procedure->description_en;
            $document->category_id = $request->category_id;
            $document->document_number = $docucment_number;
            $document->document_type = 'procedure';
            $document->status_id = 1; // Active by default
            $document->creation_date = now();
            $document->created_by = auth('tenant')->user()->id;
            $document->save();
            
            // Create the initial document version
            $version = new DocumentVersion();
            $version->document_id = $document->id;
            $version->version = '1.0';
            $version->issue_date = null;
            $version->expiry_date = null;
            $version->status_id = 1; // Active status
            $version->file_path = $pdfPath;
            $version->storage_path = $pdfPath;
            $version->is_active = true;
            $version->created_by = auth('tenant')->user()->id;
            $version->save();
            
            return response()->json([
                'status' => 'success'
            ]);

        } catch (\Exception $e) {
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
    private function generatePDF($procedure, $documentNumber)
    {
        try {
            $jobRoles = Position::get();
            $departments = Department::get();
            // Prepare data for the view
            $contentData = $procedure->data;
            $viewData = [
                'pageTitle' => $procedure->procedure->procedure_name,
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
            
            // Generate PDF using LaravelMpdf
            $pdf = LaravelMpdf::loadView('template.procedures.procedure_template', $viewData);
            
            // Generate a unique filename
            $fileName = 'procedure_' . $procedure->procedure_coding . '-' . date('YmdHis') . '.pdf';
            $yearMonth = date('Y/m');

            $directoryPath = storage_path('app/public/procedures/' . $yearMonth);
            
            // Ensure the directory exists
            if (!file_exists($directoryPath)) {
                mkdir($directoryPath, 0755, true);
            }
            
            $filePath = 'public/procedures/' . $yearMonth . '/' . $fileName;
            $fullPath = storage_path('app/' . $filePath);
            
            $pdf->save($fullPath);
          
            return $filePath;
            
        } catch (\Exception $e) {
            \Log::error('PDF Generation Error: ' . $e->getMessage());
            if ($e->getMessage() == 'TCPDF ERROR: Some data has already been output, can\'t send PDF file') {
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
