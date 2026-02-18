<?php

namespace App\Http\Controllers;

use App\Models\LegalAct;
use App\Models\ActType;
use App\Models\IssuingAuthority;
use App\Models\Executor;
use App\Models\ExecutionNote;
use App\Models\ExecutorStatusLog;
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
        $canManage = auth()->user()->canManage();
        $isAdmin = auth()->user()->isAdmin();

        // Count pending approvals for badge
        $pendingApprovalCount = 0;
        if ($canManage) {
            $pendingApprovalCount = ExecutorStatusLog::pending()
                ->whereHas('executionNote', function ($q) {
                    $q->where('note', 'like', '%İcra olunub%');
                })
                ->count();
        }

        return view('legal_acts.index', compact(
            'actTypes',
            'issuingAuthorities',
            'executors',
            'executionNotes',
            'departments',
            'canManage',
            'isAdmin',
            'pendingApprovalCount'
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
            case 1: $query->orderBy('legal_act_number', $orderDir); break;
            case 2: $query->orderBy('legal_act_date', $orderDir); break;
            case 5: $query->orderBy('task_number', $orderDir); break;
            case 9: $query->orderBy('execution_deadline', $orderDir); break;
            case 11: $query->orderBy('related_document_number', $orderDir); break;
            case 12: $query->orderBy('related_document_date', $orderDir); break;
            default: $query->orderBy('id', 'desc'); break;
        }

        $results = $query->skip($start)->take($length)->get();

        $userId = auth()->id();
        $canManage = auth()->user()->canManage();
        $isAdmin = auth()->user()->isAdmin();

        $data = [];
        foreach ($results as $i => $act) {
            $latestLog = $act->latestStatusLog;
            $noteText = $latestLog?->executionNote?->note ?? '';
            $isIcraOlunub = $noteText && mb_stripos($noteText, 'İcra olunub') !== false;

            // Approval-aware status determination
            $isExecuted = $isIcraOlunub && $latestLog?->approval_status === ExecutorStatusLog::APPROVAL_APPROVED;
            $isPending = $isIcraOlunub && $latestLog?->approval_status === ExecutorStatusLog::APPROVAL_PENDING;
            $isRejected = $isIcraOlunub && $latestLog?->approval_status === ExecutorStatusLog::APPROVAL_REJECTED;

            $daysLeft = null;
            $rowClass = '';

            if ($isExecuted) {
                $rowClass = 'row-executed';
            } elseif ($isPending) {
                $rowClass = 'row-pending';
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
                if (!$isExecuted && !$isPending) {
                    if ($daysLeft !== null && $daysLeft < 0) {
                        $deadlineHtml .= '<br><span class="badge bg-danger text-white mt-1">İcra müddəti bitib</span>';
                    } elseif ($daysLeft !== null && $daysLeft <= 3) {
                        $deadlineHtml .= '<br><span class="badge bg-warning text-dark mt-1">' . $daysLeft . ' gün qalıb</span>';
                    }
                }
            }

            // Status from latest log — approval aware
            $noteHtml = '-';
            if ($latestLog) {
                if ($isExecuted) {
                    $noteHtml = '<span class="badge bg-success">İcra olunub ✓</span>';
                } elseif ($isPending) {
                    $noteHtml = '<span class="badge bg-warning text-dark">Təsdiq gözləyir</span>';
                } elseif ($isRejected) {
                    $noteHtml = '<span class="badge bg-danger">Rədd edilib</span>';
                } else {
                    $noteHtml = '<span class="badge bg-secondary">' . e(Str::limit($noteText, 25)) . '</span>';
                }
            }

            // Executors
            $mainExecutor = $act->executors->where('pivot.role', 'main')->first();
            $helperExecutor = $act->executors->where('pivot.role', 'helper')->first();
            $executorHtml = $mainExecutor ? e($mainExecutor->name) : '-';
            if ($helperExecutor) {
                $executorHtml .= '<br><small class="text-muted">Köməkçi: ' . e($helperExecutor->name) . '</small>';
            }
            $departmentName = $mainExecutor?->department?->name ?? '-';

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
                'executor' => $executorHtml,
                'department' => $departmentName,
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
            'main_executor_id' => 'required|exists:executors,id',
            'helper_executor_id' => 'nullable|exists:executors,id|different:main_executor_id',
            'legal_act_number' => 'required|string|max:255|unique:legal_acts,legal_act_number',
            'legal_act_date' => 'required|date',
            'summary' => 'required|string',
            'task_number' => 'nullable|string|max:255',
            'task_description' => 'nullable|string',
            'execution_deadline' => 'nullable|date',
            'related_document_number' => 'nullable|string|max:255',
            'related_document_date' => 'nullable|date',
        ]);

        $actData = collect($validated)->except(['main_executor_id', 'helper_executor_id'])->toArray();
        $actData['inserted_user_id'] = auth()->id();

        $legalAct = LegalAct::create($actData);

        $legalAct->executors()->attach($validated['main_executor_id'], ['role' => 'main']);

        if (!empty($validated['helper_executor_id'])) {
            $legalAct->executors()->attach($validated['helper_executor_id'], ['role' => 'helper']);
        }

        return redirect()->route('legal-acts.index')->with('success', 'Hüquqi akt uğurla yaradıldı.');
    }

    public function show(LegalAct $legalAct)
    {
        $legalAct->load([
            'actType',
            'issuingAuthority',
            'executors.department',
            'latestStatusLog.executionNote',
            'statusLogs.executionNote',
            'statusLogs.user',
            'statusLogs.attachments',
            'statusLogs.approvedByUser',
            'attachments.user',
            'insertedUser',
        ]);

        $mainExecutor = $legalAct->executors->where('pivot.role', 'main')->first();
        $helperExecutor = $legalAct->executors->where('pivot.role', 'helper')->first();

        return response()->json([
            'id' => $legalAct->id,
            'act_type' => $legalAct->actType?->name,
            'legal_act_number' => $legalAct->legal_act_number,
            'legal_act_date' => $legalAct->legal_act_date?->format('d.m.Y'),
            'summary' => $legalAct->summary,
            'issuing_authority' => $legalAct->issuingAuthority?->name,
            'main_executor' => $mainExecutor?->name,
            'main_executor_position' => $mainExecutor?->position,
            'main_executor_department' => $mainExecutor?->department?->name,
            'helper_executor' => $helperExecutor?->name,
            'helper_executor_position' => $helperExecutor?->position,
            'helper_executor_department' => $helperExecutor?->department?->name,
            'task_number' => $legalAct->task_number,
            'task_description' => $legalAct->task_description,
            'execution_deadline' => $legalAct->execution_deadline?->format('d.m.Y'),
            'related_document_number' => $legalAct->related_document_number,
            'related_document_date' => $legalAct->related_document_date?->format('d.m.Y'),
            'inserted_user' => $legalAct->insertedUser ? $legalAct->insertedUser->name . ' ' . $legalAct->insertedUser->surname : null,
            'created_at' => $legalAct->created_at?->format('d.m.Y H:i'),
            'status_logs' => $legalAct->statusLogs->map(function ($log) {
                return [
                    'user' => $log->user?->full_name,
                    'note' => $log->executionNote?->note,
                    'custom_note' => $log->custom_note,
                    'date' => $log->created_at?->format('d.m.Y H:i'),
                    'approval_status' => $log->approval_status,
                    'approval_note' => $log->approval_note,
                    'approved_by' => $log->approvedByUser?->full_name,
                    'approved_at' => $log->approved_at?->format('d.m.Y H:i'),
                    'attachments' => $log->attachments->map(fn ($a) => [
                        'id' => $a->id,
                        'name' => $a->original_name,
                        'mime_type' => $a->mime_type,
                    ]),
                ];
            }),
        ]);
    }

    public function edit(LegalAct $legalAct)
    {
        $legalAct->load('executors');

        $mainExecutorId = $legalAct->executors->where('pivot.role', 'main')->first()?->id;
        $helperExecutorId = $legalAct->executors->where('pivot.role', 'helper')->first()?->id;

        return response()->json([
            'id' => $legalAct->id,
            'act_type_id' => $legalAct->act_type_id,
            'issued_by_id' => $legalAct->issued_by_id,
            'main_executor_id' => $mainExecutorId,
            'helper_executor_id' => $helperExecutorId,
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
        ]);
    }

    public function update(Request $request, LegalAct $legalAct)
    {
        if (!auth()->user()->canManage() && auth()->id() !== $legalAct->inserted_user_id) {
            abort(403, 'Sizin bu əməliyyat üçün icazəniz yoxdur.');
        }

        $validated = $request->validate([
            'act_type_id' => 'required|exists:act_types,id',
            'issued_by_id' => 'required|exists:issuing_authorities,id',
            'main_executor_id' => 'required|exists:executors,id',
            'helper_executor_id' => 'nullable|exists:executors,id|different:main_executor_id',
            'legal_act_number' => 'required|string|max:255|unique:legal_acts,legal_act_number,' . $legalAct->id,
            'legal_act_date' => 'required|date',
            'summary' => 'required|string',
            'task_number' => 'nullable|string|max:255',
            'task_description' => 'nullable|string',
            'execution_deadline' => 'nullable|date',
            'related_document_number' => 'nullable|string|max:255',
            'related_document_date' => 'nullable|date',
        ]);

        $actData = collect($validated)->except(['main_executor_id', 'helper_executor_id'])->toArray();
        $legalAct->update($actData);

        $syncData = [
            $validated['main_executor_id'] => ['role' => 'main'],
        ];
        if (!empty($validated['helper_executor_id'])) {
            $syncData[$validated['helper_executor_id']] = ['role' => 'helper'];
        }
        $legalAct->executors()->sync($syncData);

        return redirect()->route('legal-acts.index')->with('success', 'Hüquqi akt uğurla yeniləndi.');
    }

    public function destroy(LegalAct $legalAct)
    {
        if (!auth()->user()->isAdmin()) {
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
        $query = LegalAct::with([
                'actType',
                'issuingAuthority',
                'executors.department',
                'latestStatusLog.executionNote',
                'latestStatusLog.approvedByUser',
                'insertedUser',
            ])
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
            $query->whereHas('executors', function ($q) use ($request) {
                $q->where('executors.id', $request->input('col.executor_id'));
            });
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
            $query->whereHas('executors', function ($q) use ($request) {
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
                        // Not executed = no approved "İcra olunub"
                        $q->whereDoesntHave('statusLogs')
                            ->orWhereDoesntHave('latestStatusLog', function ($sq) {
                                $sq->where('approval_status', ExecutorStatusLog::APPROVAL_APPROVED)
                                   ->whereHas('executionNote', function ($nq) {
                                       $nq->where('note', 'like', '%İcra olunub%');
                                   });
                            });
                    });
            } elseif (in_array($status, ['0day', '1day', '2days', '3days'])) {
                $days = (int) $status[0];
                $target = $today->copy()->addDays($days);
                $query->whereNotNull('execution_deadline')
                    ->whereDate('execution_deadline', '=', $target)
                    ->where(function ($q) {
                        $q->whereDoesntHave('statusLogs')
                            ->orWhereDoesntHave('latestStatusLog', function ($sq) {
                                $sq->where('approval_status', ExecutorStatusLog::APPROVAL_APPROVED)
                                   ->whereHas('executionNote', function ($nq) {
                                       $nq->where('note', 'like', '%İcra olunub%');
                                   });
                            });
                    });
            } elseif ($status === 'executed') {
                // Only approved "İcra olunub"
                $query->whereHas('latestStatusLog', function ($q) {
                    $q->where('approval_status', ExecutorStatusLog::APPROVAL_APPROVED)
                      ->whereHas('executionNote', function ($nq) {
                          $nq->where('note', 'like', '%İcra olunub%');
                      });
                });
            } elseif ($status === 'pending') {
                // Pending approval
                $query->whereHas('latestStatusLog', function ($q) {
                    $q->where('approval_status', ExecutorStatusLog::APPROVAL_PENDING)
                      ->whereHas('executionNote', function ($nq) {
                          $nq->where('note', 'like', '%İcra olunub%');
                      });
                });
            }
        }

        if ($request->filled('col.execution_note_id')) {
            $query->whereHas('latestStatusLog', function ($q) use ($request) {
                $q->where('execution_note_id', $request->input('col.execution_note_id'));
            });
        }

        return $query;
    }
}
