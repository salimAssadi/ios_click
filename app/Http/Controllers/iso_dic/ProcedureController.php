<?php

namespace App\Http\Controllers\iso_dic;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\FormProcessor;
use App\Models\Category;
use App\Models\Document;
use App\Models\Form;
use App\Models\IsoSystem;
use App\Models\IsoSystemProcedure;
use App\Models\Procedure;
use App\Models\ProcedureAttachment;
use App\Models\ProcedureTemplate;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Config;
use Illuminate\Database\QueryException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use App\Models\IsoReference;

class ProcedureController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $category_id = $request->category_id;
        $search = $request->search;
        $query = Procedure::with(['form','attachments','category']);
        
        if ($category_id) {
            $query->where('category_id', $category_id);
        }
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('procedure_name_ar', 'like', "%{$search}%")
                  ->orWhere('procedure_name_en', 'like', "%{$search}%")
                  ->orWhere('description_ar', 'like', "%{$search}%")
                  ->orWhere('description_en', 'like', "%{$search}%");
            });
        }
        
        $procedures = $query->paginate(10);
        $categories = Category::where('type','dictionary')->get()->pluck('title', 'id');
        $categories->prepend(__('All Categories'), '');
        
        return view($this->iso_dic_path . '.procedures.index', compact('procedures', 'categories', 'category_id', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $isoSystems = IsoSystem::where('status', STATUS::ENABLE)->get()->pluck('name_ar', 'id');
        $isoSystems->prepend(__('Select ISO System'), '');
        $categories = Category::where('type','dictionary')->get()->pluck('title', 'id');
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
            $procedure->uuid= Str::uuid();

            $procedure->procedure_name_ar = $request->input('procedure_name_ar');
            $procedure->procedure_name_en = $request->input('procedure_name_en');
            $procedure->description_ar = $request->input('procedure_description_ar');
            $procedure->description_en = $request->input('procedure_description_en');
            $procedure->is_optional = $request->input('is_optional');
            $procedure->template_path = "";
            $procedure->status =  0;
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

            Cache::forget('ProcedureDictionary');
            DB::commit();
            
            return redirect()->route('iso_dic.procedures.configure', $procedure->id)->with('success', __('Procedure created successfully!'));
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
        $categories = Category::where('type','dictionary')->get()->pluck('title', 'id');
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
            Cache::forget('ProcedureDictionary');
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
        
        // $jobRoles = Position::all()->pluck('title_ar', 'id');
        $iso_system_references = IsoReference::get();
        $jobRoles = [
            ['id' => 1, 'title' => 'Department Manager'],
            ['id' => 2, 'title' => 'Quality Committee Head'],
            ['id' => 3, 'title' => 'Quality Officer'],
            ['id' => 4, 'title' => 'Section Supervisor'],
            ['id' => 5, 'title' => 'Project Manager'],
            ['id' => 6, 'title' => 'Training Specialist'],
            ['id' => 7, 'title' => 'HR Officer'],
            ['id' => 8, 'title' => 'Quality Engineer'],
            ['id' => 9, 'title' => 'Maintenance Technician'],
            ['id' => 10, 'title' => 'Legal Advisor'],
        ];
        $jobRoles = json_decode(json_encode($jobRoles));

        $contentData = $procedure->content;
        $pageTitle = __('Configure') . ' ' . $procedure->procedure_name;

            // dd($contentData);
        return view($this->iso_dic_path . '.procedures.configure', [
            'pageTitle' => $pageTitle,
            'procedure' => $procedure,
            'jobRoles' => $jobRoles,
            'departments' => [],
            'purposes' => ($contentData['purpose'] ?? []),
            'scopes' => ($contentData['scope'] ?? []),
            'responsibilities' => ($contentData['responsibility'] ?? []),
            'definitions' => ($contentData['definitions'] ?? []),
            'forms' => ($contentData['forms'] ?? []),
            'procedures' => ($contentData['procedures'] ?? []),
            'risk_matrix' => ($contentData['risk_matrix'] ?? []),
            'kpis' => ($contentData['kpis'] ?? []),
            'references' => ($contentData['references'] ?? []),
            'users' => [],
            'iso_system_references' => $iso_system_references
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

    public function publish($id)
    {
        try {
            $id = Crypt::decrypt($id);
            
            $procedure = Procedure::findOrFail($id);
            if(!$procedure){
                return redirect()->back()->with('error', __('Procedure not found'));
            }
            $tenantDatabases = $this->getAllTenantDatabases();
            
            foreach ($tenantDatabases as $databaseName) {
                $this->publishProcedureToTenant($procedure, $databaseName);
            }
            
            return redirect()->back()->with('success', __('Procedure published successfully to all tenants'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', __('Error publishing procedure: ') . $e->getMessage());
        }
    }
    
    /**
     * Get all tenant databases
     * 
     * @return array List of tenant database names
     */
    private function getAllTenantDatabases()
    {
        $tenantDatabases = [];
        
        // Query all companies that should have tenant databases
        $companies = DB::connection('crm')
            ->table('consulting_companies')
            ->select('name_en')
            ->get();
        
        // Format database names according to naming convention
        foreach ($companies as $company) {
            $tenantDatabases[] = 'isoclick_' . $company->name_en;
        }
        
        return $tenantDatabases;
    }
    
   
    private function publishProcedureToTenant(Procedure $procedure, $databaseName)
    {
        try {
            // Set up database connection for the tenant
            $config = [
                'driver' => 'mysql',
                'host' => "127.0.0.1",
                'port' =>   '3306',
                'database' => $databaseName,
                'username' => config('database.connections.tenant.username'),
                'password' => config('database.connections.tenant.password'),
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'prefix' => '',
                'strict' => true,
                'engine' => null,
            ];
          
            
            Config::set("database.connections.tenant", $config);
            
            DB::purge('tenant');
            
            $existingProcedure = DB::connection('tenant')
                ->table('procedures')
                ->where('id', $procedure->id)
                ->first();
            
            if ($existingProcedure) {
                DB::connection('tenant')
                    ->table('procedures')
                    ->where('id', $procedure->id)
                    ->where('category_id', $procedure->category_id)
                    ->update([
                        'procedure_name_ar' => $procedure->procedure_name_ar,
                        'procedure_name_en' => $procedure->procedure_name_en,
                        'description_ar' => $procedure->description_ar,
                        'description_en' => $procedure->description_en,
                        'template_path' => $procedure->template_path,
                        'is_optional' => $procedure->is_optional,
                        'content' => json_encode($procedure->content),
                        'enable_upload_file' => $procedure->enable_upload_file,
                        'enable_editor' => $procedure->enable_editor,
                        'has_menual_config' => $procedure->has_menual_config,
                        'blade_view' => $procedure->blade_view,
                        'status' => 1,
                        'updated_at' => now(),
                    ]);
            } else {
                // Insert new procedure
                DB::connection('tenant')
                    ->table('procedures')
                    ->insert([
                        'id' => $procedure->id,
                        'category_id' => $procedure->category_id,
                        'procedure_name_ar' => $procedure->procedure_name_ar,
                        'procedure_name_en' => $procedure->procedure_name_en,
                        'description_ar' => $procedure->description_ar,
                        'description_en' => $procedure->description_en,
                        'template_path' => $procedure->template_path,
                        'is_optional' => $procedure->is_optional,
                        'form_id' => $procedure->form_id ?? 0,
                        'content' => json_encode($procedure->content),
                        'enable_upload_file' => $procedure->enable_upload_file,
                        'enable_editor' => $procedure->enable_editor,
                        'has_menual_config' => $procedure->has_menual_config,
                        'blade_view' => $procedure->blade_view,
                        'status' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
            }
            
            // Also copy any attachments if they exist
            $this->syncProcedureAttachments($procedure, $databaseName);
            
        } catch (\Exception $e) {
            // Log error but continue with other tenants
            Log::error("Failed to publish procedure to tenant {$databaseName}: " . $e->getMessage());
        }
    }
    
   
    private function syncProcedureAttachments(Procedure $procedure, $databaseName)
    {
        // Get all attachments for this procedure
        $attachments = $procedure->attachments;
        
        if ($attachments && count($attachments) > 0) {
            foreach ($attachments as $attachment) {
                // Check if attachment already exists
                $existingAttachment = DB::connection('tenant')
                    ->table('procedure_attachments')
                    ->where('file_path', $attachment->file_path)
                    ->first();
                
                if (!$existingAttachment) {
                    // Insert new attachment
                    DB::connection('tenant')
                        ->table('procedure_attachments')
                        ->insert([
                            'procedure_id' => $procedure->id,
                            'file_name' => $attachment->file_name,
                            'original_name' => $attachment->original_name,
                            'file_path' => $attachment->file_path,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                }
            }
        }
    }

    public function status($id)
    {
        return Form::changeStatus($id);
    }
}
