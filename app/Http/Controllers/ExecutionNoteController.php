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

    public function store(Request $request)
    {
        $validated = $request->validate([
            'note' => 'required|string',
        ]);

        ExecutionNote::create($validated);

        return redirect()->route('execution-notes.index')->with('success', 'Execution Note created successfully.');
    }

    public function show(ExecutionNote $executionNote)
    {
        return response()->json([
            'id' => $executionNote->id,
            'note' => $executionNote->note,
            'created_at' => $executionNote->created_at?->format('d.m.Y H:i'),
        ]);
    }

    public function edit(ExecutionNote $executionNote)
    {
        return response()->json([
            'id' => $executionNote->id,
            'note' => $executionNote->note,
        ]);
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