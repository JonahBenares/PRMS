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
    public function index() {
        return response()->json(Employee::all());
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'department_id' => 'required|exists:departments,id',
            'department_name' => 'required|string',
        ]);

        Employee::create($request->only('employee_name', 'position', 'department_id', 'department_name'));

        return response()->json(['message' => 'Employee created']);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'employee_name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'department_id' => 'required|exists:departments,id',
            'department_name' => 'required|string',
        ]);

        $employee = Employee::findOrFail($id);
        $employee->update($request->only('employee_name', 'position', 'department_id', 'department_name'));

        return response()->json(['message' => 'Employee updated']);
    }
}
