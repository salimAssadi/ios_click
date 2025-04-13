<?php

namespace App\Http\Controllers\iso_dic;

use App\Http\Controllers\Controller;
use App\Models\IsoReference;
use App\Models\IsoReferenceAttachment;
use App\Models\IsoSystem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class IsoReferenceController extends Controller
{
    public function index()
    {
        $references = IsoReference::with(['isoSystems', 'attachments'])->paginate(10);
        return view('iso_dic.references.index', compact('references'));
    }

    public function create()
    {
        $isoSystems = IsoSystem::all();
        return view('iso_dic.references.create', compact('isoSystems'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'iso_systems' => 'required|array',
            'iso_systems.*' => 'exists:iso_dic.iso_systems,id',
            'attachments.*' => 'file|mimes:pdf,doc,docx,xls,xlsx|max:10240',
            'is_published' => 'boolean'
        ]);

        DB::beginTransaction();
        try {
            $reference = IsoReference::create([
                'name_ar' => $request->name_ar,
                'name_en' => $request->name_en,
                'is_published' => $request->is_published ?? false
            ]);

            $reference->isoSystems()->attach($request->iso_systems);

            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('references', 'public');
                    $reference->attachments()->create([
                        'file_path' => $path,
                        'original_name' => $file->getClientOriginalName(),
                        'mime_type' => $file->getMimeType(),
                        'file_size' => $file->getSize()
                    ]);
                }
            }
            
            DB::commit();
            return redirect()->route('iso_dic.references.index')->with('success', 'Reference created successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error creating reference: ' . $e->getMessage());
        }
    }

    public function edit(IsoReference $reference)
    {
        $reference->load(['isoSystems', 'attachments']);
        $isoSystems = IsoSystem::all();
        return view('iso_dic.references.edit', compact('reference', 'isoSystems'));
    }

    public function update(Request $request, IsoReference $reference)
    {
        $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'iso_systems' => 'required|array',
            'iso_systems.*' => 'exists:iso_dic.iso_systems,id',
            'attachments.*' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx|max:10240',
            'is_published' => 'boolean'
        ]);

        DB::beginTransaction();
        try {
            // Only update the reference data
            $reference->update([
                'name_ar' => $request->name_ar,
                'name_en' => $request->name_en,
                'is_published' => $request->is_published ?? false
            ]);

            // Only sync ISO systems if they are provided
            if ($request->has('iso_systems')) {
                $reference->isoSystems()->sync($request->iso_systems);
            }

            // Add new attachments if any, but don't delete existing ones
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('references', 'public');
                    $reference->attachments()->create([
                        'file_path' => $path,
                        'original_name' => $file->getClientOriginalName(),
                        'mime_type' => $file->getMimeType(),
                        'file_size' => $file->getSize()
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('iso_dic.references.index')->with('success', __('Reference updated successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', __('Error updating reference: ') . $e->getMessage());
        }
    }

    public function destroy(IsoReference $reference)
    {
        try {
            DB::beginTransaction();
            foreach ($reference->attachments as $attachment) {
                Storage::delete($attachment->file_path);
                $attachment->delete();
            }
            $reference->isoSystems()->detach();
            $reference->delete();
            DB::commit();
            return redirect()->route('iso_dic.references.index')->with('success', __('Reference deleted successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', __('Error deleting reference: ') . $e->getMessage());
        }
    }

    public function downloadAttachment(IsoReferenceAttachment $attachment)
    {
        return Storage::download($attachment->file_path, $attachment->original_name);
    }

    public function deleteAttachment(IsoReferenceAttachment $attachment)
    {
        try {
            Storage::delete($attachment->file_path);
            $attachment->delete();
            return back()->with('success', 'Attachment deleted successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting attachment: ' . $e->getMessage());
        }
    }
}
