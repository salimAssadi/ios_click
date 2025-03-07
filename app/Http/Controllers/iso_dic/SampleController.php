<?php

namespace App\Http\Controllers\iso_dic;

use App\Http\Controllers\Controller;
use App\Lib\FormProcessor;
use App\Models\Form;
use App\Models\Sample;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator; 

class SampleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $samples = Sample::searchable(['sample_name'])->with('form')->paginate(getPaginate());
        return view($this->iso_dic_path . '.samples.index', compact('samples'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view($this->iso_dic_path.'.samples.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
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
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {   
        $id = Crypt::decrypt($id);
        $sample = Sample::find($id);
        if($sample){
            $form = $sample->form()->where('act', 'sample_'.$id)->first();
            $pageTitle =  $sample->sample_name;
            $identifier = 'sample_'.$id;
            return view($this->iso_dic_path . '.sample_view', compact('pageTitle', 'form','identifier'));
        }
        return redirect()->back()->with('error', __('Not Found'));

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {  
        $sample =Sample::find($id);
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
        $remark= __('Configure');
        $pageTitle =$remark.' '. $sample->sample_name ;

        return view($this->iso_dic_path . '.samples.configure', compact('pageTitle', 'sample'));
    }

    public function saveTemplatePath(Request $request,$id)
    {
        // $id = Crypt::decrypt($cid);
        $sample   = Sample::findOrFail($id);
        $sample->template_path = $request->input('template_path','');
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
}
