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
use App\Models\ProcedureTemplate;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Config;
use Illuminate\Database\QueryException;

class ProcedureController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $procedures = Procedure::searchable(['name'])->with(['form', 'document.category'])->paginate(getPaginate());
        return view($this->iso_dic_path . '.procedures.index', compact('procedures'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $isoSystems = IsoSystem::where('status', STATUS::ENABLE)->get()->pluck('name_ar', 'id');
        $isoSystems->prepend(__('Select ISO System'), '');
        $category = Category::where('parent_id', parentId())->get()->pluck('title', 'id');
        $category->prepend(__('Select Category'), '');
        return view($this->iso_dic_path . '.procedures.create', compact('category', 'isoSystems'));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Define validation rules
        $validator = Validator::make($request->all(), [
            'procedure_name' => 'required|string|max:255',
            'procedure_description' => 'nullable|string|max:1000',
            'is_optional' => 'required|boolean',
            'status' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        try {
            $procedure = new Procedure();
            $procedure->procedure_name = $request->input('procedure_name');
            $procedure->description = $request->input('procedure_description');
            $procedure->is_optional = $request->input('is_optional');
            $procedure->template_path = "";
            $procedure->status = $request->input('status');
            $procedure->has_menual_config = $request->has('has_menual_config') ? 1 : 0; 
            $procedure->enable_upload_file = $request->has('enable_upload_file') ? 1 : 0; 
            $procedure->enable_editor = $request->has('enable_editor') ? 1 : 0; 
            $procedure->blade_view = $request->input('blade_view',''); 
            $procedure->save();
            return redirect()->back()->with('success', __('Procedure created successfully!'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

       /**
     * Display the specified resource.
     */
    
    public function show(string $id)
    {
        $id = Crypt::decrypt($id);
        $procedure = Procedure::find($id);
        if ($procedure) {
            $form = $procedure->form()->where('act', 'procedure_' . $id)->first();
            $pageTitle =  $procedure->procedure_name;
            $identifier = 'procedure_' . $id;
            return view($this->iso_dic_path . '.procedure_view', compact('pageTitle', 'form', 'identifier'));
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
            'procedure_name' => 'required|string|max:255',
            'procedure_description' => 'nullable|string|max:1000',
            'is_optional' => 'required|boolean',
            'status' => 'required|boolean',
        ]);


        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        try {

            $procedure = Procedure::findOrFail($id);
            if (!$procedure) {
                return redirect()->back()->with('error', __('Procedure not found!'));
            }

            // Update the procedure data
            $procedure->procedure_name = $request->input('procedure_name');
            $procedure->description = $request->input('procedure_description');
            $procedure->is_optional = $request->input('is_optional');
            $procedure->status = $request->input('status');
            $procedure->has_menual_config = $request->has('has_menual_config') ? 1 : 0; 
            $procedure->enable_upload_file = $request->has('enable_upload_file') ? 1 : 0; 
            $procedure->enable_editor = $request->has('enable_editor') ? 1 : 0; 
            $procedure->blade_view = $request->input('blade_view',''); 
            $procedure->save();
            return redirect()->back()->with('success', __('Procedure updated successfully!'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
    // public function update(Request $request, $id)
    // {
    //     try {
    //     // Define validation rules
    //     $validator = Validator::make($request->all(), [
    //         'category_id' => 'required|exists:categories,id',
    //         'iso_system_id' => 'nullable|exists:iso_systems,id',
    //         'procedure_name' => 'required|string|max:255',
    //         'procedure_description' => 'nullable|string|max:1000',
    //         'is_optional' => 'required|boolean',
    //         'status' => 'required|boolean',
    //     ]);

    //     $validator->sometimes('iso_system_id', 'required|exists:iso_systems,id', function ($input) {
    //         return $input->category_id == 2;
    //     });

    //     // Check if validation fails
    //     if ($validator->fails()) {
    //         return redirect()->back()->withErrors($validator)->withInput();
    //     }


    //         DB::beginTransaction();
    //         $procedure = Procedure::findOrFail($id);
    //         if (!$procedure) {
    //             return redirect()->back()->with('error', __('Procedure not found!'));
    //         }

    //         // Update the procedure data
    //         $procedure->procedure_name = $request->input('procedure_name');
    //         $procedure->description = $request->input('procedure_description');
    //         $procedure->is_optional = $request->input('is_optional');
    //         $procedure->status = $request->input('status');
    //         $procedure->save();
    //         $type = 'P';
    //         $isoSystemSymbol=getIsoSystemSymbol($request->iso_system_id);
    //         $formattedProcedureId = sprintf('%02d', $procedure->id); // o
    //         // Fetch the associated document
    //         $IsoSystemProcedure = IsoSystemProcedure::where('procedure_id', $procedure->id)->where('iso_system_id', $request->iso_system_id)->first();
    //         if (!$IsoSystemProcedure) {
    //             // insert the document data
    //             $IsoSystemProcedure = new IsoSystemProcedure();
    //             $IsoSystemProcedure->name = $request->procedure_name;
    //             $IsoSystemProcedure->category_id = $request->category_id;
    //             $IsoSystemProcedure->iso_system_id = $request->iso_system_id ?? 0;
    //             $IsoSystemProcedure->procedure_id = $procedure->id;
    //             $IsoSystemProcedure->procedure_coding ="{$type}-{$isoSystemSymbol}-{$formattedProcedureId}";
    //             $IsoSystemProcedure->description = $request->procedure_description;
    //             $IsoSystemProcedure->created_by = \Auth::user()->id;
    //             $IsoSystemProcedure->parent_id = parentId();
    //         }else {
    //             // Update the IsoSystemProcedure data
    //             $IsoSystemProcedure->name = $request->procedure_name;
    //             $IsoSystemProcedure->category_id = $request->category_id;
    //             $IsoSystemProcedure->iso_system_id = $request->iso_system_id ?? 0;
    //             $IsoSystemProcedure->procedure_id = $procedure->id;
    //             $IsoSystemProcedure->procedure_coding ="{$type}-{$isoSystemSymbol}-{$formattedProcedureId}";
    //             $IsoSystemProcedure->description = $request->procedure_description;
    //             $IsoSystemProcedure->created_by = \Auth::user()->id;
    //             $IsoSystemProcedure->parent_id = parentId();
    //         }
    //         $IsoSystemProcedure->save();
    //         DB::commit();
    //         return redirect()->back()->with('success', __('Procedure updated successfully!'));
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return redirect()->back()->with('error', $e->getMessage());
    //     }
    // }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        if (\Auth::check()) {
            $procedures = Procedure::find($id);
            $procedures->delete();
            return redirect()->route('iso_dic.procedures')->with('success', __('iosSystem successfully deleted.'));
        }
    }

    public function configure($id)
    {

        $procedure   = Procedure::findOrFail($id);
        $templateTitles = [
            Config::get('procedure_templates.purpose_title'),
            Config::get('procedure_templates.scope_title'),
            Config::get('procedure_templates.responsibility_title'),
            Config::get('procedure_templates.definition_title'),
            Config::get('procedure_templates.forms_title'),
            Config::get('procedure_templates.procedure_title'),
            Config::get('procedure_templates.risk_matrix_title'),
            Config::get('procedure_templates.kpis'),
        ];

        $procedureTemplates = ProcedureTemplate::whereIn('title', $templateTitles)->get();

        $groupedTemplates = $procedureTemplates->keyBy('title');
        $pageTitle = __('Configure') . ' ' . $procedure->procedure_name;

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
        return view($this->iso_dic_path . '.procedures.configure', [
            'pageTitle' => $pageTitle,
            'procedure' => $procedure,
            'jobRoles' => $jobRoles,
            'purposes' => $groupedTemplates->get(Config::get('procedure_templates.purpose_title')),
            'scopes' => $groupedTemplates->get(Config::get('procedure_templates.scope_title')),
            'responsibilities' => $groupedTemplates->get(Config::get('procedure_templates.responsibility_title')),
            'definitions' => $groupedTemplates->get(Config::get('procedure_templates.definition_title')),
            'forms' => $groupedTemplates->get(Config::get('procedure_templates.forms_title')),
            'procedures' => $groupedTemplates->get(Config::get('procedure_templates.procedure_title')),
            'risk_matrix' => $groupedTemplates->get(Config::get('procedure_templates.risk_matrix_title')),
            'kpis' => $groupedTemplates->get(Config::get('procedure_templates.kpis')),
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

        $validated = $request->validate([
            'content' => 'required|array',
        ]);
        $template = ProcedureTemplate::where('title', $id)->first();
        if ($template) {
            $template->update([
                'content' => $validated['content'],
            ]);
            return response()->json(['message' => 'تم تحديث البيانات بنجاح']);
        } else {
            $template = new ProcedureTemplate();
            $template->create([
                'title' => $id,
                'content' => $validated['content'],
            ]);
        }
        return response()->json(['message' => 'تم حفظ البيانات بنجاح']);
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

    public function status($id)
    {
        return Form::changeStatus($id);
    }
}
