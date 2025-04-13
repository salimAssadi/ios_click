<?php

namespace App\Http\Controllers\iso_dic;

use App\Http\Controllers\Controller;
use App\Models\IsoInstruction;
use App\Models\IsoInstructionAttachment;
use App\Models\Procedure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class IsoInstructionController extends Controller
{
    public function index()
    {
        $instructions = IsoInstruction::with(['procedures', 'attachments'])->paginate(10);
        return view('iso_dic.instructions.index', compact('instructions'));
    }

    public function create()
    {
        $procedures = Procedure::all();
        return view('iso_dic.instructions.create', compact('procedures'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'content' => 'nullable|string',
            'procedures' => 'required|array',
            'procedures.*' => 'exists:procedures,id',
            'attachments.*' => 'file|mimes:pdf,doc,docx,xls,xlsx|max:10240',
            'is_published' => 'boolean'
        ]);

        DB::beginTransaction();
        try {
            $instruction = IsoInstruction::create([
                'name_ar' => $request->name_ar,
                'name_en' => $request->name_en,
                'description_ar' => $request->description_ar,
                'description_en' => $request->description_en,
                'content' => $request->content,
                'is_published' => $request->is_published ?? false
            ]);

            $instruction->procedures()->attach($request->procedures);

            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('instructions', 'public');
                    $instruction->attachments()->create([
                        'file_path' => $path,
                        'original_name' => $file->getClientOriginalName(),
                        'mime_type' => $file->getMimeType(),
                        'file_size' => $file->getSize()
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('iso_dic.instructions.index')->with('success', __('Instruction created successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', __('Error creating instruction: ') . $e->getMessage());
        }
    }

    public function edit(IsoInstruction $instruction)
    {
        $instruction->load(['procedures', 'attachments']);
        $procedures = Procedure::all();
        return view('iso_dic.instructions.edit', compact('instruction', 'procedures'));
    }

    public function update(Request $request, IsoInstruction $instruction)
    {
        $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'content' => 'nullable|string',
            'procedures' => 'required|array',
            'procedures.*' => 'exists:procedures,id',
            'attachments.*' => 'file|mimes:pdf,doc,docx,xls,xlsx|max:10240',
            'is_published' => 'boolean'
        ]);

        DB::beginTransaction();
        try {
            $instruction->update([
                'name_ar' => $request->name_ar,
                'name_en' => $request->name_en,
                'description_ar' => $request->description_ar,
                'description_en' => $request->description_en,
                'content' => $request->content,
                'is_published' => $request->is_published ?? false
            ]);

            $instruction->procedures()->sync($request->procedures);

            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('instructions', 'public');
                    $instruction->attachments()->create([
                        'file_path' => $path,
                        'original_name' => $file->getClientOriginalName(),
                        'mime_type' => $file->getMimeType(),
                        'file_size' => $file->getSize()
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('iso_dic.instructions.index')->with('success', __('Instruction updated successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', __('Error updating instruction: ') . $e->getMessage());
        }
    }

    public function destroy(IsoInstruction $instruction)
    {
        try {
            foreach ($instruction->attachments as $attachment) {
                Storage::delete($attachment->file_path);
            }
            $instruction->delete();
            return redirect()->route('iso_dic.instructions.index')->with('success', __('Instruction deleted successfully'));
        } catch (\Exception $e) {
            return back()->with('error', __('Error deleting instruction: ') . $e->getMessage());
        }
    }

    public function downloadAttachment(IsoInstructionAttachment $attachment)
    {
        return Storage::download($attachment->file_path, $attachment->original_name);
    }

    public function deleteAttachment(IsoInstructionAttachment $attachment)
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
