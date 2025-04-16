<?php

namespace Modules\Tenant\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class RiskController extends Controller
{
    public function index()
    {
        $risks = Auth::user()->risks()->paginate(10);
        return view('tenant::risks.index', compact('risks'));
    }

    public function create()
    {
        return view('tenant::risks.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'probability' => 'required|integer|min:1|max:5',
            'impact' => 'required|integer|min:1|max:5',
            'mitigation_plan' => 'required|string',
            'status' => 'required|string|in:identified,assessed,mitigated,monitored,closed',
            'department_id' => 'required|exists:departments,id',
            'owner_id' => 'required|exists:users,id',
        ]);

        $risk = Auth::user()->risks()->create($validated);

        return redirect()->route('tenant.risks.show', $risk)
            ->with('success', __('Risk created successfully'));
    }

    public function show($id)
    {
        $risk = Auth::user()->risks()->findOrFail($id);
        return view('tenant::risks.show', compact('risk'));
    }

    public function edit($id)
    {
        $risk = Auth::user()->risks()->findOrFail($id);
        return view('tenant::risks.edit', compact('risk'));
    }

    public function update(Request $request, $id)
    {
        $risk = Auth::user()->risks()->findOrFail($id);
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'probability' => 'required|integer|min:1|max:5',
            'impact' => 'required|integer|min:1|max:5',
            'mitigation_plan' => 'required|string',
            'status' => 'required|string|in:identified,assessed,mitigated,monitored,closed',
            'department_id' => 'required|exists:departments,id',
            'owner_id' => 'required|exists:users,id',
        ]);

        $risk->update($validated);

        return redirect()->route('tenant.risks.show', $risk)
            ->with('success', __('Risk updated successfully'));
    }

    public function destroy($id)
    {
        $risk = Auth::user()->risks()->findOrFail($id);
        $risk->delete();

        return redirect()->route('tenant.risks.index')
            ->with('success', __('Risk deleted successfully'));
    }
}
