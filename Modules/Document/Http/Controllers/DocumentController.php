<?php

namespace Modules\Document\Http\Controllers;

// use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Traits\TenantFileManager;
use Modules\Document\Models\IsoSystem;
use Modules\Document\Models\Procedure;
use Modules\Document\Models\IsoSystemProcedure;

class DocumentController extends Controller
{
   
    // use TenantFileManager;

    public function index()
    {   
        
    }


    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        $isoSystems = IsoSystem::where('status', true)->get(); 
        return view('document::create', compact('isoSystems'));
    }   

    public function getTemplates(Request $request)
    {
        
        $isoSystemId = $request->input('iso_system_id');
        $documentType = $request->input('document_type');
        
        // Fetch templates based on ISO system and document type
        $templates = IsoSystemProcedure::with(['procedure'])
            ->where('iso_system_id', $isoSystemId)
            ->get();

        $htmlRows = '';
        
        // Build HTML rows for each template
        foreach ($templates as $template) {
            $htmlRows .= '<div class="col-md-6 mb-3">';
            $htmlRows .= '<div class="card h-100 cursor-pointer template-card">';
            $htmlRows .= '<div class="card-body shadow-sm rounded bg-gray-100">';
            $htmlRows .= '<div class="form-check">';
            $htmlRows .= '<input class="form-check-input" type="radio" name="template_id" value="' . $template->id . '" id="template_' . $template->id . '">';
            $htmlRows .= '<label class="form-check-label w-100" for="template_' . $template->id . '">';
            $htmlRows .= '<h5 class="mb-2">' . htmlspecialchars($template->procedure->procedure_name_ar) . '</h5>';
            $htmlRows .= '</label>';
            $htmlRows .= '</div>';
            $htmlRows .= '</div>';
            $htmlRows .= '</div>';
            $htmlRows .= '</div>';
        }

        if (empty($htmlRows)) {
            $htmlRows = '<div class="col-12"><div class="alert alert-info">No templates found for this selection.</div></div>';
        }

        return response()->json(['html' => $htmlRows]);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'iso_system_id' => 'required',
            'document_type' => 'required',
            'template_id' => 'required',
            'title' => 'required',
        ]);

        // Create document
        $document = Document::create([
            'title' => $validated['title'],
            'document_type' => $validated['document_type'],
            'template_id' => $validated['template_id'],
            'iso_system_id' => $validated['iso_system_id'],
        ]);

        // Handle file upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $path = $file->store('documents', 'public');
            $document->file = $path;
            $document->save();
        }

        return redirect()->route('document.index')->with('success', 'Document created successfully');
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        // $tenantId =auth('tenant')->user()->id;
        // $config = $this->getTenantFileManagerConfig($tenantId);
        
        // return view('document::filemanager', compact('config'));
        // return view('document::show');
    }

    public function getConfig(Request $request)
    {
        // $tenantId = auth('tenant')->user()->id;
        // $config = $this->getTenantFileManagerConfig($tenantId);
        
        // return response()->json($config);
    }
    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        // return view('document::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'required',
            'document_type' => 'required',
            'template_id' => 'required',
        ]);

        // Update document
        $document = Document::findOrFail($id);
        $document->title = $validated['title'];
        $document->document_type = $validated['document_type'];
        $document->template_id = $validated['template_id'];
        $document->save();

        // Handle file upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $path = $file->store('documents', 'public');
            $document->file = $path;
            $document->save();
        }

        return redirect()->route('document.index')->with('success', 'Document updated successfully');
    }
    public function upload(Request $request)
    {
        $tenantId = auth('tenant')->user()->id;
        $path = $this->getTenantStoragePath($tenantId);
        
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = $file->getClientOriginalName();
            $file->storeAs($path, $filename, 'public');
            
            return response()->json([
                'success' => true,
                'path' => "{$path}/{$filename}"
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'No file uploaded'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
    }
}
