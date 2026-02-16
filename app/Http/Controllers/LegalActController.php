<?php

namespace App\Http\Controllers;

use App\Models\LegalAct;
use App\Models\ActType;
use App\Models\IssuingAuthority;
use App\Models\Executor;
use App\Models\ExecutionNote;
use App\Models\Department;
use App\Exports\LegalActsExport;
use App\Services\LegalActWordExportService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LegalActController extends Controller
{
    public function index()
    {
        $actTypes = ActType::active()->get();
        $issuingAuthorities = IssuingAuthority::active()->get();
        $executors = Executor::with('department')->active()->get();
        $executionNotes = ExecutionNote::active()->get();
        $departments = Department::active()->get();
        $canManage = in_array(auth()->user()->user_role, ['admin', 'manager']);
        $isAdmin = auth()->user()->user_role === 'admin';

        return view('legal_acts.index', compact(
            'actTypes',
            'issuingAuthorities',
            'executors',
            'executionNotes',
            'departments',
            'canManage',
            'isAdmin'
        ));
    }

    public function load(Request $request)
    {
        $draw = $request->input('draw', 1);
        $start = $request->input('start', 0);
        $length = $request->input('length', 25);

        $totalQuery = LegalAct::active();
        $totalRecords = (clone $totalQuery)->count();

        $query = $this->applyFilters($request);
        $filteredRecords = (clone $query)->count();

        $orderCol = (int) $request->input('order.0.column', 3);
        $orderDir = $request->input('order.0.dir', 'desc') === 'asc' ? 'asc' : 'desc';

        switch ($orderCol) {
            case 1:
                $query->orderBy('legal_act_number', $orderDir);
                break;
            case 2:
                $query->orderBy('legal_act_date', $orderDir);
                break;
            case 5:
                $query->orderBy('task_number', $orderDir);
                break;
            case 9:
                $query->orderBy('execution_deadline', $orderDir);
                break;
            case 11:
                $query->orderBy('related_document_number', $orderDir);
                break;
            case 12:
                $query->orderBy('related_document_date', $orderDir);
                break;
            default:
                $query->orderBy('id', 'desc');
                break;
        }

        $results = $query->skip($start)->take($length)->get();

        $userId = auth()->id();
        $userRole = auth()->user()->user_role;
        $canManage = in_array($userRole, ['admin', 'manager']);
        $isAdmin = $userRole === 'admin';

        $data = [];
        foreach ($results as $i => $act) {
            $noteText = $act->executionNote?->note ?? '';
            $isExecuted = $noteText && mb_stripos($noteText, 'İcra olunub') !== false;
            $daysLeft = null;
            $rowClass = '';

            if ($isExecuted) {
                $rowClass = 'row-executed';
            } elseif ($act->execution_deadline) {
                $daysLeft = (int) now()->startOfDay()->diffInDays($act->execution_deadline->startOfDay(), false);
                if ($daysLeft < 0) {
                    $rowClass = 'row-overdue';
                } elseif ($daysLeft <= 3) {
                    $rowClass = 'row-warning';
                }
            }

            $deadlineHtml = '-';
            if ($act->execution_deadline) {
                $deadlineHtml = $act->execution_deadline->format('d.m.Y');
                if (!$isExecuted) {
                    if ($daysLeft !== null && $daysLeft < 0) {
                        $deadlineHtml .= '<br><span class="badge bg-danger text-white mt-1">İcra müddəti bitib</span>';
                    } elseif ($daysLeft !== null && $daysLeft <= 3) {
                        $deadlineHtml .= '<br><span class="badge bg-warning text-dark mt-1">' . $daysLeft . ' gün qalıb</span>';
                    }
                }
            }

            $noteHtml = '-';
            if ($act->executionNote) {
                if ($isExecuted) {
                    $noteHtml = '<span class="badge bg-success">' . e($noteText) . '</span>';
                } else {
                    $noteHtml = '<span class="badge bg-secondary">' . e(Str::limit($noteText, 25)) . '</span>';
                }
            }

            $data[] = [
                'DT_RowClass' => $rowClass,
                'id' => $act->id,
                'rowNum' => $start + $i + 1,
                'actType' => $act->actType?->name ?? '-',
                'legalActNumber' => $act->legal_act_number ?? '-',
                'legalActDate' => $act->legal_act_date?->format('d.m.Y') ?? '-',
                'issuingAuthority' => $act->issuingAuthority?->name ?? '-',
                'summary' => Str::limit($act->summary, 80) ?? '-',
                'taskNumber' => $act->task_number ?? '-',
                'taskDescription' => Str::limit($act->task_description, 60) ?: '-',
                'executor' => $act->executor?->name ?? '-',
                'department' => $act->executor?->department?->name ?? '-',
                'deadlineHtml' => $deadlineHtml,
                'noteHtml' => $noteHtml,
                'relatedDocNumber' => $act->related_document_number ?? '-',
                'relatedDocDate' => $act->related_document_date?->format('d.m.Y') ?? '-',
                'insertedUser' => $act->insertedUser ? $act->insertedUser->name . ' ' . $act->insertedUser->surname : '-',
                'canEdit' => ($userId === $act->inserted_user_id) || $canManage,
                'canDelete' => $isAdmin,
            ];
        }

        return response()->json([
            'draw' => (int) $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data,
        ]);
    }

    public function store(Request $request)
    {
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
        $legalAct->load(['actType', 'issuingAuthority', 'executor.department', 'executionNote', 'insertedUser']);

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
            'inserted_user' => $legalAct->insertedUser ? $legalAct->insertedUser->name . ' ' . $legalAct->insertedUser->surname : null,
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
        if (!in_array(auth()->user()->user_role, ['admin', 'manager']) && auth()->id() !== $legalAct->inserted_user_id) {
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
        $query = $this->applyFilters($request);
        $filename = 'legal_acts_' . now()->format('Y_m_d_His') . '.xls';
        return (new LegalActsExport($query))->download($filename);
    }

    public function exportWord(Request $request)
    {
        $query = $this->applyFilters($request);
        $legalActs = $query->get();
        $filename = 'legal_acts_' . now()->format('Y_m_d_His') . '.doc';

        $exportService = new LegalActWordExportService();
        $filePath = $exportService->export($legalActs, $filename);

        return response()->download($filePath, $filename, [
            'Content-Type' => 'application/msword',
        ])->deleteFileAfterSend(true);
    }

    private function applyFilters(Request $request)
    {
        $query = LegalAct::with(['actType', 'issuingAuthority', 'executor.department', 'executionNote', 'insertedUser'])
            ->active();

        if ($request->filled('col.legal_act_number')) {
            $terms = preg_split('/\s+/', trim($request->input('col.legal_act_number')));
            foreach ($terms as $term) {
                $query->where('legal_act_number', 'like', '%' . $term . '%');
            }
        }

        if ($request->filled('col.summary')) {
            $terms = preg_split('/\s+/', trim($request->input('col.summary')));
            foreach ($terms as $term) {
                $query->where('summary', 'like', '%' . $term . '%');
            }
        }

        if ($request->filled('col.act_type_id')) {
            $query->where('act_type_id', $request->input('col.act_type_id'));
        }

        if ($request->filled('col.issued_by_id')) {
            $query->where('issued_by_id', $request->input('col.issued_by_id'));
        }

        if ($request->filled('col.executor_id')) {
            $query->where('executor_id', $request->input('col.executor_id'));
        }

        if ($request->filled('col.execution_note_id')) {
            $query->where('execution_note_id', $request->input('col.execution_note_id'));
        }

        if ($request->filled('col.legal_act_date_from')) {
            $query->where('legal_act_date', '>=', $request->input('col.legal_act_date_from'));
        }

        if ($request->filled('col.legal_act_date_to')) {
            $query->where('legal_act_date', '<=', $request->input('col.legal_act_date_to'));
        }

        if ($request->filled('col.deadline_from')) {
            $query->where('execution_deadline', '>=', $request->input('col.deadline_from'));
        }

        if ($request->filled('col.deadline_to')) {
            $query->where('execution_deadline', '<=', $request->input('col.deadline_to'));
        }

        if ($request->filled('col.task_number')) {
            $terms = preg_split('/\s+/', trim($request->input('col.task_number')));
            foreach ($terms as $term) {
                $query->where('task_number', 'like', '%' . $term . '%');
            }
        }

        if ($request->filled('col.department_id')) {
            $query->whereHas('executor', function ($q) use ($request) {
                $q->where('department_id', $request->input('col.department_id'));
            });
        }

        if ($request->filled('col.deadline_status')) {
            $status = $request->input('col.deadline_status');
            $today = now()->startOfDay();

            if ($status === 'expired') {
                $query->whereNotNull('execution_deadline')
                    ->where('execution_deadline', '<', $today)
                    ->where(function ($q) {
                        $q->whereNull('execution_note_id')
                            ->orWhereHas('executionNote', function ($sq) {
                                $sq->where('note', 'not like', '%İcra olunub%');
                            });
                    });
            } elseif (in_array($status, ['0day', '1day', '2days', '3days'])) {
                $days = (int) $status[0];
                $target = $today->copy()->addDays($days);
                $query->whereNotNull('execution_deadline')
                    ->whereDate('execution_deadline', '=', $target)
                    ->where(function ($q) {
                        $q->whereNull('execution_note_id')
                            ->orWhereHas('executionNote', function ($sq) {
                                $sq->where('note', 'not like', '%İcra olunub%');
                            });
                    });
            } elseif ($status === 'executed') {
                $query->whereHas('executionNote', function ($q) {
                    $q->where('note', 'like', '%İcra olunub%');
                });
            }
        }

        return $query;
    }
}