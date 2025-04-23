<?php

namespace Modules\Setting\Http\Controllers;

use App\Http\Controllers\BaseModuleController;
use Modules\Setting\Entities\Department;
use Modules\Setting\Entities\Position;
use Modules\Setting\Entities\Employee;
use Illuminate\Http\Request;

class OrganizationController extends BaseModuleController
{
    public function __construct()
    {
        parent::__construct();
        $this->viewPath = 'setting::organization';
        $this->routePrefix = 'tenant.settings.organization';
        $this->moduleName = 'Setting';
    }

    public function index()
    {
        $departments = Department::with(['positions.employees', 'children'])
            ->whereNull('parent_id')
            ->get();

        $positions = Position::with(['department', 'reportsTo'])
            ->get();

        $employees = Employee::with(['position.department', 'user'])
            ->get();

        return $this->view('index', compact('departments', 'positions', 'employees'));
    }

    public function storeDepartment(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:departments,id',
            'description' => 'nullable|string',
        ]);

        $validated['organization_id'] = tenantId();
        
        if ($validated['parent_id']) {
            $parent = Department::find($validated['parent_id']);
            $validated['level'] = $parent->level + 1;
        }

        Department::create($validated);

        return $this->success('Department added successfully.');
    }

    public function storePosition(Request $request)
    {
        $validated = $request->validate([
            'department_id' => 'required|exists:departments,id',
            'title' => 'required|string|max:255',
            'reports_to_id' => 'nullable|exists:positions,id',
            'description' => 'nullable|string',
        ]);

        Position::create($validated);

        return $this->success('Position added successfully.');
    }

    public function storeEmployee(Request $request)
    {
        $validated = $request->validate([
            'position_id' => 'required|exists:positions,id',
            'user_id' => 'nullable|exists:users,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email',
            'phone' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        Employee::create($validated);

        return $this->success('Employee added successfully.');
    }

    public function updateDepartment(Request $request, $id)
    {
        $department = Department::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $department->update($validated);

        return $this->success('Department updated successfully.');
    }

    public function updatePosition(Request $request, $id)
    {
        $position = Position::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'reports_to_id' => 'nullable|exists:positions,id',
            'description' => 'nullable|string',
        ]);

        $position->update($validated);

        return $this->success('Position updated successfully.');
    }

    public function updateEmployee(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);

        $validated = $request->validate([
            'position_id' => 'required|exists:positions,id',
            'user_id' => 'nullable|exists:users,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email,' . $id,
            'phone' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        $employee->update($validated);

        return $this->success('Employee updated successfully.');
    }

    public function destroyDepartment($id)
    {
        $department = Department::findOrFail($id);
        $department->delete();

        return $this->success('Department deleted successfully.');
    }

    public function destroyPosition($id)
    {
        $position = Position::findOrFail($id);
        $position->delete();

        return $this->success('Position deleted successfully.');
    }

    public function destroyEmployee($id)
    {
        $employee = Employee::findOrFail($id);
        $employee->delete();

        return $this->success('Employee deleted successfully.');
    }

    public function getOrganizationChart()
    {
        $departments = Department::with(['positions.employees', 'children'])
            ->where('organization_id', tenantId())
            ->whereNull('parent_id')
            ->get();

        $chart = $this->buildOrganizationChart($departments);

        return response()->json($chart);
    }

    private function buildOrganizationChart($departments)
    {
        $chart = [];

        foreach ($departments as $department) {
            $node = [
                'id' => 'dept_' . $department->id,
                'text' => $department->name,
                'type' => 'department',
                'children' => []
            ];

            // Add positions in this department
            foreach ($department->positions as $position) {
                $posNode = [
                    'id' => 'pos_' . $position->id,
                    'text' => $position->title,
                    'type' => 'position',
                    'data' => [
                        'reports_to' => $position->reports_to_id ? 'pos_' . $position->reports_to_id : null
                    ]
                ];

                // Add employee if position is filled
                if ($position->currentEmployee) {
                    $posNode['children'] = [[
                        'id' => 'emp_' . $position->currentEmployee->id,
                        'text' => $position->currentEmployee->name,
                        'type' => 'employee'
                    ]];
                }

                $node['children'][] = $posNode;
            }

            // Add sub-departments
            if ($department->children->count() > 0) {
                $node['children'] = array_merge(
                    $node['children'],
                    $this->buildOrganizationChart($department->children)
                );
            }

            $chart[] = $node;
        }

        return $chart;
    }
}
