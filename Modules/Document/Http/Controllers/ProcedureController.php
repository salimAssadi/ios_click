<?php

namespace Modules\Document\Http\Controllers;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Config;
use Illuminate\Database\QueryException;
use Modules\Document\Entities\Procedure;
use Modules\Document\Entities\Category;
use Modules\Document\Entities\IsoSystem;

class ProcedureController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $procedures = Procedure::searchable(['name'])->with(['form','attachments', 'document.category'])->paginate(10);
        return view($this->iso_dic_path . '.procedures.index', compact('procedures'));
    }

   public function mainProcedures()
    {
        $procedures = Procedure::where('category_id', '1')->paginate(20);
        return view('document::document.procedures.main', compact('procedures'));
    }

    public function publicProcedures()
    {
        $procedures = Procedure::where('category_id', '2')->paginate(20);
        return view('document::document.procedures.public', compact('procedures'));
    }

    public function privateProcedures()
    {
        $procedures = Procedure::where('category_id', '3')->paginate(20);
        return view('document::document.procedures.private', compact('procedures'));
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $isoSystems = IsoSystem::where('status', STATUS::ENABLE)->get()->pluck('name_ar', 'id');
        $isoSystems->prepend(__('Select ISO System'), '');
        $categories = Category::get()->pluck('title', 'id');
        $categories->prepend(__('Select Category'), '');
        return view($this->iso_dic_path . '.procedures.create', compact('categories', 'isoSystems'));
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
            'category_id' =>'required',
            'procedure_description_en' => 'nullable|string|max:1000',
            'procedure_description_ar' => 'nullable|string|max:1000',
            'is_optional' => 'required|boolean',
            'status' => 'required|boolean',
            'attachments.*' => 'file|mimes:pdf,doc,docx,xls,xlsx|max:10240',

        ]);

        if ($validator->fails()) {
            return redirect()->route('iso_dic.procedures.index')->with('error', $validator->errors());
        }
        DB::beginTransaction();
        try {
            $procedure = new Procedure();
            $procedure->category_id= $request->input('category_id');
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
            $procedure->blade_view = $request->input('blade_view',''); 
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
                        'file_size' => $file->getSize()
                    ]); 
                }
            }

            DB::commit();
            return redirect()->route('iso_dic.procedures.index')->with('success', __('Procedure created successfully!'));
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error creating procedure: ' . $e->getMessage());
            return redirect()->route('iso_dic.procedures.index')->with('error','error');
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
            $pageTitle =  $procedure->procedure_name;
            $identifier = 'procedure_' . $id;
            return view($this->iso_dic_path . '.procedure_view', compact('pageTitle', 'form', 'categories','identifier'));
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
            'category_id' =>'required',
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
            $procedure->category_id= $request->input('category_id');
            $procedure->procedure_name_ar = $request->input('procedure_name_ar');
            $procedure->procedure_name_en = $request->input('procedure_name_en');
            $procedure->description_ar = $request->input('procedure_description_ar');
            $procedure->description_en = $request->input('procedure_description_en');
            $procedure->is_optional = $request->input('is_optional');
            $procedure->status = $request->input('status');
            $procedure->has_menual_config = $request->has('has_menual_config') ? 1 : 0; 
            $procedure->enable_upload_file = $request->has('enable_upload_file') ? 1 : 0; 
            $procedure->enable_editor = $request->has('enable_editor') ? 1 : 0; 
            $procedure->blade_view = $request->input('blade_view','');
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
                        'file_size' => $file->getSize()
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
            foreach($procedure->attachments as $attachment) {
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
            foreach($procedure->attachments()->withTrashed()->get() as $attachment) {
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

        $procedure   = Procedure::findOrFail($id);
        
        // $jobRoles = Position::all()->pluck('name_ar', 'id');
        $jobRoles = [
            1 => 'Department Manager',
            2 => 'Quality Committee Head',
            3 => 'Quality Officer',
            4 => 'Section Supervisor',
            5 => 'Project Manager',
            6 => 'Training Specialist',
            7 => 'HR Officer',
            8 => 'Quality Engineer',
            9 => 'Maintenance Technician',
            10 => 'Legal Advisor',
        ];
        
        $contentData = $procedure->content;
        $pageTitle = __('Configure') . ' ' . $procedure->procedure_name;
            // dd($contentData);
        return view('document::document.procedures.configure', [
            'pageTitle' => $pageTitle,
            'procedure' => $procedure,
            'jobRoles' => $jobRoles,
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
        $procedure   = Procedure::findOrFail($id);
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
            
            // تحويل البيانات من JSON إلى كائن PHP
            $procedureData = json_decode($request->procedure_setup_data, true);
            
            // التأكد من صحة البيانات
            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json(['message' => 'خطأ في تنسيق بيانات الإجراء: ' . json_last_error_msg()], 422);
            }
            
            $procedure = Procedure::findOrFail($id);
            $procedure->content = $procedureData;
            $procedure->save();
            
            return response()->json(['message' => 'تم حفظ بيانات الإجراء بنجاح']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'حدث خطأ أثناء حفظ البيانات: ' . $e->getMessage()], 500);
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
