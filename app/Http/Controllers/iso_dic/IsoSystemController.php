<?php

namespace App\Http\Controllers\iso_dic;
use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\IsoSystem;
use App\Models\VersionHistory;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class IsoSystemController extends Controller
{
    public function index()
    {
        if (\Auth::user()->type == 'super admin') {
            $iso_systems = IsoSystem::get();
            return view($this->iso_dic_path.'.iso_systems.index', compact('iso_systems'));
        }
       
    }

    public function create()
    {
        return view($this->iso_dic_path.'.iso_systems.create');
    }

    

    public function store(Request $request)
    {
        // Check if the user has permission to create an ISO and is a super admin
        if (\Auth::user()->can('create user') && \Auth::user()->type == 'super admin') {

            // Validate the request data
            $validator = \Validator::make(
                $request->all(),
                [
                    'name_ar'       => 'required|string|max:255',
                    'name_en'       => 'required|string|max:255',
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
                $file = $request->file('iso_image');
                $path = $file->store('iso_image', 'public');
            } else {
                $path = null;
            }

            // Create a new IosSystem record
            $iosSystem = new IsoSystem();
            $iosSystem->name_ar       = $request->name_ar;
            $iosSystem->name_en       = $request->name_en;
            $iosSystem->code          = $request->code;
            $iosSystem->image         = $path;
            $iosSystem->version       = $request->version;
            $iosSystem->status        = $request->status;
            $iosSystem->specification = $request->specification;
            $iosSystem->save();

            return redirect()->route('iso_systems.index')
                ->with('success', __('iso System successfully created.'));
        }

        return redirect()->back()->with('error', __('Permission Denied.'));
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $id = Crypt::decrypt($id);
        $document = Document::find($id);
        $latestVersion = VersionHistory::where('document_id', $id)->where('current_version', 1)->first();
        return view($this->iso_dic_path.'.iso_systems.show', compact('document', 'latestVersion'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (\Auth::check()) {
            $isoSystem = IsoSystem::find($id);
            $isoSystem->delete();
            return redirect()->route('iso_systems.index')->with('success', __('iosSystem successfully deleted.'));
        }
    }



}
