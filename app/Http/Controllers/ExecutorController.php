<?php

namespace App\Http\Controllers;

use App\Models\Executor;
use App\Models\Department;
use Illuminate\Http\Request;

class ExecutorController extends Controller
{
    public function index()
    {
        $executors = Executor::with('department')->active()->paginate(20);
        return view('executors.index', compact('executors'));
    }

    public function create()
    {
        $departments = Department::active()->get();
        return view('executors.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'department_id' => 'required|exists:departments,id',
        ]);

        Executor::create($validated);

        return redirect()->route('executors.index')->with('success', 'Executor created successfully.');
    }

    public function edit(Executor $executor)
    {
        $departments = Department::active()->get();
        return view('executors.edit', compact('executor', 'departments'));
    }

    public function update(Request $request, Executor $executor)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'department_id' => 'required|exists:departments,id',
        ]);

        $executor->update($validated);

        return redirect()->route('executors.index')->with('success', 'Executor updated successfully.');
    }

    public function destroy(Executor $executor)
    {
        $executor->update(['is_deleted' => true]);

        return redirect()->route('executors.index')->with('success', 'Executor deleted successfully.');
    }
}
