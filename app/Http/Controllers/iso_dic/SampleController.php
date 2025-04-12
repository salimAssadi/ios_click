<?php

namespace App\Http\Controllers\iso_dic;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\FormProcessor;
use App\Models\Category;
use App\Models\Form;
use App\Models\Procedure;
use App\Models\ProductScope;
use App\Models\Sample;
use App\Models\SampleAttachment;
use Exception;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Meneses\LaravelMpdf\Facades\LaravelMpdf;
use Illuminate\Support\Facades\Redirect;

class SampleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $samplesQuery = Sample::searchable(['sample_name'])->with(['procedure', 'sampleAttachments']);
        $filterId = $request->input('procedure_id', -1); // Default to -1 if not provided
        if (!is_numeric($filterId)) {
            $filterId = -1;
        }
        $selectedProcedureId = $filterId;
        if ($filterId != -1) {
            $samplesQuery->where('procedure_id', $filterId);
        }
        // dd($samplesQuery);
        $samples = $samplesQuery->paginate(getPaginate());
        $procedures = Procedure::get();
        if ($request->ajax()) {
            return view('iso_dic.samples.sample', compact('samples'));
        }
        return view($this->iso_dic_path . '.samples.index', compact('samples', 'procedures', 'selectedProcedureId'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $procedures = Procedure::where('status', Status::ENABLE)->get()->pluck('procedure_name', 'id');
        $procedures->prepend(__('Select Procedure'), '');
        $category = Category::where('parent_id', parentId())->get()->pluck('title', 'id');
        $category->prepend(__('Select Category'), '');
        return view($this->iso_dic_path . '.samples.create', compact('category', 'procedures'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!\Auth::check()) {
            return redirect()->back()->with('error', __('Unauthorized'));
        }

        try {
            // Define validation rules
            $validator = Validator::make($request->all(), [
                'sample_name_ar' => 'required|string|max:255',
                'sample_name_en' => 'required|string|max:255',
                'sample_description_ar' => 'nullable|string|max:1000',
                'sample_description_en' => 'nullable|string|max:1000',
                'is_optional' => 'required|boolean',
                'status' => 'required|boolean',
                'has_menual_config' => 'nullable|boolean',
                'attachments.*' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx|max:10240',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            DB::beginTransaction();

            // Store the sample data
            $sample = new Sample();
            $sample->sample_name_ar = $request->input('sample_name_ar');
            $sample->sample_name_en = $request->input('sample_name_en');
            $sample->description_ar = $request->input('sample_description_ar');
            $sample->description_en = $request->input('sample_description_en');
            $sample->is_optional = $request->input('is_optional');
            $sample->procedure_id = $request->input('procedure_id');
            $sample->template_path = "";
            $sample->status = $request->input('status');
            $sample->has_menual_config = $request->has('has_menual_config') ? 1 : 0; 
            $sample->enable_upload_file = $request->has('enable_upload_file') ? 1 : 0; 
            $sample->enable_editor = $request->has('enable_editor') ? 1 : 0; 
            $sample->blade_view = $request->input('blade_view','');
            $sample->save();

            // Handle file uploads
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $fileName = time() . '_' . $file->getClientOriginalName();
                    $filePath = $file->storeAs('sample_attachments', $fileName, 'public');
                    
                    SampleAttachment::create([
                        'sample_id' => $sample->id,
                        'file_name' => $fileName,
                        'original_name' => $file->getClientOriginalName(),
                        'file_path' => $filePath,
                        'mime_type' => $file->getMimeType(),
                        'file_size' => $file->getSize()
                    ]);
                }
            }

            DB::commit();
            return redirect()->back()->with('success', __('Sample created successfully!'));
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating sample: ' . $e->getMessage());
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $id = Crypt::decrypt($id);
        $sample = Sample::find($id);
        if ($sample) {
            $form = $sample->form()->where('act', 'sample_' . $id)->first();
            $pageTitle =  $sample->sample_name;
            $identifier = 'sample_' . $id;
            return view($this->iso_dic_path . '.sample_view', compact('pageTitle', 'form', 'identifier'));
        }
        return redirect()->back()->with('error', __('Not Found'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $sample = Sample::find($id);
        $procedures = Procedure::where('status', Status::ENABLE)->get()->pluck('procedure_name_ar', 'id');
        $sample->load('sampleAttachments'); 
        return view($this->iso_dic_path . '.samples.edit', compact('sample', 'procedures'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        if (!\Auth::check()) {
            return redirect()->back()->with('error', __('Unauthorized'));
        }

        try {
            // Define validation rules
            $validator = Validator::make($request->all(), [
                'sample_name_ar' => 'required|string|max:255',
                'sample_name_en' => 'required|string|max:255',
                'description_ar' => 'nullable|string|max:1000',
                'description_en' => 'nullable|string|max:1000',
                'is_optional' => 'required|boolean',
                'status' => 'required|boolean',
                'has_menual_config' => 'nullable|boolean',
                'attachments.*' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx|max:10240',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            DB::beginTransaction();

            $sample = Sample::findOrFail($id);
            
            // Update the sample data
            $sample->sample_name_ar = $request->input('sample_name_ar');
            $sample->sample_name_en = $request->input('sample_name_en');
            $sample->description_ar = $request->input('description_ar');
            $sample->description_en = $request->input('description_en');
            $sample->is_optional = $request->input('is_optional');
            $sample->procedure_id = $request->input('procedure_id');
            $sample->status = $request->input('status');
            $sample->has_menual_config = $request->has('has_menual_config') ? 1 : 0; 
            $sample->enable_upload_file = $request->has('enable_upload_file') ? 1 : 0; 
            $sample->enable_editor = $request->has('enable_editor') ? 1 : 0; 
            $sample->blade_view = $request->input('blade_view','');
            $sample->save();

            // Handle file uploads
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $fileName = time() . '_' . $file->getClientOriginalName();
                    $filePath = $file->storeAs('sample_attachments', $fileName, 'public');
                    $sampleAttachment = new SampleAttachment();
                    $sampleAttachment->sample_id = $sample->id;
                    $sampleAttachment->file_name = $fileName;
                    $sampleAttachment->original_name = $file->getClientOriginalName();
                    $sampleAttachment->file_path = $filePath;
                    $sampleAttachment->mime_type = $file->getMimeType();
                    $sampleAttachment->file_size = $file->getSize();
                    $sampleAttachment->save();
                    
                }
            }

            DB::commit();
            return redirect()->back()->with('success', __('Sample updated successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating sample: ' . $e->getMessage());
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if (!\Auth::check()) {
            return redirect()->back()->with('error', __('Unauthorized'));
        }

        try {
            $sample = Sample::findOrFail($id);
            
            // Delete attachments first
            foreach($sample->attachments as $attachment) {
                Storage::disk('public')->delete($attachment->file_path);
                $attachment->delete();
            }
            
            // Then delete the sample
            // $sample->delete();
            
            return redirect()->route('iso_dic.samples.index')->with('success', __('Sample successfully deleted.'));
        } catch (\Exception $e) {
            Log::error('Error deleting sample: ' . $e->getMessage());
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function configure($id)
    {
        $id = Crypt::decrypt($id);
        $sample   = Sample::with('procedure')->find($id);
        $remark = __('Configure');
        $pageTitle = $remark . ' ' . $sample->sample_name;
        $configdata=[];
        $config = request()->query('config');
        if ($config === 'editor' && $sample->enable_editor){
            return view($this->iso_dic_path . '.samples.config.editor_view' , compact('pageTitle', 'id','sample'));
        }
        if($sample->has_menual_config){
            $parents = ProductScope::all();
            $products= $parents;
            $configdata=[
                'parents' => $parents,
                'products' => $products,
            ];
        }
        // $page= 'sample_config_'.sprintf('%02d', $sample->procedure_id).'_'.sprintf('%02d', $sample->id);
        // return view($this->iso_dic_path . '.samples.config.sample_template' , compact('pageTitle', 'id','sample','configdata'));
        return view($this->iso_dic_path . '.samples.configure' , compact('pageTitle', 'id','sample','configdata'));
    }


    // public function configure($id)
    // {
    //     try {
    //         $id = Crypt::decrypt($id); // Decrypt the ID
    //     } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
    //         return redirect()->back()->with('error', __('Invalid or corrupted ID.'));
    //     }

    //     // Fetch the sample with related procedure data
    //     $sample = Sample::with('procedure')->find($id);

    //     if (!$sample) {
    //         return redirect()->back()->with('error', __('Sample not found.'));
    //     }

    //     // Prepare page title and file name
    //     $remark = __('Configure');
    //     $pageTitle = $remark . ' ' . $sample->sample_name;
    //     $page = 'sample_config_' . sprintf('%02d', $sample->procedure_id) . '_' . sprintf('%02d', $sample->id);

    //     // Define the full path to the Blade file
    //     $bladePath = resource_path("views/{$this->iso_dic_path}/samples/config/{$page}.blade.php");

    //     // Check if the Blade file exists
    //     if (!File::exists($bladePath)) {
    //         // Path to the template Blade file to copy from
    //         $templatePath = resource_path("views/{$this->iso_dic_path}/samples/config/sample_template.blade.php");

    //         // Ensure the template file exists
    //         if (!File::exists($templatePath)) {
    //             return redirect()->back()->with('error', __('Template file not found.'));
    //         }

    //         // Copy the content from the template file to the new file
    //         $templateContent = File::get($templatePath);
    //         File::put($bladePath, $templateContent);
    //     }

    //     // Render the view
    //     return view("{$this->iso_dic_path}.samples.config.{$page}", compact('pageTitle', 'sample'));
    // }

    public function saveTemplatePath(Request $request, $id)
    {
        // $id = Crypt::decrypt($cid);
        $sample   = Sample::findOrFail($id);
        $sample->template_path = $request->input('template_path', '');
        $sample->save();
        return redirect()->back()->with('success', __('Sample configured successfully'));
    }
    public function showuploadview(string $id)
    {
        $sample = Sample::find($id);
        return view($this->iso_dic_path . '.samples.uploadsample', compact('sample'));
    }
    
   
    public function uploadSample(Request $request)
    {
        // Validate the uploaded file
        $request->validate([
            'upload_file' => 'required|file|mimes:txt,doc,docx,pdf,jpg,jpeg,png|max:2048', // Max 2MB
        ]);
        $file = $request->file('upload_file');
        $sample_id = $request->input('sample_id', true);
        $sample   = Sample::findOrFail($sample_id);
        if(!$sample){
            return redirect()->back()->with('error', __('Invalid sample id.'));
        }
        $storagePath = getFilePath('sampleTemplate');
        $filePath = uploadFiles($file, $storagePath,false);
        $sample->template_path =   $filePath;
        $sample->save();
        return redirect()->back()->with('success', 'File uploaded successfully.');
    }

    public function saveConfigure(Request $request,$id)
    {   
        $id = Crypt::decrypt($id);
        $sample  = Sample::findOrFail($id);
        $sample->content = $request->content?? 0;
        $sample->save();
        return redirect()->route('iso_dic.samples.index')->with('success', __('Sample configured successfully'));
    }

    public function status($id)
    {
        return Form::changeStatus($id);
    }


    public function download($id)
    {
        $data = $this->initializeFormData($id);

        if ($data instanceof \Illuminate\Http\RedirectResponse) {
            return $data;
        }
        $procedureName =$data['pageTitle']; 
        $pdf = LaravelMpdf::loadView('template.forms.template', $data);
        return $pdf->download($procedureName . '.pdf');
    }

    public function preview($id)
    {
        $data = $this->initializeFormData($id);
        if ($data instanceof \Illuminate\Http\RedirectResponse) {
            return $data; 
        }
        $pdf = LaravelMpdf::loadView('template.forms.template', compact('data'));
        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="procedure_document.pdf"'
        ]);
    }
    
    private function initializeFormData($id)
    {
        try {
            $id = Crypt::decrypt($id); // Decrypt the ID
        } catch (DecryptException $e) {
            return redirect()->back()->with('error', __('Invalid or corrupted ID.'));
        }

        $sample = Sample::with('procedure')->where('id', $id)->first();

        if (!$sample) {
            return redirect()->back()->with('error', __('Sample not found.'));
        }

       

        // Fetch and group procedure templates
       
        $pageTitle = $sample->sample_name;
        return [
            'pageTitle' => $pageTitle,
            'sample' => $sample
        ];
    }
}
