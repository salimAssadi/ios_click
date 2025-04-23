<?php

namespace Modules\Setting\Http\Controllers;

use App\Http\Controllers\BaseModuleController;
use Illuminate\Http\Request;
use Modules\Setting\Entities\Department;
use Modules\Setting\Entities\Employee;
use Modules\Setting\Entities\Position;

class OrganizationController extends BaseModuleController
{
    public function __construct()
    {
        parent::__construct();
        $this->viewPath = 'setting::organization';
        $this->routePrefix = 'tenant.setting.organization';
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
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:departments,id',
            'description' => 'nullable|string',
        ]);

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
            'title_ar' => 'required|string|max:255',
            'title_en' => 'required|string|max:255',
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
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $department->update($validated);

        return $this->success('Department updated successfully.');
    }

    public function updatePosition(Request $request, $id)
    {
        $position = Position::findOrFail($id);

        $validated = $request->validate([
            'title_ar' => 'required|string|max:255',
            'title_en' => 'required|string|max:255',
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
            ->whereNull('parent_id')
            ->orderBy('level', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $chart = $this->buildOrganizationChart($departments);

        return response()->json($chart);
    }

    private function buildOrganizationChart($departments)
    {
        $chart = [];

        foreach ($departments as $department) {
            $deptNode = [
                'id' => 'dept_' . $department->id,
                'text' => $department->name,
                'type' => 'department',
                'icon' => 'fas fa-building text-primary',
                'state' => ['opened' => false],
                'children' => [],
            ];

            // خريطة المناصب حسب ID لسهولة الربط بينهم
            $positionMap = [];

            foreach ($department->positions as $position) {
                $posNode = [
                    'id' => 'pos_' . $position->id,
                    'text' => $position->title_ar,
                    'type' => 'position',
                    'icon' => 'fas fa-briefcase text-primary',
                    'state' => ['opened' => false],
                    'children' => [],
                ];

                // إضافة الموظف الحالي إن وجد
                if ($position->currentEmployee) {
                    $posNode['children'][] = [
                        'id' => 'emp_' . $position->currentEmployee->id,
                        'text' => $position->currentEmployee->name,
                        'type' => 'employee',
                        'icon' => 'fas fa-user-tie text-success',
                        'state' => ['opened' => false],
                    ];
                }

                $positionMap[$position->id] = $posNode;
            }

            // ربط المناصب مع بعضها حسب reports_to
            foreach ($department->positions as $position) {
                $posNode = $positionMap[$position->id];

                if ($position->reports_to_id && isset($positionMap[$position->reports_to_id])) {
                    $positionMap[$position->reports_to_id]['children'][] = $posNode;
                } else {
                    $deptNode['children'][] = $posNode;
                }
            }

            // معالجة الأقسام الفرعية بشكل تكراري
            if ($department->children->count() > 0) {
                $deptNode['children'] = array_merge(
                    $deptNode['children'],
                    $this->buildOrganizationChart($department->children)
                );
            }

            $chart[] = $deptNode;
        }

        return $chart;
    }

    public function showDepartment($id)
    {
        $department = Department::findOrFail($id);
        $departments = Department::whereNull('parent_id')->get();
        
        return response()->json([
            'id' => $department->id,
            'name_ar' => $department->name_ar,
            'name_en' => $department->name_en,
            'parent_id' => $department->parent_id,
            'description' => $department->description,
            'departments' => $departments
        ]);
    }

    public function showPosition($id)
    {
        $position = Position::findOrFail($id);
        $departments = Department::all();
        $positions = Position::with('department')->get();

        return response()->json([
            'id' => $position->id,
            'title_ar' => $position->title_ar,
            'title_en' => $position->title_en,
            'department_id' => $position->department_id,
            'reports_to_id' => $position->reports_to_id,
            'description' => $position->description,
            'departments' => $departments,
            'positions' => $positions
        ]);
    }

    public function showEmployee($id)
    {
        $employee = Employee::findOrFail($id);
        $positions = Position::with('department')->get();

        return response()->json([
            'id' => $employee->id,
            'name' => $employee->name,
            'email' => $employee->email,
            'phone' => $employee->phone,
            'status' => $employee->status,
            'position_id' => $employee->position_id,
            'user_id' => $employee->user_id,
            'positions' => $positions
        ]);
    }

    private function addToParentPosition(&$nodes, $parentId, $newNode)
    {
        foreach ($nodes as &$node) {
            if ($node['id'] === $parentId) {
                $node['children'][] = $newNode;
                return true;
            }
            if (!empty($node['children'])) {
                if ($this->addToParentPosition($node['children'], $parentId, $newNode)) {
                    return true;
                }
            }
        }
        return false;
    }
}
