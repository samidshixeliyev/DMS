<?php

namespace App\Http\Controllers;

use App\Models\ActType;
use Illuminate\Http\Request;

class ActTypeController extends Controller
{
    public function index()
    {
        $actTypes = ActType::active()->paginate(20);
        return view('act_types.index', compact('actTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        ActType::create($validated);

        return redirect()->route('act-types.index')->with('success', 'Act Type created successfully.');
    }

    public function show(ActType $actType)
    {
        return response()->json([
            'id' => $actType->id,
            'name' => $actType->name,
            'created_at' => $actType->created_at?->format('d.m.Y H:i'),
        ]);
    }

    public function edit(ActType $actType)
    {
        return response()->json([
            'id' => $actType->id,
            'name' => $actType->name,
        ]);
    }

    public function update(Request $request, ActType $actType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $actType->update($validated);

        return redirect()->route('act-types.index')->with('success', 'Act Type updated successfully.');
    }

    public function destroy(ActType $actType)
    {
        $actType->update(['is_deleted' => true]);

        return redirect()->route('act-types.index')->with('success', 'Act Type deleted successfully.');
    }
}