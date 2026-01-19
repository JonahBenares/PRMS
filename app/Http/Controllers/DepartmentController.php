<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    // Get all departments
    public function index() {
        return response()->json(Department::all());
    }

    // Add new department
    public function store(Request $request) {
        $request->validate([
            'department_name' => 'required|string|max:255|unique:departments,department_name',
            'department_code' => 'required|string|max:10|unique:departments,department_code',
        ]);

        $dept = Department::create($request->only('department_name', 'department_code'));
        return response()->json($dept);
    }

    // Update department
    public function update(Request $request, $id) {
        $dept = Department::findOrFail($id);

        $request->validate([
            'department_name' => 'required|string|max:255|unique:departments,department_name,' . $id,
            'department_code' => 'required|string|max:10|unique:departments,department_code,' . $id,
        ]);

        $dept->update($request->only('department_name', 'department_code'));
        return response()->json($dept);
    }

    // Delete department
    public function destroy($id) {
        $dept = Department::findOrFail($id);
        $dept->delete();
        return response()->json(['message' => 'Department deleted']);
    }
}


