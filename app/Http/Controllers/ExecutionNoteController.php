<?php

namespace App\Http\Controllers;

use App\Models\ExecutionNote;
use Illuminate\Http\Request;

class ExecutionNoteController extends Controller
{
    public function index()
    {
        $executionNotes = ExecutionNote::active()->paginate(20);
        return view('execution_notes.index', compact('executionNotes'));
    }

    public function create()
    {
        return view('execution_notes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'note' => 'required|string',
        ]);

        ExecutionNote::create($validated);

        return redirect()->route('execution-notes.index')->with('success', 'Execution Note created successfully.');
    }

    public function edit(ExecutionNote $executionNote)
    {
        return view('execution_notes.edit', compact('executionNote'));
    }

    public function update(Request $request, ExecutionNote $executionNote)
    {
        $validated = $request->validate([
            'note' => 'required|string',
        ]);

        $executionNote->update($validated);

        return redirect()->route('execution-notes.index')->with('success', 'Execution Note updated successfully.');
    }

    public function destroy(ExecutionNote $executionNote)
    {
        $executionNote->update(['is_deleted' => true]);

        return redirect()->route('execution-notes.index')->with('success', 'Execution Note deleted successfully.');
    }
}