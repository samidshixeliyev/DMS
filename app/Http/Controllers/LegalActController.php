<?php

namespace App\Http\Controllers;

use App\Models\LegalAct;
use App\Models\ActType;
use App\Models\IssuingAuthority;
use App\Models\Executor;
use App\Models\ExecutionNote;
use App\Exports\LegalActsExport;
use App\Services\LegalActWordExportService;
use Illuminate\Http\Request;

class LegalActController extends Controller
{
    public function index(Request $request)
    {
        $query = LegalAct::with(['actType', 'issuingAuthority', 'executor', 'executionNote'])
            ->active();

        if ($request->filled('legal_act_number')) {
            $query->where('legal_act_number', 'like', '%' . $request->legal_act_number . '%');
        }
        if ($request->filled('summary')) {
            $query->where('summary', 'like', '%' . $request->summary . '%');
        }
        if ($request->filled('act_type_id')) {
            $query->where('act_type_id', $request->act_type_id);
        }
        if ($request->filled('issued_by_id')) {
            $query->where('issued_by_id', $request->issued_by_id);
        }
        if ($request->filled('executor_id')) {
            $query->where('executor_id', $request->executor_id);
        }
        if ($request->filled('legal_act_date_from')) {
            $query->where('legal_act_date', '>=', $request->legal_act_date_from);
        }
        if ($request->filled('legal_act_date_to')) {
            $query->where('legal_act_date', '<=', $request->legal_act_date_to);
        }

        // Adjustable pagination
        $perPage = in_array((int) $request->input('per_page'), [10, 20, 50, 100]) 
            ? (int) $request->input('per_page') 
            : 20;

        $legalActs = $query->orderBy('id', 'desc')->paginate($perPage)->appends($request->query());

        $actTypes = ActType::active()->get();
        $issuingAuthorities = IssuingAuthority::active()->get();
        $executors = Executor::with('department')->active()->get();
        $executionNotes = ExecutionNote::active()->get();

        return view('legal_acts.index', compact(
            'legalActs', 'actTypes', 'issuingAuthorities', 'executors', 'executionNotes', 'perPage'
        ));
    }

    public function store(Request $request)
    {
        // Route middleware already checks role, but double-check
        if (!in_array(auth()->user()->user_role, ['admin', 'manager'])) {
            abort(403, 'Sizin bu əməliyyat üçün icazəniz yoxdur.');
        }

        $validated = $request->validate([
            'act_type_id' => 'required|exists:act_types,id',
            'issued_by_id' => 'required|exists:issuing_authorities,id',
            'executor_id' => 'required|exists:executors,id',
            'execution_note_id' => 'nullable|exists:execution_notes,id',
            'legal_act_number' => 'required|string|max:255|unique:legal_acts,legal_act_number',
            'legal_act_date' => 'required|date',
            'summary' => 'required|string',
            'task_number' => 'nullable|string|max:255',
            'task_description' => 'nullable|string',
            'execution_deadline' => 'nullable|date',
            'related_document_number' => 'nullable|string|max:255',
            'related_document_date' => 'nullable|date',
        ]);

        $validated['inserted_user_id'] = auth()->id();

        LegalAct::create($validated);

        return redirect()->route('legal-acts.index')->with('success', 'Hüquqi akt uğurla yaradıldı.');
    }

    public function show(LegalAct $legalAct)
    {
        $legalAct->load(['actType', 'issuingAuthority', 'executor.department', 'executionNote']);

        return response()->json([
            'id' => $legalAct->id,
            'act_type' => $legalAct->actType?->name,
            'legal_act_number' => $legalAct->legal_act_number,
            'legal_act_date' => $legalAct->legal_act_date?->format('d.m.Y'),
            'summary' => $legalAct->summary,
            'issuing_authority' => $legalAct->issuingAuthority?->name,
            'executor' => $legalAct->executor?->name,
            'executor_position' => $legalAct->executor?->position,
            'executor_department' => $legalAct->executor?->department?->name,
            'execution_note' => $legalAct->executionNote?->note,
            'task_number' => $legalAct->task_number,
            'task_description' => $legalAct->task_description,
            'execution_deadline' => $legalAct->execution_deadline?->format('d.m.Y'),
            'related_document_number' => $legalAct->related_document_number,
            'related_document_date' => $legalAct->related_document_date?->format('d.m.Y'),
            'created_at' => $legalAct->created_at?->format('d.m.Y H:i'),
        ]);
    }

    public function edit(LegalAct $legalAct)
    {
        return response()->json([
            'id' => $legalAct->id,
            'act_type_id' => $legalAct->act_type_id,
            'issued_by_id' => $legalAct->issued_by_id,
            'executor_id' => $legalAct->executor_id,
            'execution_note_id' => $legalAct->execution_note_id,
            'legal_act_number' => $legalAct->legal_act_number,
            'legal_act_date' => $legalAct->legal_act_date?->format('Y-m-d'),
            'summary' => $legalAct->summary,
            'task_number' => $legalAct->task_number,
            'task_description' => $legalAct->task_description,
            'execution_deadline' => $legalAct->execution_deadline?->format('Y-m-d'),
            'related_document_number' => $legalAct->related_document_number,
            'related_document_date' => $legalAct->related_document_date?->format('Y-m-d'),
            'act_types' => ActType::active()->get(),
            'authorities' => IssuingAuthority::active()->get(),
            'executors' => Executor::with('department')->active()->get(),
            'execution_notes' => ExecutionNote::active()->get(),
        ]);
    }

    public function update(Request $request, LegalAct $legalAct)
    {
        if (!in_array(auth()->user()->user_role, ['admin', 'manager'])) {
            abort(403, 'Sizin bu əməliyyat üçün icazəniz yoxdur.');
        }

        $validated = $request->validate([
            'act_type_id' => 'required|exists:act_types,id',
            'issued_by_id' => 'required|exists:issuing_authorities,id',
            'executor_id' => 'required|exists:executors,id',
            'execution_note_id' => 'nullable|exists:execution_notes,id',
            'legal_act_number' => 'required|string|max:255|unique:legal_acts,legal_act_number,' . $legalAct->id,
            'legal_act_date' => 'required|date',
            'summary' => 'required|string',
            'task_number' => 'nullable|string|max:255',
            'task_description' => 'nullable|string',
            'execution_deadline' => 'nullable|date',
            'related_document_number' => 'nullable|string|max:255',
            'related_document_date' => 'nullable|date',
        ]);

        $legalAct->update($validated);

        return redirect()->route('legal-acts.index')->with('success', 'Hüquqi akt uğurla yeniləndi.');
    }

    public function destroy(LegalAct $legalAct)
    {
        if (auth()->user()->user_role !== 'admin') {
            abort(403, 'Yalnız admin silə bilər.');
        }

        $legalAct->update(['is_deleted' => true]);

        return redirect()->route('legal-acts.index')->with('success', 'Hüquqi akt uğurla silindi.');
    }

    public function exportExcel(Request $request)
    {
        $query = LegalAct::with(['actType', 'issuingAuthority', 'executor', 'executionNote'])->active();

        if ($request->filled('legal_act_number')) {
            $query->where('legal_act_number', 'like', '%' . $request->legal_act_number . '%');
        }
        if ($request->filled('summary')) {
            $query->where('summary', 'like', '%' . $request->summary . '%');
        }
        if ($request->filled('act_type_id')) {
            $query->where('act_type_id', $request->act_type_id);
        }
        if ($request->filled('issued_by_id')) {
            $query->where('issued_by_id', $request->issued_by_id);
        }
        if ($request->filled('executor_id')) {
            $query->where('executor_id', $request->executor_id);
        }
        if ($request->filled('legal_act_date_from')) {
            $query->where('legal_act_date', '>=', $request->legal_act_date_from);
        }
        if ($request->filled('legal_act_date_to')) {
            $query->where('legal_act_date', '<=', $request->legal_act_date_to);
        }

        $filename = 'legal_acts_' . now()->format('Y_m_d_His') . '.xls';

        return (new LegalActsExport($query))->download($filename);
    }

    public function exportWord(Request $request)
    {
        $query = LegalAct::with(['actType', 'issuingAuthority', 'executor', 'executionNote'])->active();

        if ($request->filled('legal_act_number')) {
            $query->where('legal_act_number', 'like', '%' . $request->legal_act_number . '%');
        }
        if ($request->filled('summary')) {
            $query->where('summary', 'like', '%' . $request->summary . '%');
        }
        if ($request->filled('act_type_id')) {
            $query->where('act_type_id', $request->act_type_id);
        }
        if ($request->filled('issued_by_id')) {
            $query->where('issued_by_id', $request->issued_by_id);
        }
        if ($request->filled('executor_id')) {
            $query->where('executor_id', $request->executor_id);
        }
        if ($request->filled('legal_act_date_from')) {
            $query->where('legal_act_date', '>=', $request->legal_act_date_from);
        }
        if ($request->filled('legal_act_date_to')) {
            $query->where('legal_act_date', '<=', $request->legal_act_date_to);
        }

        $legalActs = $query->get();
        $filename = 'legal_acts_' . now()->format('Y_m_d_His') . '.doc';

        $exportService = new LegalActWordExportService();
        $filePath = $exportService->export($legalActs, $filename);

        return response()->download($filePath, $filename, [
            'Content-Type' => 'application/msword',
        ])->deleteFileAfterSend(true);
    }
}
