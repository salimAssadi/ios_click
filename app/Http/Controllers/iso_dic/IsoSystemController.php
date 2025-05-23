<?php

namespace App\Http\Controllers\iso_dic;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\IsoSpecificationItem;
use App\Models\IsoSystem;
use App\Models\Category;
use App\Models\IsoSystemForm;
use App\Models\IsoSystemProcedure;
use App\Models\Procedure;
use App\Models\ProcedureTemplate;
use App\Models\Sample;
use App\Models\VersionHistory;
use Exception;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Meneses\LaravelMpdf\Facades\LaravelMpdf;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

class IsoSystemController extends Controller
{
    public function index()
    {
        if (\Auth::user()->type == 'super admin') {
            $iso_systems = IsoSystem::paginate(getPaginate());
            return view($this->iso_dic_path . '.iso_systems.index', compact('iso_systems'));
        }
    }

    public function create()
    {
        return view($this->iso_dic_path . '.iso_systems.create');
    }
    
    public function store(Request $request)
    {
        try {
            // Check if the user has permission to create an ISO and is a super admin
            if (\Auth::user()->type == 'super admin') {

                // Validate the request data
                $validator = Validator::make(
                    $request->all(),
                    [
                        'name_ar'       => 'required|string|max:255',
                        'name_en'       => 'required|string|max:255',
                        'symbole'       => 'required|string',
                        'code'          => 'required|string|max:50|unique:iso_systems,code',
                        'image'         => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                        'version'       => 'required|string|max:50',
                        'status'        => 'required|in:0,1',
                        'specification' => 'nullable|string',
                    ]
                );

                if ($validator->fails()) {
                    $messages = $validator->getMessageBag();
                    return redirect()->back()->with('error', $messages->first());
                }

                if ($request->hasFile('iso_image')) {
                    try {
                        $file = fileUploader($request->iso_image, getFilePath('isoIcon').null, null);
                    } catch (\Exception $exp) {
                        return redirect()->back()->with('error',  $exp->getMessage());
                    }
                }
                // Create a new IosSystem record
                $iosSystem = new IsoSystem();
                $iosSystem->name_ar       = $request->name_ar;
                $iosSystem->name_en       = $request->name_en;
                $iosSystem->code          = $request->code;
                $iosSystem->symbole       = $request->symbole;
                $iosSystem->image         = $file;
                $iosSystem->version       = $request->version;
                $iosSystem->status        = $request->status;
                $iosSystem->specification = $request->specification;
                $iosSystem->save();

                return redirect()->route('iso_systems.index')
                    ->with('success', __('iso System successfully created.'));
            }
            return redirect()->back()->with('error', __('Permission Denied.'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', __('An error occurred while creating the iso System.'));
        }
    }

    public function show(Request $request, $id)
    {
        try {
            $id = Crypt::decrypt($id); // Decrypt the ID
        } catch (DecryptException $e) {
            return redirect()->route('iso_dic.iso_systems.index')->with('error', __('Invalid or corrupted ID.'));
        }

        if (!is_numeric($id)) {
            return redirect()->route('iso_dic.iso_systems.index')->with('error', __('Invalid ID format.'));
        }

        $isoSystem = IsoSystem::with(['procedures.procedure', 'attachments', 'forms.form'])->find($id);

        if (!$isoSystem) {
            return redirect()->route('iso_systems.index')->with('error', __('ISO System not found.'));
        }

        $specificationItems = IsoSpecificationItem::whereNull('parent_id')
        ->with([
            'children' => function ($childQuery){
                $childQuery->orderBy('item_number', 'asc');
            }
        ])
        ->where('iso_system_id', $isoSystem->id)->orderBy('item_number', 'asc')->get();

        // Validate filter_id
        $filterId = $request->input('procedure_id', -1);
        if (!is_numeric($filterId)) {
            $filterId = -1;
        }
        $selectedProcedureId = $filterId;

        // Prepare page data
        $pageTitle = $isoSystem->name_ar;
        $procedures = $isoSystem->procedures;

        // Fetch and filter forms
        $formsQuery = $isoSystem->forms();
        if ($filterId != -1) {
            $formsQuery->where('iso_system_procedure_id', $filterId);
        }
        $forms = $formsQuery->get();

        // Fetch the latest version
        $latestVersion = VersionHistory::where('document_id', $id)
            ->where('current_version', 1)
            ->first();

        // Provide a fallback if no version history exists
        if (!$latestVersion) {
            $latestVersion = (object)['version' => 'N/A', 'updated_at' => 'N/A'];
        }

        // Handle AJAX request
        if ($request->ajax()) {
            return view('partials.forms', compact('forms'));
        }

        // Return full view for non-AJAX requests
        return view($this->iso_dic_path . '.iso_systems.main', compact(
            'pageTitle',
            'isoSystem',
            'procedures',
            'forms',
            'selectedProcedureId',
            'latestVersion',
            'specificationItems'
        ));
    }

    public function edit($id)
    {
        $id = Crypt::decrypt($id);
        try {
            if (\Auth::user()->type == 'super admin') {
    
                $isoSystem = IsoSystem::find($id);
                if (!$isoSystem) {
                    return redirect()->back()->with('error', __('ISO System not found.'));
                }
    
            return view($this->iso_dic_path . '.iso_systems.edit', compact('isoSystem'));
            }
    
            return redirect()->back()->with('error', __('Permission Denied.'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', __('An error occurred while loading the edit page.'));
        }
    }

    public function download($id)
    {
        $data = $this->initializeProcedureData($id);

        if ($data instanceof \Illuminate\Http\RedirectResponse) {
            return $data;
        }
        $procedureName =$data['pageTitle']; 
        $pdf = LaravelMpdf::loadView('template.procedures.procedure_template', $data);
        return $pdf->download($procedureName . '.pdf');
    }

    public function preview($id)
    {
        $data = $this->initializeProcedureData($id);

        if ($data instanceof \Illuminate\Http\RedirectResponse) {
            return $data; 
        }
        $pdf = LaravelMpdf::loadView('template.procedures.procedure_template', $data);
        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="procedure_document.pdf"'
        ]);
    }
    
    private function initializeProcedureData($id)
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

    public function update(Request $request, $id)
    {
        try {
            // Check if the user has permission to edit an ISO and is a super admin
            if (\Auth::user()->type == 'super admin') {
                // Validate the request data
                $validator = Validator::make(
                    $request->all(),
                    [
                        'name_ar'       => 'required|string|max:255',
                        'name_en'       => 'required|string|max:255',
                        'symbole'       => 'required|string',
                        'code'          => 'required|string|max:50|unique:iso_systems,code,' . $id,
                        'iso_image'     => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                        'version'       => 'required|string|max:50',
                        'status'        => 'required|in:0,1',
                        'specification' => 'nullable|string',
                    ]
                );
    
                if ($validator->fails()) {
                    $messages = $validator->getMessageBag();
                    return redirect()->back()->with('error', $messages->first());
                }
    
                // Find the ISO system by ID
                $iosSystem = IsoSystem::find($id);
    
                if (!$iosSystem) {
                    return redirect()->back()->with('error', __('ISO System not found.'));
                }
    
                // Handle file upload (if a new image is provided)
                if ($request->hasFile('iso_image')) {
                    try {
                        // Delete the old image if it exists
                        if (!empty($iosSystem->image)) {
                            $file = fileUploader($request->iso_image, getFilePath('isoIcon'),null,  $iosSystem->image);
                        }
                        $iosSystem->image = $file;
                    } catch (\Exception $exp) {
                        return redirect()->back()->with('error', __('Couldn\'t upload your image'));
                    }
                }
    
                // Update the ISO system fields
                $iosSystem->name_ar       = $request->name_ar;
                $iosSystem->name_en       = $request->name_en;
                $iosSystem->code          = $request->code;
                $iosSystem->symbole       = $request->symbole;
                $iosSystem->version       = $request->version;
                $iosSystem->status        = $request->status;
                $iosSystem->specification = $request->specification;
    
                // Save the updated record
                $iosSystem->save();
    
                return redirect()->route('iso_dic.iso_systems.index')
                    ->with('success', __('ISO System successfully updated.'));
            }
    
            return redirect()->back()->with('error', __('Permission Denied.'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error',$e->getMessage());
        }
    }

    public function destroy($id)
    {
        if (\Auth::check()) {
            $isoSystem = IsoSystem::find($id);
            $isoSystem->delete();
            return redirect()->route('iso_systems.index')->with('success', __('iosSystem successfully deleted.'));
        }
    }

    public function deleteProcedure($id)
    {
        try {
            $id  = Crypt::decrypt($id);
            DB::beginTransaction();
            $isoSystemProcedure = IsoSystemProcedure::find($id);
            $isoSystemProcedure->isoSystemProcedureForm()->delete();
            $isoSystemProcedure->delete();
            DB::commit();
            return redirect()->back()->with('success', __('Procedure and related samples deleted successfully!'));
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', __('An error occurred while deleting the procedure.'));
        }
    }

    public function addProcedure($cid)
    {
        $id  = Crypt::decrypt($cid);
        $isoSystem = IsoSystem::with('procedures')->find($id);
        if (!$isoSystem) {
            return redirect()->route('iso_systems.index')->with('error', __('iso System not found.'));
        }
        $targetIsoSystemId = $id;
        $pageTitle = __('Iso System Procedure') . $isoSystem->name_ar;
        $isoSystemProcedures = $isoSystem->procedures;
        $procedures = Procedure::all();

        return view($this->iso_dic_path . '.iso_systems.add_procedure', compact('pageTitle', 'targetIsoSystemId', 'procedures', 'isoSystemProcedures'));
    }

    public function saveProcedure(Request $request)
    {
        // Validate the request data
        $validator = \Validator::make($request->all(), [
            'isoSystemId' => 'required|exists:iso_systems,id',
            'procedures' => 'required|array',
            'procedures.*' => 'exists:procedures,id',
        ]);

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return redirect()->back()->with('error', $messages->first());
        }

        // Extract request data
        $isoSystemId = $request->isoSystemId;
        $includeSample = $request->input('includeSample') === 'on';

        try {
            DB::beginTransaction();

            foreach ($request->procedures as $procedureId) {
                $procedure = Procedure::findOrFail($procedureId);
                $this->insertProcedure($procedure, $isoSystemId, $includeSample);
            }

            DB::commit();
            return redirect()->to(route('iso_dic.iso_systems.show', \Illuminate\Support\Facades\Crypt::encrypt($isoSystemId)))
                ->with('success', __('Procedures saved successfully!'));
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    private function insertProcedure(Procedure $procedure, int $isoSystemId, bool $includeSample = false)
    {
        $type = 'P';
        $isoSystemSymbol = getIsoSystemSymbol($isoSystemId);
        $procedureCoding = generateProcedureCoding($isoSystemSymbol, $procedure->id);
        $existingRecord = IsoSystemProcedure::where([
            'iso_system_id' => $isoSystemId,
            'procedure_id' => $procedure->id,
        ])->first();

        if ($existingRecord) {
            return;
        }
        $isoSystemProcedure = IsoSystemProcedure::create([
            'name' => $procedure->name,
            'category_id' => $procedure->category_id,
            'iso_system_id' => $isoSystemId,
            'procedure_id' => $procedure->id,
            'procedure_coding' => $procedureCoding,
            'description' => $procedure->description,
            'created_by' => \Auth::user()->id,
            'parent_id' => parentId(),
        ]);
        // If includeSample is true, insert related samples
        if ($includeSample) {
            $samples = Sample::where('procedure_id', $procedure->id)->get(); // Get related samples
            foreach ($samples as $sample) {
                $this->insertIsoSystemProcedureSample($isoSystemProcedure, $sample);
            }
        }
    }

    private function insertIsoSystemProcedureSample(IsoSystemProcedure $isoSystemProcedure, Sample $sample)
    {
        $modifiedProcedureCoding = preg_replace('/^[A-Z]-/', 'F-', $isoSystemProcedure->procedure_coding, 1);
        $SampleId = sprintf('%02d', $sample->id);
        $sampleCoding = "{$modifiedProcedureCoding}-" . $SampleId;
        $existingRecord = IsoSystemForm::where([
            'iso_system_id' => $isoSystemProcedure->iso_system_id,
            'procedure_id' => $isoSystemProcedure->procedure_id,
            'form_id' => $sample->id,
        ])->first();

        if ($existingRecord) {
            return;
        }
        IsoSystemForm::create([
            'name' => $sample->name,
            'category_id' => $sample->category_id,
            'iso_system_id' => $isoSystemProcedure->iso_system_id,
            'procedure_id' => $isoSystemProcedure->procedure_id,
            'iso_system_procedure_id' => $isoSystemProcedure->id,
            'form_id' => $sample->id,
            'form_coding' => $sampleCoding,
            'description' => $sample->description,
            'created_by' => \Auth::user()->id,
            'parent_id' => parentId(),
        ]);
    }

    
    public function procedures($id)
    {   
        $id = Crypt::decrypt($id);
        $isoSystem = IsoSystem::findOrFail($id);
        $procedures = $isoSystem->procedures()->paginate(getPaginate());
        if (request()->ajax()) {
            return view('iso_dic.iso_systems._procedures', compact('procedures'))->render();
        }
        return $procedures;
    }

    public function samples($id)
    {
        $id = Crypt::decrypt($id);
        $isoSystem = IsoSystem::findOrFail($id);
        $query = $isoSystem->forms();
        
        if (request('procedure_id') && request('procedure_id') != -1) {
            $query->where('procedure_id', request('procedure_id'));
        }
        
        $forms = $query->paginate(getPaginate());
        
        if (request()->ajax()) {
            return view('iso_dic.iso_systems._samples', compact('forms'))->render();
        }
        return $forms;
    }

    public function specificationItems($id)
    {
        $id = Crypt::decrypt($id);
        $isoSystem = IsoSystem::findOrFail($id);
        $specificationItems = $isoSystem->specificationItems()->paginate(getPaginate());
        if (request()->ajax()) {
            return view('iso_dic.specification_items.table', compact('specificationItems'))->render();
        }
        return $specificationItems;
    }

    public function addSample($id)
    {
        return view($this->iso_dic_path . '.iso_systems.add_sample');
    }
}
