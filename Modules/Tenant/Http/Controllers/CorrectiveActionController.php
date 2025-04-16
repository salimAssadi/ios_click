<?php

namespace Modules\Tenant\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class CorrectiveActionController extends Controller
{
    public function index()
    {
        $actions = Auth::user()->correctiveActions()->paginate(10);
        return view('tenant::corrective_actions.index', compact('actions'));
    }

    public function create()
    {
        return view('tenant::corrective_actions.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'root_cause' => 'required|string',
            'action_plan' => 'required|string',
            'due_date' => 'required|date',
            'priority' => 'required|string|in:low,medium,high',
            'status' => 'required|string|in:open,in_progress,completed,verified',
            'department_id' => 'required|exists:departments,id',
            'assigned_to' => 'required|exists:users,id',
        ]);

        $action = Auth::user()->correctiveActions()->create($validated);

        return redirect()->route('tenant.corrective-actions.show', $action)
            ->with('success', __('Corrective action created successfully'));
    }

    public function show($id)
    {
        $action = Auth::user()->correctiveActions()->findOrFail($id);
        return view('tenant::corrective_actions.show', compact('action'));
    }

    public function edit($id)
    {
        $action = Auth::user()->correctiveActions()->findOrFail($id);
        return view('tenant::corrective_actions.edit', compact('action'));
    }

    public function update(Request $request, $id)
    {
        $action = Auth::user()->correctiveActions()->findOrFail($id);
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'root_cause' => 'required|string',
            'action_plan' => 'required|string',
            'due_date' => 'required|date',
            'priority' => 'required|string|in:low,medium,high',
            'status' => 'required|string|in:open,in_progress,completed,verified',
            'department_id' => 'required|exists:departments,id',
            'assigned_to' => 'required|exists:users,id',
        ]);

        $action->update($validated);

        return redirect()->route('tenant.corrective-actions.show', $action)
            ->with('success', __('Corrective action updated successfully'));
    }

    public function destroy($id)
    {
        $action = Auth::user()->correctiveActions()->findOrFail($id);
        $action->delete();

        return redirect()->route('tenant.corrective-actions.index')
            ->with('success', __('Corrective action deleted successfully'));
    }
}
