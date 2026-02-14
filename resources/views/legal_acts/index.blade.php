@extends('layouts.app')

@section('title', 'Hüquqi Aktlar')

@push('styles')
    <style>
        /* ── Row status colors ── */
        .row-overdue {
            background-color: #ff4444 !important;
            color: #fff;
        }
        .row-overdue:hover {
            background-color: #ff3333 !important;
            color: #fff;
        }
        .row-overdue small{
            background-color: red;
        }
        .row-overdue td {
            background-color: rgba(255, 152, 152, 0.8) !important;
        }
        /* .row-overdue .badge {
            background: rgba(255, 255, 255, 0.25) !important;
            color: #fff !important;
        } */
        .row-warning {
            background-color: #ffff00 !important;
        }
        .row-warning:hover {
            background-color: #eeee00 !important;
        }
        .row-executed {
            background-color: #d4edda !important;
        }
        .row-executed:hover {
            background-color: #c3e6cb !important;
        }

        /* ── Filter row layout ── */
        .filter-row .select2-container {
            width: 100% !important;
        }
        .filter-row .flatpickr-input {
            background: #fff !important;
            cursor: pointer;
        }

        /* ── ALL thead cells base ── */
        #legalActsTable thead th,
        .dataTables_scrollHead thead th {
            text-align: center !important;
            vertical-align: middle !important;
            font-weight: 700;
            white-space: nowrap;
            color: #fff !important;
            border: 1px solid rgba(255,255,255,0.18) !important;
        }

        /* ── Band (top) row ── */
        #legalActsTable thead tr.band-header th,
        .dataTables_scrollHead thead tr.band-header th {
            font-size: 0.82rem;
            padding: 0.6rem 0.6rem;
            letter-spacing: 0.3px;
        }

        /* ── Sub (bottom) row ── */
        #legalActsTable thead tr.sub-header th,
        .dataTables_scrollHead thead tr.sub-header th {
            font-size: 0.74rem;
            font-weight: 600;
            padding: 0.5rem 0.45rem;
        }

        /* ── Color classes — work everywhere including DT cloned headers ── */
        th.bg-band-index    { background: #374151 !important; }
        th.bg-band-doc      { background: #1e3a5f !important; }
        th.bg-band-doc-sub  { background: #2a5298 !important; }
        th.bg-band-task     { background: #065f46 !important; }
        th.bg-band-task-sub { background: #10a37f !important; }
        th.bg-band-exec     { background: #5b21b6 !important; }
        th.bg-band-exec-sub { background: #7c4ddb !important; }
        th.bg-band-icra     { background: #92400e !important; }
        th.bg-band-icra-sub { background: #d97706 !important; }
        th.bg-band-actions  { background: #374151 !important; }

        /* ── Table wide + body ── */
        #legalActsTable {
            min-width: 2100px;
        }
        #legalActsTable tbody td {
            font-size: 0.82rem;
            padding: 0.5rem 0.65rem;
            vertical-align: middle;
            text-align: center;
        }
        #legalActsTable tbody td.wrap-cell {
            white-space: normal;
            word-break: break-word;
            text-align: left;
            min-width: 180px;
            max-width: 280px;
        }

        /* ── Zebra striping for readability ── */
        #legalActsTable tbody tr:nth-child(even):not([class*="row-"]) {
            background-color: #f8fafc;
        }

        /* ── DataTables overrides ── */
        #legalActsTable_wrapper .dt-buttons {
            gap: 0.35rem;
            display: flex;
        }
        div.dataTables_wrapper div.dataTables_length select {
            min-width: 60px;
        }

        /* ── Action & # columns ── */
        #legalActsTable td:last-child,
        #legalActsTable th:last-child {
            white-space: nowrap;
            min-width: 130px;
        }
        #legalActsTable td:first-child,
        #legalActsTable th:first-child {
            white-space: nowrap;
            min-width: 50px;
        }

        /* ── Badge cells should be centered ── */
        #legalActsTable tbody td .badge {
            display: inline-block;
            vertical-align: middle;
        }
    </style>
@endpush

@section('content')
    <div class="page-header">
        <h2><i class="bi bi-file-text me-2"></i>Hüquqi Aktlar</h2>
        <div class="d-flex gap-2 flex-wrap">
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createModal">
                <i class="bi bi-plus-circle me-1"></i> Yeni əlavə et
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Filters --}}
    <div class="card filter-card mb-3">
        <div class="card-header" data-bs-toggle="collapse" data-bs-target="#filterBody">
            <h5 class="mb-0 d-flex align-items-center">
                <i class="bi bi-funnel me-2"></i> Filtrlər
                <i class="bi bi-chevron-down ms-auto"></i>
            </h5>
        </div>
        <div class="collapse show" id="filterBody">
            <div class="card-body filter-row">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Sənədin nömrəsi</label>
                        <input type="text" id="filter_legal_act_number" class="form-control filter-el"
                            value="{{ request('legal_act_number') }}" placeholder="Axtar...">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Qısa məzmun</label>
                        <input type="text" id="filter_summary" class="form-control filter-el"
                            value="{{ request('summary') }}" placeholder="Axtar...">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Sənədin növü</label>
                        <select id="filter_act_type" class="form-select filter-select">
                            <option value="">Hamısı</option>
                            @foreach($actTypes as $type)
                                <option value="{{ Str::lower($type->name) }}" {{ request('act_type_id') == $type->id ? 'selected' : '' }}>
                                    {{ $type->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Kim qəbul edib</label>
                        <select id="filter_issued_by" class="form-select filter-select">
                            <option value="">Hamısı</option>
                            @foreach($issuingAuthorities as $authority)
                                <option value="{{ Str::lower($authority->name) }}" {{ request('issued_by_id') == $authority->id ? 'selected' : '' }}>
                                    {{ $authority->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">İcraçı</label>
                        <select id="filter_executor" class="form-select filter-select">
                            <option value="">Hamısı</option>
                            @foreach($executors as $executor)
                                <option value="{{ Str::lower($executor->name) }}" {{ request('executor_id') == $executor->id ? 'selected' : '' }}>
                                    {{ $executor->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Sənəd tarixi</label>
                        <input type="text" id="filter_date_range" class="form-control filter-el"
                            placeholder="Tarix aralığı seçin..." readonly>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">İcra müddəti</label>
                        <input type="text" id="filter_deadline_range" class="form-control filter-el"
                            placeholder="Tarix aralığı seçin..." readonly>
                    </div>
                    <div class="col-md-3 d-flex align-items-end gap-2">
                        <button type="button" id="filtersSearchBtn" class="btn btn-primary">
                            <i class="bi bi-search me-1"></i> Axtar
                        </button>
                        <button type="button" id="filtersResetBtn" class="btn btn-secondary">
                            <i class="bi bi-x-circle me-1"></i> Sıfırla
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Data Table --}}
    <div class="card">
        <div class="card-body p-0">
            <div style="overflow-x:auto;">
                <table class="table table-hover table-bordered mb-0" id="legalActsTable" style="width:100%">
                    <thead>
                        <tr class="band-header">
                            <th rowspan="2" class="bg-band-index">#</th>
                            <th colspan="5" class="bg-band-doc">Sənəd Məlumatları</th>
                            <th colspan="2" class="bg-band-task">Tapşırıq Məlumatları</th>
                            <th colspan="4" class="bg-band-exec">İcraçı Məlumatları</th>
                            <th colspan="3" class="bg-band-icra">İcra Məlumatları</th>
                            <th rowspan="2" class="bg-band-actions">Əməliyyatlar</th>
                        </tr>
                        <tr class="sub-header">
                            <th class="bg-band-doc-sub">Sənədin<br>Növü</th>
                            <th class="bg-band-doc-sub">Sənədin<br>Nömrəsi</th>
                            <th class="bg-band-doc-sub">Sənədin<br>Tarixi</th>
                            <th class="bg-band-doc-sub">Kim Qəbul<br>Edib</th>
                            <th class="bg-band-doc-sub">Qısa<br>Məzmunu</th>
                            <th class="bg-band-task-sub">Tapşırığın<br>Nömrəsi</th>
                            <th class="bg-band-task-sub">Tapşırıq</th>
                            <th class="bg-band-exec-sub">İcraçı</th>
                            <th class="bg-band-exec-sub">Bölməsi</th>
                            <th class="bg-band-exec-sub">İcra<br>Müddəti</th>
                            <th class="bg-band-exec-sub">Qeyd</th>
                            <th class="bg-band-icra-sub">Sənədin<br>Nömrəsi</th>
                            <th class="bg-band-icra-sub">Sənədin<br>Tarixi</th>
                            <th class="bg-band-icra-sub">Daxil<br>Edən</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($legalActs as $index => $legalAct)
                            @php
                                $rowClass = '';
                                $daysLeft = null;
                                $noteText = $legalAct->executionNote?->note;
                                $isExecuted = $noteText && mb_stripos($noteText, 'İcra olunub') !== false;

                                if ($isExecuted) {
                                    $rowClass = 'row-executed';
                                } elseif ($legalAct->execution_deadline) {
                                    $daysLeft = (int) now()->startOfDay()->diffInDays($legalAct->execution_deadline->startOfDay(), false);
                                    if ($daysLeft < 0) {
                                        $rowClass = 'row-overdue';
                                    } elseif ($daysLeft <= 3) {
                                        $rowClass = 'row-warning';
                                    }
                                }
                                $isCreator = auth()->id() === $legalAct->inserted_user_id;
                            @endphp
                            <tr class="{{ $rowClass }}">
                                <td class="text-center"><span class="badge bg-secondary">{{ $loop->iteration }}</span></td>
                                <td class="text-center">
                                    @if($legalAct->actType)
                                        <span class="badge"
                                            style="background: var(--accent-dark)">{{ $legalAct->actType->name }}</span>
                                    @else - @endif
                                </td>
                                <td class="fw-semibold text-center">{{ $legalAct->legal_act_number }}</td>
                                <td class="text-center" data-order="{{ $legalAct->legal_act_date?->format('Y-m-d') }}">
                                    {{ $legalAct->legal_act_date?->format('d.m.Y') }}
                                </td>
                                <td>{{ $legalAct->issuingAuthority?->name ?? '-' }}</td>
                                <td class="wrap-cell">{{ Str::limit($legalAct->summary, 80) }}</td>
                                <td class="text-center">{{ $legalAct->task_number ?? '-' }}</td>
                                <td class="wrap-cell">{{ Str::limit($legalAct->task_description, 60) ?: '-' }}</td>
                                <td>{{ $legalAct->executor?->name ?? '-' }}</td>
                                <td>{{ $legalAct->executor?->department?->name ?? '-' }}</td>
                                <td class="text-center" data-order="{{ $legalAct->execution_deadline?->format('Y-m-d') }}">
                                    @if($legalAct->execution_deadline)
                                        {{ $legalAct->execution_deadline->format('d.m.Y') }}
                                        @if(!$isExecuted)
                                            @if($daysLeft !== null && $daysLeft < 0)
                                                <br><span class="badge bg-danger text-white mt-1">İcra müddəti bitib</small>
                                            @elseif($daysLeft !== null && $daysLeft <= 3)
                                                <br><span class="badge bg-warning text-dark mt-1">{{ $daysLeft }} gün qalıb</span>
                                            @endif
                                        @endif
                                    @else - @endif
                                </td>
                                <td>
                                    @if($legalAct->executionNote)
                                        @if($isExecuted)
                                            <span class="badge bg-success">{{ $legalAct->executionNote->note }}</span>
                                        @else
                                            <span class="badge bg-secondary">{{ Str::limit($legalAct->executionNote->note, 25) }}</span>
                                        @endif
                                    @else - @endif
                                </td>
                                <td class="text-center">{{ $legalAct->related_document_number ?? '-' }}</td>
                                <td class="text-center" data-order="{{ $legalAct->related_document_date?->format('Y-m-d') }}">
                                    {{ $legalAct->related_document_date?->format('d.m.Y') ?? '-' }}
                                </td>
                                <td>
                                    @if($legalAct->insertedUser)
                                        {{ $legalAct->insertedUser->name }} {{ $legalAct->insertedUser->surname }}
                                    @else - @endif
                                </td>
                                <td>
                                    <div class="action-btns">
                                        <button type="button" class="btn btn-sm btn-info" title="Bax"
                                            onclick="showDetails({{ $legalAct->id }})">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        @if($isCreator || in_array(auth()->user()->user_role, ['admin', 'manager']))
                                            <button type="button" class="btn btn-sm btn-warning" title="Redaktə et"
                                                onclick="editRecord({{ $legalAct->id }})">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                        @endif
                                        @if(auth()->user()->user_role === 'admin')
                                            <button type="button" class="btn btn-sm btn-danger" title="Sil"
                                                onclick="deleteRecord({{ $legalAct->id }})">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="16">
                                    <div class="empty-state">
                                        <i class="bi bi-file-text d-block"></i>
                                        <p class="mb-0">Sənəd tapılmadı</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Create Modal --}}
    <div class="modal fade" id="createModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="{{ route('legal-acts.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Yeni sənəd yarat</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Sənədin nömrəsi <span class="text-danger">*</span></label>
                                <input type="text" name="legal_act_number" class="form-control"
                                    value="{{ old('legal_act_number') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Sənədin tarixi <span class="text-danger">*</span></label>
                                <input type="text" name="legal_act_date" class="form-control modal-datepicker"
                                    value="{{ old('legal_act_date') }}" placeholder="Tarix seçin..." required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Sənədin növü <span class="text-danger">*</span></label>
                                <select name="act_type_id" class="form-select modal-select2" required>
                                    <option value="">Seç</option>
                                    @foreach($actTypes as $type)
                                        <option value="{{ $type->id }}" {{ old('act_type_id') == $type->id ? 'selected' : '' }}>
                                            {{ $type->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Kim qəbul edib <span class="text-danger">*</span></label>
                                <select name="issued_by_id" class="form-select modal-select2" required>
                                    <option value="">Seç</option>
                                    @foreach($issuingAuthorities as $authority)
                                        <option value="{{ $authority->id }}" {{ old('issued_by_id') == $authority->id ? 'selected' : '' }}>{{ $authority->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">İcraçı <span class="text-danger">*</span></label>
                                <select name="executor_id" class="form-select modal-select2" required>
                                    <option value="">Seç</option>
                                    @foreach($executors as $executor)
                                        <option value="{{ $executor->id }}" {{ old('executor_id') == $executor->id ? 'selected' : '' }}>
                                            {{ $executor->name }}
                                            {{ $executor->department ? '- ' . $executor->department->name : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">İcra müddəti</label>
                                <input type="text" name="execution_deadline" class="form-control modal-datepicker"
                                    value="{{ old('execution_deadline') }}" placeholder="Tarix seçin...">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Qısa məzmun <span class="text-danger">*</span></label>
                                <textarea name="summary" class="form-control" rows="3"
                                    required>{{ old('summary') }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tapşırığın nömrəsi</label>
                                <input type="text" name="task_number" class="form-control" value="{{ old('task_number') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Qeyd</label>
                                <select name="execution_note_id" class="form-select modal-select2">
                                    <option value="">Seç</option>
                                    @foreach($executionNotes as $note)
                                        <option value="{{ $note->id }}" {{ old('execution_note_id') == $note->id ? 'selected' : '' }}>
                                            {{ Str::limit($note->note, 60) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Tapşırıq</label>
                                <textarea name="task_description" class="form-control"
                                    rows="2">{{ old('task_description') }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Əlaqəli sənədin nömrəsi</label>
                                <input type="text" name="related_document_number" class="form-control"
                                    value="{{ old('related_document_number') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Əlaqəli sənədin tarixi</label>
                                <input type="text" name="related_document_date" class="form-control modal-datepicker"
                                    value="{{ old('related_document_date') }}" placeholder="Tarix seçin...">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İmtina</button>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Yarat</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Edit Modal --}}
    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="editForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bi bi-pencil me-2"></i>Sənədi redaktə et</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body" id="editModalBody">
                        <div class="text-center py-4">
                            <div class="spinner-border text-primary" role="status"></div>
                            <p class="mt-2 text-muted">Yüklənir...</p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İmtina</button>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Yenilə</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Show Modal --}}
    <div class="modal fade" id="showModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-info-circle me-2"></i>Sənəd haqqında məlumat</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="showModalBody">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bağla</button>
                </div>
            </div>
        </div>
    </div>

    <form id="deleteForm" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const $filterCard = $('.container-fluid');

            /* ══════════════════════════════════════════
               1. SELECT2 — Filter dropdowns
               ══════════════════════════════════════════ */
            ['#filter_act_type', '#filter_issued_by', '#filter_executor'].forEach(sel => {
                $(sel).select2({
                    theme: 'bootstrap-5',
                    dropdownParent: $filterCard,
                    placeholder: $(sel).find('option:first').text(),
                    allowClear: true,
                    width: '100%'
                });
            });

            /* ══════════════════════════════════════════
               2. FLATPICKR — Date range filters
               ══════════════════════════════════════════ */
            const fpConfig = {
                mode: 'range',
                dateFormat: 'd.m.Y',
                locale: flatpickr.l10ns.az,
                allowInput: false,
                clickOpens: true
            };
            fpConfig.locale.rangeSeparator = ' — ';

            const fpDateRange = flatpickr('#filter_date_range', { ...fpConfig });
            const fpDeadlineRange = flatpickr('#filter_deadline_range', { ...fpConfig });

            /* ══════════════════════════════════════════
               3. DATATABLES
               ══════════════════════════════════════════ */
            const table = $('#legalActsTable').DataTable({
                paging: true,
                pageLength: 25,
                lengthMenu: [10, 25, 50, 100],
                ordering: true,
                order: [[3, 'desc']],
                searching: true,
                info: true,
                scrollX: true,
                autoWidth: true,
                orderCellsTop: false,
                fixedColumns: {
                    start: 0,
                    end: 1
                },
                dom: '<"d-flex justify-content-between align-items-center flex-wrap px-3 pt-2"lB>rt<"d-flex justify-content-between align-items-center flex-wrap px-3 pb-2"ip>',
                buttons: [
                    {
                        extend: 'colvis',
                        text: '<i class="bi bi-eye me-1"></i> Sütunlar',
                        className: 'btn btn-secondary btn-sm',
                        columns: ':not(:first-child):not(:last-child)'
                    },
                    {
                        text: '<i class="bi bi-file-earmark-excel me-1"></i> Excel',
                        className: 'btn btn-primary btn-sm',
                        action: function () { exportToExcel(); }
                    },
                    {
                        text: '<i class="bi bi-file-earmark-word me-1"></i> Word',
                        className: 'btn btn-info btn-sm',
                        action: function () { exportToWord(); }
                    }
                ],
                columnDefs: [
                    { targets: 0, orderable: false, searchable: false, width: '50px' },    // #
                    { targets: -1, orderable: false, searchable: false, width: '130px' },   // Əməliyyatlar
                    { targets: [1], width: '110px' },          // Sənəd növü
                    { targets: [2], width: '120px' },          // Nömrə
                    { targets: [3], width: '110px' },          // Sənəd tarixi
                    { targets: [4], width: '150px' },          // Kim qəbul edib
                    { targets: [5], width: '240px' },          // Qısa məzmun
                    { targets: [6], width: '110px' },          // Tapşırıq nömrəsi
                    { targets: [7], width: '200px' },          // Tapşırıq
                    { targets: [8], width: '140px' },          // İcraçı
                    { targets: [9], width: '130px' },          // Bölməsi
                    { targets: [10], width: '120px' },         // İcra müddəti
                    { targets: [11], width: '140px' },         // Qeyd
                    { targets: [12], width: '110px' },         // Sənəd nömrəsi (icra)
                    { targets: [13], width: '110px' },         // Sənəd tarixi (icra)
                    { targets: [14], width: '120px' },         // Daxil edən
                ],
                language: {
                    paginate: { previous: "&laquo;", next: "&raquo;" },
                    emptyTable: "Cədvəldə məlumat yoxdur",
                    info: "_START_ - _END_ göstərilir, cəmi: _TOTAL_",
                    infoFiltered: "(filtrlənib, ümumi: _MAX_)",
                    infoEmpty: "Məlumat yoxdur",
                    lengthMenu: "_MENU_ nəticə göstər",
                    loadingRecords: "Yüklənir...",
                    processing: "İşlənilir...",
                    zeroRecords: "Uyğun nəticə tapılmadı",
                    search: "Axtar:"
                }
            });

            (function syncHeaderColors() {
                const origHeaders = $('#legalActsTable thead th');
                const scrollHead = $('.dataTables_scrollHead thead th');
                if (scrollHead.length) {
                    origHeaders.each(function(i) {
                        const cls = Array.from(this.classList).filter(c => c.startsWith('bg-band-'));
                        if (cls.length && scrollHead[i]) {
                            scrollHead[i].classList.add(...cls);
                        }
                    });
                    const origRows = $('#legalActsTable thead tr');
                    const scrollRows = $('.dataTables_scrollHead thead tr');
                    origRows.each(function(i) {
                        if (scrollRows[i]) {
                            scrollRows[i].className = this.className;
                        }
                    });
                }
            })();

            /* ══════════════════════════════════════════
               4. CUSTOM FILTER
               ══════════════════════════════════════════ */
            $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
                const numFilter = $('#filter_legal_act_number').val().trim().toLowerCase();
                if (numFilter && !data[2].toLowerCase().includes(numFilter)) return false;

                const summaryFilter = $('#filter_summary').val().trim().toLowerCase();
                if (summaryFilter && !data[5].toLowerCase().includes(summaryFilter)) return false;

                const actTypeFilter = $('#filter_act_type').val();
                if (actTypeFilter && !data[1].toLowerCase().includes(actTypeFilter)) return false;

                const issuedByFilter = $('#filter_issued_by').val();
                if (issuedByFilter && !data[4].toLowerCase().includes(issuedByFilter)) return false;

                const executorFilter = $('#filter_executor').val();
                if (executorFilter && !data[8].toLowerCase().includes(executorFilter)) return false;

                if (fpDateRange.selectedDates.length) {
                    const cellDate = parseDMY(data[3]);
                    if (!cellDate) return false;
                    const from = stripTime(fpDateRange.selectedDates[0]);
                    const to = stripTime(fpDateRange.selectedDates[1] || fpDateRange.selectedDates[0]);
                    if (cellDate < from || cellDate > to) return false;
                }

                if (fpDeadlineRange.selectedDates.length) {
                    const cellDate = parseDMY(data[10]);
                    if (!cellDate) return false;
                    const from = stripTime(fpDeadlineRange.selectedDates[0]);
                    const to = stripTime(fpDeadlineRange.selectedDates[1] || fpDeadlineRange.selectedDates[0]);
                    if (cellDate < from || cellDate > to) return false;
                }

                return true;
            });

            function parseDMY(str) {
                const m = (str || '').match(/(\d{2})\.(\d{2})\.(\d{4})/);
                if (!m) return null;
                return new Date(+m[3], +m[2] - 1, +m[1]);
            }
            function stripTime(d) {
                return new Date(d.getFullYear(), d.getMonth(), d.getDate());
            }

            $('#filtersSearchBtn').on('click', function () {
                table.draw();
            });

            $('#filtersResetBtn').on('click', function () {
                $('#filter_legal_act_number, #filter_summary').val('');
                $('#filter_act_type, #filter_issued_by, #filter_executor').val(null).trigger('change');
                fpDateRange.clear();
                fpDeadlineRange.clear();
                table.draw();
            });

            $filterCard.on('keydown', 'input.filter-el', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    $('#filtersSearchBtn').trigger('click');
                }
            });

            /* ══════════════════════════════════════════
               5. SELECT2 + FLATPICKR in CREATE modal
               ══════════════════════════════════════════ */
            const $createModal = $('#createModal');
            $createModal.on('shown.bs.modal', function () {
                $(this).find('.modal-select2').each(function () {
                    if (!$(this).data('select2')) {
                        $(this).select2({
                            theme: 'bootstrap-5',
                            dropdownParent: $createModal.find('.modal-body'),
                            placeholder: 'Seç',
                            allowClear: true,
                            width: '100%'
                        });
                    }
                });
                $(this).find('.modal-datepicker').each(function () {
                    if (!this._flatpickr) {
                        flatpickr(this, {
                            dateFormat: 'Y-m-d',
                            locale: flatpickr.l10ns.az,
                            allowInput: true
                        });
                    }
                });
            });
            $createModal.on('hidden.bs.modal', function () {
                $(this).find('.modal-select2').each(function () {
                    if ($(this).data('select2')) $(this).select2('destroy');
                });
                $(this).find('.modal-datepicker').each(function () {
                    if (this._flatpickr) this._flatpickr.destroy();
                });
            });
        });

        /* ══════════════════════════════════════════
           Exports
           ══════════════════════════════════════════ */
        function exportToExcel() {
            const params = buildExportParams();
            window.location.href = "{{ route('legal-acts.export.excel') }}?" + params.toString();
        }
        function exportToWord() {
            const params = buildExportParams();
            window.location.href = "{{ route('legal-acts.export.word') }}?" + params.toString();
        }
        function buildExportParams() {
            const params = new URLSearchParams();
            const num = document.getElementById('filter_legal_act_number')?.value?.trim();
            const sum = document.getElementById('filter_summary')?.value?.trim();
            if (num) params.set('legal_act_number', num);
            if (sum) params.set('summary', sum);
            return params;
        }

        /* ══════════════════════════════════════════
           Show Details
           ══════════════════════════════════════════ */
        async function showDetails(id) {
            const data = await fetchJson(`/legal-acts/${id}`);
            if (!data) return;
            document.getElementById('showModalBody').innerHTML = `
            <table class="table table-bordered detail-table mb-0">
                <tr><th width="30%">Sənədin növü</th><td>${escapeHtml(data.act_type || '-')}</td></tr>
                <tr><th>Sənədin nömrəsi</th><td class="fw-bold">${escapeHtml(data.legal_act_number || '-')}</td></tr>
                <tr><th>Sənədin tarixi</th><td>${escapeHtml(data.legal_act_date || '-')}</td></tr>
                <tr><th>Qısa məzmun</th><td style="white-space:pre-wrap">${escapeHtml(data.summary || '-')}</td></tr>
                <tr><th>Kim qəbul edib</th><td>${escapeHtml(data.issuing_authority || '-')}</td></tr>
                <tr><th>İcraçı</th><td>${escapeHtml(data.executor || '-')}</td></tr>
                <tr><th>İcraçının vəzifəsi</th><td>${escapeHtml(data.executor_position || '-')}</td></tr>
                <tr><th>İcraçının bölməsi</th><td>${escapeHtml(data.executor_department || '-')}</td></tr>
                <tr><th>Tapşırığın nömrəsi</th><td>${escapeHtml(data.task_number || '-')}</td></tr>
                <tr><th>Tapşırıq</th><td style="white-space:pre-wrap">${escapeHtml(data.task_description || '-')}</td></tr>
                <tr><th>İcra müddəti</th><td>${escapeHtml(data.execution_deadline || '-')}</td></tr>
                <tr><th>Qeyd</th><td style="white-space:pre-wrap">${escapeHtml(data.execution_note || '-')}</td></tr>
                <tr><th>Əlaqəli sənəd nömrəsi</th><td>${escapeHtml(data.related_document_number || '-')}</td></tr>
                <tr><th>Əlaqəli sənəd tarixi</th><td>${escapeHtml(data.related_document_date || '-')}</td></tr>
                <tr><th>Daxil edən</th><td>${escapeHtml(data.inserted_user || '-')}</td></tr>
                <tr><th>Yaradılma tarixi</th><td>${escapeHtml(data.created_at || '-')}</td></tr>
            </table>`;
            new bootstrap.Modal(document.getElementById('showModal')).show();
        }

        /* ══════════════════════════════════════════
           Edit Record
           ══════════════════════════════════════════ */
        async function editRecord(id) {
            document.getElementById('editModalBody').innerHTML = `
            <div class="text-center py-4">
                <div class="spinner-border text-primary" role="status"></div>
                <p class="mt-2 text-muted">Yüklənir...</p>
            </div>`;

            const editModalEl = document.getElementById('editModal');
            const editModal = bootstrap.Modal.getOrCreateInstance(editModalEl);
            editModal.show();

            const data = await fetchJson(`/legal-acts/${id}/edit`);
            if (!data) return;

            function buildOptions(items, selectedId, labelKey = 'name') {
                return items.map(item => {
                    const selected = item.id == selectedId ? 'selected' : '';
                    const label = labelKey === 'note' ? (item.note || '').substring(0, 60) : item[labelKey];
                    return `<option value="${item.id}" ${selected}>${escapeHtml(label)}</option>`;
                }).join('');
            }
            function buildExecutorOptions(items, selectedId) {
                return items.map(item => {
                    const selected = item.id == selectedId ? 'selected' : '';
                    const dept = item.department ? ' - ' + item.department.name : '';
                    return `<option value="${item.id}" ${selected}>${escapeHtml(item.name + dept)}</option>`;
                }).join('');
            }

            document.getElementById('editModalBody').innerHTML = `
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Sənədin nömrəsi <span class="text-danger">*</span></label>
                    <input type="text" name="legal_act_number" class="form-control" value="${escapeHtml(data.legal_act_number || '')}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Sənədin tarixi <span class="text-danger">*</span></label>
                    <input type="text" name="legal_act_date" class="form-control edit-datepicker" value="${data.legal_act_date || ''}" placeholder="Tarix seçin..." required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Sənədin növü <span class="text-danger">*</span></label>
                    <select name="act_type_id" class="form-select edit-select2" required>
                        <option value="">Seç</option>
                        ${buildOptions(data.act_types || [], data.act_type_id)}
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Kim qəbul edib <span class="text-danger">*</span></label>
                    <select name="issued_by_id" class="form-select edit-select2" required>
                        <option value="">Seç</option>
                        ${buildOptions(data.authorities || [], data.issued_by_id)}
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">İcraçı <span class="text-danger">*</span></label>
                    <select name="executor_id" class="form-select edit-select2" required>
                        <option value="">Seç</option>
                        ${buildExecutorOptions(data.executors || [], data.executor_id)}
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">İcra müddəti</label>
                    <input type="text" name="execution_deadline" class="form-control edit-datepicker" value="${data.execution_deadline || ''}" placeholder="Tarix seçin...">
                </div>
                <div class="col-12">
                    <label class="form-label">Qısa məzmun <span class="text-danger">*</span></label>
                    <textarea name="summary" class="form-control" rows="3" required>${escapeHtml(data.summary || '')}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tapşırığın nömrəsi</label>
                    <input type="text" name="task_number" class="form-control" value="${escapeHtml(data.task_number || '')}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Qeyd</label>
                    <select name="execution_note_id" class="form-select edit-select2">
                        <option value="">Seç (ixtiyari)</option>
                        ${buildOptions(data.execution_notes || [], data.execution_note_id, 'note')}
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">Tapşırıq</label>
                    <textarea name="task_description" class="form-control" rows="2">${escapeHtml(data.task_description || '')}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Əlaqəli sənəd nömrəsi</label>
                    <input type="text" name="related_document_number" class="form-control" value="${escapeHtml(data.related_document_number || '')}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Əlaqəli sənəd tarixi</label>
                    <input type="text" name="related_document_date" class="form-control edit-datepicker" value="${data.related_document_date || ''}" placeholder="Tarix seçin...">
                </div>
            </div>`;

            document.getElementById('editForm').action = `/legal-acts/${id}`;

            const $editModal = $('#editModal');
            $editModal.find('.edit-select2').each(function () {
                $(this).select2({
                    theme: 'bootstrap-5',
                    dropdownParent: $editModal.find('.modal-body'),
                    placeholder: 'Seç',
                    allowClear: true,
                    width: '100%'
                });
            });
            $editModal.find('.edit-datepicker').each(function () {
                flatpickr(this, {
                    dateFormat: 'Y-m-d',
                    locale: flatpickr.l10ns.az,
                    allowInput: true
                });
            });
        }

        $('#editModal').on('hidden.bs.modal', function () {
            $(this).find('.edit-select2').each(function () {
                if ($(this).data('select2')) $(this).select2('destroy');
            });
            $(this).find('.edit-datepicker').each(function () {
                if (this._flatpickr) this._flatpickr.destroy();
            });
        });

        /* ══════════════════════════════════════════
           Delete
           ══════════════════════════════════════════ */
        function deleteRecord(id) {
            if (confirm('Bu sənədi silmək istədiyinizə əminsiniz?')) {
                const form = document.getElementById('deleteForm');
                form.action = `/legal-acts/${id}`;
                form.submit();
            }
        }

        /* ── Re-open create modal on validation errors ── */
        @if($errors->any() && old('_token'))
            document.addEventListener('DOMContentLoaded', function () {
                new bootstrap.Modal(document.getElementById('createModal')).show();
            });
        @endif
    </script>
@endpush