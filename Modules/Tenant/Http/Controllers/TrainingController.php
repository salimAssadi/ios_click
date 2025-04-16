<?php

namespace Modules\Tenant\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class TrainingController extends Controller
{
    public function index()
    {
        $trainings = Auth::user()->trainings()->paginate(10);
        return view('tenant::trainings.index', compact('trainings'));
    }

    public function create()
    {
        return view('tenant::trainings.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'trainer' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'status' => 'required|string|in:scheduled,in_progress,completed,cancelled',
            'department_id' => 'required|exists:departments,id',
            'participants' => 'required|array',
            'participants.*' => 'exists:users,id',
            'materials' => 'nullable|array',
            'materials.*' => 'file|max:10240', // 10MB max file size
        ]);

        $training = Auth::user()->trainings()->create($validated);

        if ($request->hasFile('materials')) {
            foreach ($request->file('materials') as $material) {
                $path = $material->store('training_materials');
                $training->materials()->create(['path' => $path]);
            }
        }

        return redirect()->route('tenant.trainings.show', $training)
            ->with('success', __('Training created successfully'));
    }

    public function show($id)
    {
        $training = Auth::user()->trainings()->with(['participants', 'materials'])->findOrFail($id);
        return view('tenant::trainings.show', compact('training'));
    }

    public function edit($id)
    {
        $training = Auth::user()->trainings()->with(['participants', 'materials'])->findOrFail($id);
        return view('tenant::trainings.edit', compact('training'));
    }

    public function update(Request $request, $id)
    {
        $training = Auth::user()->trainings()->findOrFail($id);
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'trainer' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'status' => 'required|string|in:scheduled,in_progress,completed,cancelled',
            'department_id' => 'required|exists:departments,id',
            'participants' => 'required|array',
            'participants.*' => 'exists:users,id',
            'materials' => 'nullable|array',
            'materials.*' => 'file|max:10240', // 10MB max file size
        ]);

        $training->update($validated);

        if ($request->hasFile('materials')) {
            foreach ($request->file('materials') as $material) {
                $path = $material->store('training_materials');
                $training->materials()->create(['path' => $path]);
            }
        }

        return redirect()->route('tenant.trainings.show', $training)
            ->with('success', __('Training updated successfully'));
    }

    public function destroy($id)
    {
        $training = Auth::user()->trainings()->findOrFail($id);
        
        // Delete associated materials
        foreach ($training->materials as $material) {
            Storage::delete($material->path);
            $material->delete();
        }
        
        $training->delete();

        return redirect()->route('tenant.trainings.index')
            ->with('success', __('Training deleted successfully'));
    }
}
