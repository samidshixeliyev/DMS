<?php

namespace App\Http\Controllers;

use App\Models\LegalAct;
use App\Models\ExecutorStatusLog;
use App\Models\ExecutionAttachment;
use App\Models\ExecutionNote;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ExecutorDashboardController extends Controller
{
    /**
     * Show executor dashboard with assigned documents.
     */
    public function index()
    {
        $user = auth()->user();
        $executorId = $user->executor_id;

        if (!$executorId) {
            abort(403, 'Sizin icraçı profiliniz yoxdur.');
        }

        $executionNotes = ExecutionNote::active()->get();

        return view('executor.index', compact('executionNotes'));
    }

    /**
     * Load assigned legal acts via DataTables AJAX.
     */
    public function load(Request $request)
    {
        $user = auth()->user();
        $executorId = $user->executor_id;

        if (!$executorId) {
            return response()->json(['draw' => 1, 'recordsTotal' => 0, 'recordsFiltered' => 0, 'data' => []]);
        }

        $draw = $request->input('draw', 1);
        $start = $request->input('start', 0);
        $length = $request->input('length', 25);

        $baseQuery = LegalAct::active()
            ->whereHas('executors', function ($q) use ($executorId) {
                $q->where('executors.id', $executorId);
            });

        $totalRecords = (clone $baseQuery)->count();

        $query = LegalAct::with([
                'actType', 'issuingAuthority', 'executors.department',
                'latestStatusLog.executionNote', 'insertedUser',
            ])
            ->active()
            ->whereHas('executors', function ($q) use ($executorId) {
                $q->where('executors.id', $executorId);
            });

        if ($request->filled('col.legal_act_number')) {
            foreach (preg_split('/\s+/', trim($request->input('col.legal_act_number'))) as $term) {
                $query->where('legal_act_number', 'like', '%' . $term . '%');
            }
        }
        if ($request->filled('col.summary')) {
            foreach (preg_split('/\s+/', trim($request->input('col.summary'))) as $term) {
                $query->where('summary', 'like', '%' . $term . '%');
            }
        }

        $filteredRecords = (clone $query)->count();

        $orderCol = (int) $request->input('order.0.column', 2);
        $orderDir = $request->input('order.0.dir', 'desc') === 'asc' ? 'asc' : 'desc';
        match ($orderCol) {
            1 => $query->orderBy('legal_act_number', $orderDir),
            2 => $query->orderBy('legal_act_date', $orderDir),
            6 => $query->orderBy('execution_deadline', $orderDir),
            default => $query->orderBy('id', 'desc'),
        };

        $results = $query->skip($start)->take($length)->get();
        $data = [];

        foreach ($results as $i => $act) {
            $latestLog = $act->latestStatusLog;
            $noteText = $latestLog?->executionNote?->note ?? '';
            $isExecuted = $noteText && mb_stripos($noteText, 'İcra olunub') !== false;
            $daysLeft = null;
            $rowClass = '';

            if ($isExecuted) {
                $rowClass = 'row-executed';
            } elseif ($act->execution_deadline) {
                $daysLeft = (int) now()->startOfDay()->diffInDays($act->execution_deadline->startOfDay(), false);
                $rowClass = $daysLeft < 0 ? 'row-overdue' : ($daysLeft <= 3 ? 'row-warning' : '');
            }

            $deadlineHtml = '-';
            if ($act->execution_deadline) {
                $deadlineHtml = $act->execution_deadline->format('d.m.Y');
                if (!$isExecuted && $daysLeft !== null) {
                    if ($daysLeft < 0) $deadlineHtml .= '<br><span class="badge bg-danger text-white mt-1">İcra müddəti bitib</span>';
                    elseif ($daysLeft <= 3) $deadlineHtml .= '<br><span class="badge bg-warning text-dark mt-1">' . $daysLeft . ' gün qalıb</span>';
                }
            }

            $statusHtml = '-';
            if ($latestLog) {
                $statusHtml = $isExecuted
                    ? '<span class="badge bg-success">' . e($noteText) . '</span>'
                    : '<span class="badge bg-secondary">' . e(Str::limit($noteText, 25)) . '</span>';
                if ($latestLog->custom_note) {
                    $statusHtml .= '<br><small class="text-muted">' . e(Str::limit($latestLog->custom_note, 30)) . '</small>';
                }
            }

            $pivot = $act->executors->where('id', $user->executor_id)->first()?->pivot;
            $roleHtml = $pivot?->role === 'main'
                ? '<span class="badge bg-primary">Əsas</span>'
                : '<span class="badge bg-info">Köməkçi</span>';

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
                'deadlineHtml' => $deadlineHtml,
                'statusHtml' => $statusHtml,
                'roleHtml' => $roleHtml,
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
     * Show legal act detail.
     */
    public function show(LegalAct $legalAct)
    {
        $user = auth()->user();
        $this->authorizeAccess($legalAct, $user);

        $legalAct->load([
            'actType', 'issuingAuthority', 'executors.department',
            'statusLogs.executionNote', 'statusLogs.user', 'statusLogs.attachments',
            'attachments.user', 'insertedUser',
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
            'related_document_number' => $legalAct->related_document_number,
            'related_document_date' => $legalAct->related_document_date?->format('d.m.Y'),
            'inserted_user' => $legalAct->insertedUser?->full_name,
            'created_at' => $legalAct->created_at?->format('d.m.Y H:i'),
            'status_logs' => $legalAct->statusLogs->map(function ($log) {
                return [
                    'id' => $log->id,
                    'user' => $log->user?->full_name,
                    'note' => $log->executionNote?->note,
                    'custom_note' => $log->custom_note,
                    'date' => $log->created_at?->format('d.m.Y H:i'),
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
     * Store a new status log with optional attachments (multiple files).
     */
    public function storeStatus(Request $request, LegalAct $legalAct)
    {
        $user = auth()->user();
        $this->authorizeAccess($legalAct, $user);

        $allowedMimes = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'image/jpeg',
            'image/png',
            'image/jpg',
        ];

        $allowedExtensions = ['doc', 'docx', 'pdf', 'jpg', 'jpeg', 'png'];

        $validated = $request->validate([
            'execution_note_id' => 'required|exists:execution_notes,id',
            'custom_note' => 'nullable|string|max:2000',
            'attachments' => 'nullable|array|max:10',
            'attachments.*' => 'file|max:10240',
        ]);

        // Validate MIME types manually for each file
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $mime = $file->getClientMimeType();
                $ext = strtolower($file->getClientOriginalExtension());

                if (!in_array($mime, $allowedMimes) && !in_array($ext, $allowedExtensions)) {
                    return back()->withErrors(['attachments' => 'Yalnız Word (.doc, .docx), PDF (.pdf) və şəkil (.jpg, .jpeg, .png) faylları qəbul olunur.'])->withInput();
                }
            }
        }

        // Check if "İcra olunub" is selected — at least one attachment is required
        $executionNote = ExecutionNote::find($validated['execution_note_id']);
        if ($executionNote && mb_stripos($executionNote->note, 'İcra olunub') !== false) {
            if (!$request->hasFile('attachments')) {
                return back()->withErrors(['attachments' => '"İcra olunub" statusu seçildikdə ən azı bir sübut sənəd yükləmək məcburidir.'])->withInput();
            }
        }

        $statusLog = ExecutorStatusLog::create([
            'legal_act_id' => $legalAct->id,
            'user_id' => $user->id,
            'execution_note_id' => $validated['execution_note_id'],
            'custom_note' => $validated['custom_note'] ?? null,
        ]);

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('execution-attachments/' . $legalAct->id, 'local');

                ExecutionAttachment::create([
                    'legal_act_id' => $legalAct->id,
                    'user_id' => $user->id,
                    'status_log_id' => $statusLog->id,
                    'file_path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getClientMimeType(),
                    'file_size' => $file->getSize(),
                ]);
            }
        }

        return redirect()->route('executor.index')->with('success', 'Status uğurla yeniləndi.');
    }

    /**
     * Download an attachment.
     */
    public function downloadAttachment(ExecutionAttachment $attachment)
    {
        $this->authorizeAttachmentAccess($attachment);

        $fullPath = $this->getAttachmentPath($attachment);

        return response()->download($fullPath, $attachment->original_name);
    }

    /**
     * Preview an attachment inline in the browser.
     * Images → served inline
     * PDF → served inline
     * DOCX/DOC → served as octet-stream for mammoth.js to fetch via arrayBuffer
     */
    public function previewAttachment(ExecutionAttachment $attachment)
    {
        $this->authorizeAttachmentAccess($attachment);

        $fullPath = $this->getAttachmentPath($attachment);
        $ext = strtolower(pathinfo($attachment->original_name, PATHINFO_EXTENSION));

        // Images — serve inline
        if (in_array($ext, ['jpg', 'jpeg', 'png'])) {
            $mimeMap = [
                'jpg'  => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'png'  => 'image/png',
            ];
            return response()->file($fullPath, [
                'Content-Type' => $mimeMap[$ext],
                'Content-Disposition' => 'inline; filename="' . $attachment->original_name . '"',
            ]);
        }

        // PDF — serve inline in browser
        if ($ext === 'pdf') {
            return response()->file($fullPath, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $attachment->original_name . '"',
            ]);
        }

        // DOCX — serve raw bytes so mammoth.js can convert on frontend
        if ($ext === 'docx') {
            return response()->file($fullPath, [
                'Content-Type' => 'application/octet-stream',
                'Content-Disposition' => 'inline; filename="' . $attachment->original_name . '"',
                'Access-Control-Allow-Origin' => '*',
            ]);
        }

        // DOC — try to convert to DOCX using PhpWord, then serve for mammoth.js
        if ($ext === 'doc') {
            try {
                $phpWord = \PhpOffice\PhpWord\IOFactory::load($fullPath, 'MsDoc');
                $tempPath = storage_path('app/private/temp_preview_' . uniqid() . '.docx');
                $writer = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
                $writer->save($tempPath);

                return response()->file($tempPath, [
                    'Content-Type' => 'application/octet-stream',
                    'Content-Disposition' => 'inline; filename="preview.docx"',
                ])->deleteFileAfterSend(true);
            } catch (\Exception $e) {
                return response()->json([
                    'error' => true,
                    'message' => '.doc faylı çevrilə bilmədi: ' . $e->getMessage(),
                ], 422);
            }
        }

        // Fallback — download
        return response()->download($fullPath, $attachment->original_name);
    }

    /**
     * Get the full filesystem path for an attachment.
     */
    private function getAttachmentPath(ExecutionAttachment $attachment): string
    {
        // Try both possible storage locations
        $paths = [
            storage_path('app/private/' . $attachment->file_path),
            storage_path('app/' . $attachment->file_path),
        ];

        foreach ($paths as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        abort(404, 'Fayl tapılmadı.');
    }

    /**
     * Authorize user access to an attachment.
     */
    private function authorizeAttachmentAccess(ExecutionAttachment $attachment): void
    {
        $user = auth()->user();
        $legalAct = $attachment->legalAct;

        if ($legalAct && $user->isExecutor()) {
            $this->authorizeAccess($legalAct, $user);
        }
    }

    /**
     * Ensure user's executor is assigned to this legal act.
     */
    private function authorizeAccess(LegalAct $legalAct, $user): void
    {
        if ($user->canManage()) {
            return;
        }

        if (!$user->executor_id) {
            abort(403, 'İcraçı profiliniz yoxdur.');
        }

        $isAssigned = $legalAct->executors()->where('executors.id', $user->executor_id)->exists();
        if (!$isAssigned) {
            abort(403, 'Bu sənədə giriş icazəniz yoxdur.');
        }
    }
}