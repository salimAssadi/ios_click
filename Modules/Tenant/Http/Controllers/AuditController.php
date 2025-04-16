<?php

namespace Modules\Tenant\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class AuditController extends Controller
{
    public function index()
    {
        return view('tenant::audits.index');
    }

    public function create()
    {
        return view('tenant::audits.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'audit_date' => 'required|date',
            'status' => 'required|string|in:planned,in_progress,completed,cancelled',
            'type' => 'required|string|in:internal,external,surveillance',
            'department_id' => 'required|exists:departments,id',
            'auditor_id' => 'required|exists:users,id',
        ]);

        $audit = Auth::user()->audits()->create($validated);

        return redirect()->route('tenant.audits.show', $audit)
            ->with('success', __('Audit created successfully'));
    }

    public function show($id)
    {
        $audit = Auth::user()->audits()->findOrFail($id);
        return view('tenant::audits.show', compact('audit'));
    }

    public function edit($id)
    {
        $audit = Auth::user()->audits()->findOrFail($id);
        return view('tenant::audits.edit', compact('audit'));
    }

    public function update(Request $request, $id)
    {
        $audit = Auth::user()->audits()->findOrFail($id);
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'audit_date' => 'required|date',
            'status' => 'required|string|in:planned,in_progress,completed,cancelled',
            'type' => 'required|string|in:internal,external,surveillance',
            'department_id' => 'required|exists:departments,id',
            'auditor_id' => 'required|exists:users,id',
        ]);

        $audit->update($validated);

        return redirect()->route('tenant.audits.show', $audit)
            ->with('success', __('Audit updated successfully'));
    }

    public function destroy($id)
    {
        $audit = Auth::user()->audits()->findOrFail($id);
        $audit->delete();

        return redirect()->route('tenant.audits.index')
            ->with('success', __('Audit deleted successfully'));
    }
}
