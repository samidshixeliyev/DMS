<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::active()->paginate(20);
        return view('departments.index', compact('departments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate(['name' => 'required|string|max:255']);
        Department::create($validated);
        return redirect()->route('departments.index')->with('success', 'Department created successfully.');
    }

    public function show(Department $department)
    {
        return response()->json([
            'id' => $department->id,
            'name' => $department->name,
            'created_at' => $department->created_at?->format('d.m.Y H:i'),
        ]);
    }

    public function edit(Department $department)
    {
        return response()->json([
            'id' => $department->id,
            'name' => $department->name,
        ]);
    }

    public function update(Request $request, Department $department)
    {
        $validated = $request->validate(['name' => 'required|string|max:255']);
        $department->update($validated);
        return redirect()->route('departments.index')->with('success', 'Department updated successfully.');
    }

    public function destroy(Department $department)
    {
        $department->update(['is_deleted' => true]);
        return redirect()->route('departments.index')->with('success', 'Department deleted successfully.');
    }
}