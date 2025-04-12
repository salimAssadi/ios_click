<?php

namespace App\Http\Controllers\iso_dic;

use App\Http\Controllers\Controller;
use App\Models\IsoPolicy;
use App\Models\IsoPolicyAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class IsoPolicyController extends Controller
{
    public function index()
    {
        $policies = IsoPolicy::with(['attachments'])->paginate(10);
        return view('iso_dic.policies.index', compact('policies'));
    }

    public function create()
    {
        return view('iso_dic.policies.create');
    }

    public function store(Request $request)
    {   
        $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'content' => 'nullable|string',
            'attachments.*' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx|max:10240',
            'is_published' => 'boolean'
        ]); 

        DB::beginTransaction();
        try {
            $policy = IsoPolicy::create([
                'name_ar' => $request->name_ar,
                'name_en' => $request->name_en,
                'description_ar' => $request->description_ar,
                'description_en' => $request->description_en,
                'content' => $request->content,
                'is_published' => $request->is_published ?? false
            ]);
           
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('policies');
                    $policy->attachments()->create([
                        'file_path' => $path,
                        'original_name' => $file->getClientOriginalName(),
                        'mime_type' => $file->getMimeType(),
                        'file_size' => $file->getSize()
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('iso_dic.policies.index')->with('success', __('Policy created successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', __('Error creating policy: ') . $e->getMessage());
        }
    }

    public function edit(IsoPolicy $policy)
    {
        $policy->load(['attachments']);
        return view('iso_dic.policies.edit', compact('policy'));
    }

    public function update(Request $request, IsoPolicy $policy)
    {
        $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'content' => 'nullable|string',
            'attachments.*' => 'file|mimes:pdf,doc,docx,xls,xlsx|max:10240',
            'is_published' => 'boolean'
        ]);

        DB::beginTransaction();
        try {
            $policy->update([
                'name_ar' => $request->name_ar,
                'name_en' => $request->name_en,
                'description_ar' => $request->description_ar,
                'description_en' => $request->description_en,
                'content' => $request->content,
                'is_published' => $request->is_published ?? false
            ]);

            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('policies');
                    $policy->attachments()->create([
                        'file_path' => $path,
                        'original_name' => $file->getClientOriginalName(),
                        'mime_type' => $file->getMimeType(),
                        'file_size' => $file->getSize()
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('iso_dic.policies.index')->with('success', __('Policy updated successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', __('Error updating policy: ') . $e->getMessage());
        }
    }

    public function destroy(IsoPolicy $policy)
    {
        try {
            foreach ($policy->attachments as $attachment) {
                Storage::delete($attachment->file_path);
            }
            $policy->delete();
            return redirect()->route('iso_dic.policies.index')->with('success', __('Policy deleted successfully'));
        } catch (\Exception $e) {
            return back()->with('error', __('Error deleting policy: ') . $e->getMessage());
        }
    }

    public function downloadAttachment(IsoPolicyAttachment $attachment)
    {
        return Storage::download($attachment->file_path, $attachment->original_name);
    }

    public function deleteAttachment(IsoPolicyAttachment $attachment)
    {
        try {
            Storage::delete($attachment->file_path);
            $attachment->delete();
            return back()->with('success', __('Attachment deleted successfully'));
        } catch (\Exception $e) {
            return back()->with('error', __('Error deleting attachment: ') . $e->getMessage());
        }
    }
}
