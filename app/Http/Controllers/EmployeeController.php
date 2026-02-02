<?php

namespace App\Http\Controllers;
use App\Models\Department;
use App\Models\Employee;

use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function all_department(){
        $department=Department::orderBy('department_name','ASC')->get()->unique('department_name');
        return response()->json([
            'department'=>$department
        ],200);
    }

    // Get all employees
    public function index(Request $request)
    {
        $search  = $request->query('search');
        $perPage = (int) $request->query('per_page', 10);

        $query = Employee::query();

        if ($search) {
            $query->where('employee_name', 'like', "%{$search}%")
                ->orWhere('position', 'like', "%{$search}%")
                ->orWhere('department_name', 'like', "%{$search}%");
        }

        return $query->paginate($perPage);
    }


    public function store(Request $request) {
        $request->validate([
            'employee_name' => 'required|string|max:255|unique:employee,employee_name',
            'position' => 'required|string|max:255',
            'department_id' => 'required|exists:departments,id',
            'department_name' => 'required|string|max:255',
        ],[
           'employee_name.unique' => 'This employee name already exists. Please enter a unique name.',
        ]);

        $employee = Employee::create($request->only('employee_name', 'position', 'department_id', 'department_name'));

        return response()->json($employee);
    }

    public function update(Request $request, $id) {
        $employee = Employee::findOrFail($id);

        $request->validate([
            'employee_name' => 'required|string|max:255|unique:employee,employee_name,' . $id,
            'position' => 'required|string|max:255',
            'department_id' => 'required|exists:departments,id',
            'department_name' => 'required|string|max:255',
        ],[
           'employee_name.unique' => 'This employee name already exists. Please enter a unique name.',
        ]);

        $employee->update($request->only('employee_name', 'position', 'department_id', 'department_name'));

        return response()->json($employee);
    }

}
