<?php

namespace App\Http\Controllers;

use App\Models\LegalAct;
use App\Models\ExecutorStatusLog;
use App\Models\ExecutionNote;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ApprovalController extends Controller
{
    /**
     * Get IDs of all execution notes that represent "İcra olunub".
     * This avoids LIKE queries on SQL Server which may have collation issues with İ.
     */
    private function getIcraOlunubNoteIds(): array
    {
        return ExecutionNote::all()
            ->filter(function ($note) {
                return mb_stripos($note->note, 'İcra olunub') !== false
                    || mb_stripos($note->note, 'icra olunub') !== false
                    || mb_stripos($note->note, 'ICRA OLUNUB') !== false;
            })
            ->pluck('id')
            ->toArray();
    }

    /**
     * List all legal acts with pending approval.
     */
    public function index()
    {
        return view('approvals.index');
    }

    /**
     * Load pending approvals via DataTables AJAX.
     */
    public function load(Request $request)
    {
        $draw = $request->input('draw', 1);
        $start = $request->input('start', 0);
        $length = $request->input('length', 25);

        $icraOlunubIds = $this->getIcraOlunubNoteIds();

        if (empty($icraOlunubIds)) {
            return response()->json([
                'draw' => (int) $draw,
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
            ]);
        }

        // Find ALL pending status logs (not just latest — the pending one might not be the very latest)
        $pendingLogs = ExecutorStatusLog::with([
                'legalAct.actType', 'legalAct.issuingAuthority',
                'legalAct.executors.department', 'legalAct.insertedUser',
                'user', 'executionNote', 'attachments',
            ])
            ->where('approval_status', 'pending')
            ->whereIn('execution_note_id', $icraOlunubIds)
            ->whereHas('legalAct', function ($q) {
                $q->where('is_deleted', false);
            })
            ->orderBy('created_at', 'desc');

        $totalRecords = (clone $pendingLogs)->count();

        // Apply filters
        if ($request->filled('col.legal_act_number')) {
            $term = trim($request->input('col.legal_act_number'));
            $pendingLogs->whereHas('legalAct', function ($q) use ($term) {
                $q->where('legal_act_number', 'like', '%' . $term . '%');
            });
        }

        $filteredRecords = (clone $pendingLogs)->count();
        $results = $pendingLogs->skip($start)->take($length)->get();

        $data = [];
        foreach ($results as $i => $log) {
            $act = $log->legalAct;
            if (!$act) continue;

            $mainExecutor = $act->executors->where('pivot.role', 'main')->first();
            $executorHtml = $mainExecutor ? e($mainExecutor->name) : '-';
            if ($mainExecutor?->department) {
                $executorHtml .= '<br><small class="text-muted">' . e($mainExecutor->department->name) . '</small>';
            }

            $attachmentCount = $log->attachments->count();

            $data[] = [
                'id' => $act->id,
                'logId' => $log->id,
                'rowNum' => $start + $i + 1,
                'actType' => $act->actType?->name ?? '-',
                'legalActNumber' => $act->legal_act_number ?? '-',
                'legalActDate' => $act->legal_act_date?->format('d.m.Y') ?? '-',
                'summary' => Str::limit($act->summary, 60) ?? '-',
                'executor' => $executorHtml,
                'submittedBy' => $log->user?->full_name ?? '-',
                'submittedAt' => $log->created_at?->format('d.m.Y H:i') ?? '-',
                'customNote' => $log->custom_note ? Str::limit($log->custom_note, 40) : '-',
                'attachmentCount' => $attachmentCount,
                'deadlineHtml' => $act->execution_deadline ? $act->execution_deadline->format('d.m.Y') : '-',
            ];
        }

        return response()->json([
            'draw' => (int) $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data,
        ]);
    }

    /**
     * Show details of a pending approval (legal act + status log).
     */
    public function show(LegalAct $legalAct)
    {
        $legalAct->load([
            'actType', 'issuingAuthority', 'executors.department',
            'statusLogs.executionNote', 'statusLogs.user', 'statusLogs.attachments',
            'statusLogs.approvedByUser', 'insertedUser',
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
            'main_executor_department' => $mainExecutor?->department?->name,
            'helper_executor' => $helperExecutor?->name,
            'helper_executor_department' => $helperExecutor?->department?->name,
            'task_number' => $legalAct->task_number,
            'task_description' => $legalAct->task_description,
            'execution_deadline' => $legalAct->execution_deadline?->format('d.m.Y'),
            'status_logs' => $legalAct->statusLogs->map(function ($log) {
                return [
                    'id' => $log->id,
                    'user' => $log->user?->full_name,
                    'note' => $log->executionNote?->note,
                    'custom_note' => $log->custom_note,
                    'date' => $log->created_at?->format('d.m.Y H:i'),
                    'approval_status' => $log->approval_status,
                    'approval_note' => $log->approval_note,
                    'approved_by' => $log->approvedByUser?->full_name,
                    'approved_at' => $log->approved_at?->format('d.m.Y H:i'),
                    'attachments' => $log->attachments->map(function ($att) {
                        return [
                            'id' => $att->id,
                            'name' => $att->original_name,
                            'size' => round($att->file_size / 1024, 1) . ' KB',
                            'mime_type' => $att->mime_type,
                        ];
                    }),
                ];
            }),
        ]);
    }

    /**
     * Approve a pending "İcra olunub" status log.
     */
    public function approve(Request $request, ExecutorStatusLog $statusLog)
    {
        if ($statusLog->approval_status !== 'pending') {
            return back()->withErrors(['general' => 'Bu qeyd artıq işlənib.']);
        }

        $validated = $request->validate([
            'approval_note' => 'nullable|string|max:2000',
        ]);

        $statusLog->update([
            'approval_status' => 'approved',
            'approved_by' => auth()->id(),
            'approval_note' => $validated['approval_note'] ?? null,
            'approved_at' => now(),
        ]);

        return redirect()->route('approvals.index')
            ->with('success', 'İcra qeydi təsdiqləndi. Sənəd "İcra olunub" statusuna keçdi.');
    }

    /**
     * Reject a pending "İcra olunub" status log.
     */
    public function reject(Request $request, ExecutorStatusLog $statusLog)
    {
        if ($statusLog->approval_status !== 'pending') {
            return back()->withErrors(['general' => 'Bu qeyd artıq işlənib.']);
        }

        $validated = $request->validate([
            'approval_note' => 'required|string|max:2000',
        ]);

        $statusLog->update([
            'approval_status' => 'rejected',
            'approved_by' => auth()->id(),
            'approval_note' => $validated['approval_note'],
            'approved_at' => now(),
        ]);

        return redirect()->route('approvals.index')
            ->with('success', 'İcra qeydi rədd edildi. İcraçı yenidən status təyin edə bilər.');
    }
}
