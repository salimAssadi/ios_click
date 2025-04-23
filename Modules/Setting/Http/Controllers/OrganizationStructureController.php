<?php

namespace Modules\Setting\Http\Controllers;

use App\Http\Controllers\BaseModuleController;
use Modules\Setting\Entities\OrganizationStructure;
use Illuminate\Http\Request;

class OrganizationStructureController extends BaseModuleController
{
    public function __construct()
    {
        parent::__construct();
        $this->viewPath = 'setting::organization';
        $this->routePrefix = 'settings.organization';
        $this->moduleName = 'Setting';
    }

    public function index()
    {
        $structures = OrganizationStructure::where('tenant_id', tenantId())
            ->whereNull('parent_id')
            ->with('getAllChildren')
            ->get();
        return $this->view('index', compact('structures'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:organization_structures,id',
            'type' => 'required|string',
            'head_name' => 'nullable|string',
            'head_position' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $validated['tenant_id'] = tenantId();
        
        if ($validated['parent_id']) {
            $parent = OrganizationStructure::find($validated['parent_id']);
            $validated['level'] = $parent->level + 1;
        }

        OrganizationStructure::create($validated);

        return $this->success('Organization structure updated successfully.');
    }

    public function update(Request $request, $id)
    {
        $structure = OrganizationStructure::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string',
            'head_name' => 'nullable|string',
            'head_position' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $structure->update($validated);

        return $this->success('Organization structure updated successfully.');
    }

    public function destroy($id)
    {
        $structure = OrganizationStructure::findOrFail($id);
        $structure->delete();

        return $this->success('Organization structure deleted successfully.');
    }
}
