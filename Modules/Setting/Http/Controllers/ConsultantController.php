<?php

namespace Modules\Setting\Http\Controllers;

use App\Http\Controllers\BaseModuleController;
use Modules\Setting\Entities\Consultant;
use Illuminate\Http\Request;

class ConsultantController extends BaseModuleController
{
    public function __construct()
    {
        parent::__construct();
        $this->viewPath = 'setting::consultants';
        $this->routePrefix = 'settings.consultants';
        $this->moduleName = 'Setting';
    }

    public function index()
    {
        $consultants = Consultant::get();
        return $this->view('index', compact('consultants'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:consultants,email',
            'phone' => 'nullable|string',
            'specialization' => 'required|string',
            'expertise' => 'nullable|string',
            'bio' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        $validated['tenant_id'] = tenantId();
        Consultant::create($validated);

        return $this->success('Consultant added successfully.');
    }

    public function update(Request $request, $id)
    {
        $consultant = Consultant::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:consultants,email,' . $id,
            'phone' => 'nullable|string',
            'specialization' => 'required|string',
            'expertise' => 'nullable|string',
            'bio' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        $consultant->update($validated);

        return $this->success('Consultant updated successfully.');
    }
    public function show($id)
    {
        $consultant = Consultant::findOrFail($id);
        return $this->view('show', compact('consultant'));
    }

    public function destroy($id)
    {
        $consultant = Consultant::findOrFail($id);
        $consultant->delete();

        return $this->success('Consultant deleted successfully.');
    }
}
