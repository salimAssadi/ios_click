<?php

namespace App\Http\Controllers\iso_dic;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\FormProcessor;
use App\Models\Category;
use App\Models\Form;
use App\Models\IsoSystemProcedure;
use App\Models\Procedure;
use App\Models\ProcedureTemplate;
use App\Models\Sample;
use Exception;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Meneses\LaravelMpdf\Facades\LaravelMpdf;
use Illuminate\Support\Facades\Config;
class IsoSystemSampleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {   

        $samplesQuery = Sample::searchable(['sample_name'])->with('procedure');
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
        return view($this->iso_dic_path . '.samples.index', compact('samples','procedures','selectedProcedureId'));
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
        try {

            // Define validation rules
            $validator =    Validator::make($request->all(), [
                'sample_name' => 'required|string|max:255',
                'sample_description' => 'nullable|string|max:1000',
                'is_optional' => 'required|boolean',
                'status' => 'required|boolean',
            ]);

            // Check if validation fails
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            // Store the sample data
            $sample = new Sample();
            $sample->sample_name = $request->input('sample_name');
            $sample->description = $request->input('sample_description');
            $sample->is_optional = $request->input('is_optional');
            $sample->procedure_id = $request->input('procedure_id');
            $sample->template_path = "";
            $sample->status = $request->input('status');

            // // Handle file upload
            // if ($request->hasFile('template_path')) {
            //     $file = $request->file('template_path');
            //     $filePath = $file->store('samples/templates', 'public'); // Save file in storage/app/public/samples/templates
            //     $sample->template_path = $filePath;
            // }

            // Save the sample
            $sample->save();

            // Redirect with success message
            return redirect()->back()->with('success', __('Sample created successfully!'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', __('Failed to create sample. Please try again later.'));
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
        return view($this->iso_dic_path . '.samples.edit', compact('sample'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    
    public function configure($id)
    {
        // $id = Crypt::decrypt($cid);
        $sample   = Sample::findOrFail($id);
        $remark = __('Configure');
        $pageTitle = $remark . ' ' . $sample->sample_name;

        return view($this->iso_dic_path . '.samples.configure', compact('pageTitle', 'sample'));
    }

    public function saveTemplatePath(Request $request, $id)
    {
        // $id = Crypt::decrypt($cid);
        $sample   = Sample::findOrFail($id);
        $sample->template_path = $request->input('template_path', '');
        $sample->save();
        return redirect()->back()->with('success', __('Sample configured successfully'));
    }

    public function saveConfigure($id)
    {
        $sample          = Sample::findOrFail($id);
        $formProcessor  = new FormProcessor();
        $generate       = $formProcessor->generate('sample_' . $sample->id, true);
        $sample->form_id = @$generate->id ?? 0;
        $sample->save();
        return redirect()->back()->with('success', __('Sample configured successfully'));
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
        $pdf = LaravelMpdf::loadView('template.procedure_template', $data);
        return $pdf->download($procedureName . '.pdf');
    }

    public function preview($id)
    {
        $data = $this->initializeFormData($id);

        if ($data instanceof \Illuminate\Http\RedirectResponse) {
            return $data; 
        }
        $pdf = LaravelMpdf::loadView('template.procedure_template', $data);
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

        $procedure = IsoSystemProcedure::with('procedure', 'isoSystem')->where('id', $id)->first();

        if (!$procedure) {
            return redirect()->back()->with('error', __('Procedure not found.'));
        }

        $templateTitles = [
            Config::get('procedure_templates.purpose_title'),
            Config::get('procedure_templates.scope_title'),
            Config::get('procedure_templates.responsibility_title'),
            Config::get('procedure_templates.definition_title'),
            Config::get('procedure_templates.forms_title'),
            Config::get('procedure_templates.procedure_title'),
            Config::get('procedure_templates.risk_matrix_title'),
        ];

        // Fetch and group procedure templates
        $procedureTemplates = ProcedureTemplate::whereIn('title', $templateTitles)->get();
        $groupedTemplates = $procedureTemplates->keyBy('title');

        // Prepare job roles
        $jobRoles = [
            "مدير إدارة",
            "رئيس لجنة الجودة",
            "موظف جودة",
            "مشرف قسم",
            "مدير مشروع",
            "أخصائي تدريب",
            "مسؤول موارد بشرية",
            "مهندس جودة",
            "فني صيانة",
            "مستشار قانوني"
        ];

        $pageTitle = $procedure->procedure?->procedure_name;
        return [
            'pageTitle' => $pageTitle,
            'jobRoles' => $jobRoles,
            'purposes' => $groupedTemplates->get(Config::get('procedure_templates.purpose_title')),
            'scopes' => $groupedTemplates->get(Config::get('procedure_templates.scope_title')),
            'responsibilities' => $groupedTemplates->get(Config::get('procedure_templates.responsibility_title')),
            'definitions' => $groupedTemplates->get(Config::get('procedure_templates.definition_title')),
            'forms' => $groupedTemplates->get(Config::get('procedure_templates.forms_title')),
            'procedures' => $groupedTemplates->get(Config::get('procedure_templates.procedure_title')),
            'risk_matrix' => $groupedTemplates->get(Config::get('procedure_templates.risk_matrix_title')),
        ];
    }
}
