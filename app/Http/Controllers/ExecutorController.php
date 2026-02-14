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
        $departments = Department::active()->get();
        return view('executors.index', compact('executors', 'departments'));
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

    public function show(Executor $executor)
    {
        $executor->load('department');
        return response()->json([
            'id' => $executor->id,
            'name' => $executor->name,
            'position' => $executor->position,
            'department' => $executor->department?->name,
            'created_at' => $executor->created_at?->format('d.m.Y H:i'),
        ]);
    }

    public function edit(Executor $executor)
    {
        $departments = Department::active()->get();
        return response()->json([
            'id' => $executor->id,
            'name' => $executor->name,
            'position' => $executor->position,
            'department_id' => $executor->department_id,
            'departments' => $departments,
        ]);
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