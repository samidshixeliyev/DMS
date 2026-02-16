@extends('layouts.app')

@section('title', 'Hüquqi Aktlar')

@push('styles')
    <style>
        .row-overdue td {
            background-color: #fef2f2 !important;
            border-left-color: #fecaca !important;
        }

        .row-overdue:hover td {
            background-color: #fee2e2 !important;
        }

        .row-overdue td:first-child {
            box-shadow: inset 3px 0 0 #dc2626;
        }

        .row-warning td {
            background-color: #fefce8 !important;
            border-left-color: #fef08a !important;
        }

        .row-warning:hover td {
            background-color: #fef9c3 !important;
        }

        .row-warning td:first-child {
            box-shadow: inset 3px 0 0 #ca8a04;
        }

        .row-executed td {
            background-color: #f0fdf4 !important;
            border-left-color: #bbf7d0 !important;
        }

        .row-executed:hover td {
            background-color: #dcfce7 !important;
        }

        .row-executed td:first-child {
            box-shadow: inset 3px 0 0 #16a34a;
        }

        .filter-row .flatpickr-input {
            background: #fff !important;
            cursor: pointer;
        }

        .filter-row .select2-container--bootstrap-5 .select2-selection--single {
            display: flex !important;
            align-items: center !important;
            min-height: 38px;
        }

        .filter-row .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
            line-height: normal !important;
            padding-top: 0 !important;
            padding-bottom: 0 !important;
        }

        .filter-row .select2-container--bootstrap-5 .select2-selection--single .select2-selection__placeholder {
            line-height: normal !important;
            color: #94a3b8 !important;
        }

        #legalActsTable thead th,
        .dataTables_scrollHead thead th {
            text-align: center !important;
            vertical-align: middle !important;
            font-weight: 700;
            white-space: nowrap;
            color: #fff !important;
            border: 1px solid rgba(255, 255, 255, 0.18) !important;
        }

        #legalActsTable thead tr.band-header th,
        .dataTables_scrollHead thead tr.band-header th {
            font-size: 0.82rem;
            padding: 0.6rem 0.6rem;
            letter-spacing: 0.3px;
        }

        #legalActsTable thead tr.sub-header th,
        .dataTables_scrollHead thead tr.sub-header th {
            font-size: 0.74rem;
            font-weight: 600;
            padding: 0.5rem 0.45rem;
        }
        
        th.bg-band-index {
            background: #374151 !important;
        }

        th.bg-band-doc {
            background: #1e3a5f !important;
        }

        th.bg-band-doc-sub {
            background: #2a5298 !important;
        }

        th.bg-band-task {
            background: #065f46 !important;
        }

        th.bg-band-task-sub {
            background: #10a37f !important;
        }

        th.bg-band-exec {
            background: #5b21b6 !important;
        }

        th.bg-band-exec-sub {
            background: #7c4ddb !important;
        }

        th.bg-band-icra {
            background: #92400e !important;
        }

        th.bg-band-icra-sub {
            background: #d97706 !important;
        }

        th.bg-band-actions {
            background: #374151 !important;
        }

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

        #legalActsTable tbody tr:nth-child(even):not([class*="row-"]) {
            background-color: #f8fafc;
        }

        #legalActsTable_wrapper .dt-buttons {
            gap: 0.35rem;
            display: flex;
        }

        div.dataTables_wrapper div.dataTables_length select {
            min-width: 60px;
        }

        #legalActsTable td:last-child,
        .last-custom-col{
            white-space: nowrap;
            position: sticky;
            right: 0; 
            background-color: #fff;
            box-shadow: -2px 0 4px rgba(0, 0, 0, 0.06);
            z-index: 999 !important;
        }

        #legalActsTable thead th:last-child {
            z-index: 4;
        }

        .row-overdue td:last-child {
            background-color: #fef2f2 !important;
        }

        .row-warning td:last-child {
            background-color: #fefce8 !important;
        }

        .row-executed td:last-child {
            background-color: #f0fdf4 !important;
        }

        #legalActsTable tbody tr:nth-child(even):not([class*="row-"]) td:last-child {
            background-color: #f8fafc;
        }

        #legalActsTable tbody td .badge {
            display: inline-block;
            vertical-align: middle;
        }

        #legalActsTable_paginate{
            padding: 8px 0;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            align-items: center;
        }

        .pagination{
            margin: 0;
        }

        .action-btns {
            display: flex;
            flex-direction: column;
            gap: 4px;
            flex-wrap: wrap;
            justify-content: center;
            align-items: center;
        }

        .action-btns .btn {
            width: 32px;
            height: 32px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
        }

        .dt-buttons .btn.btn-primary {
            background-color: var(--bs-primary) !important;
            border-color: var(--bs-primary) !important;
            color: #fff !important;
        }

        .dt-buttons .btn.btn-info {
            background-color: var(--bs-info) !important;
            border-color: var(--bs-info) !important;
            color: #fff !important;
        }
    </style>
@endpush

@section('content')
    <div class="page-header">
        <h2><i class="bi bi-file-text me-2"></i>Hüquqi Aktlar</h2>
        <div class="d-flex gap-2 flex-wrap">
            @if($canManage)
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createModal">
                    <i class="bi bi-plus-circle me-1"></i> Yeni əlavə et
                </button>
            @endif
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

    <div class="card filter-card mb-3">
        <div class="card-header" data-bs-toggle="collapse" data-bs-target="#filterBody" aria-expanded="true">
            <h5 class="mb-0 d-flex align-items-center">
                <i class="bi bi-funnel me-2"></i> Filtrlər
                <i class="bi bi-chevron-down ms-auto"></i>
            </h5>
        </div>
        <div class="collapse show" id="filterBody">
            <div class="card-body filter-row">
                <div class="row g-2">
                    <div class="col-xl-2 col-md-3">
                        <label class="form-label">Sənədin nömrəsi</label>
                        <input type="text" id="filter_legal_act_number" class="form-control filter-el"
                            placeholder="Axtar...">
                    </div>
                    <div class="col-xl-2 col-md-3">
                        <label class="form-label">Qısa məzmun</label>
                        <input type="text" id="filter_summary" class="form-control filter-el" placeholder="Axtar...">
                    </div>
                    <div class="col-xl-2 col-md-3">
                        <label class="form-label">Sənədin növü</label>
                        <select id="filter_act_type" class="form-select filter-select">
                            <option value="">Hamısı</option>
                            @foreach($actTypes as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-xl-2 col-md-3">
                        <label class="form-label">Kim qəbul edib</label>
                        <select id="filter_issued_by" class="form-select filter-select">
                            <option value="">Hamısı</option>
                            @foreach($issuingAuthorities as $authority)
                                <option value="{{ $authority->id }}">{{ $authority->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-xl-2 col-md-3">
                        <label class="form-label">İcraçı</label>
                        <select id="filter_executor" class="form-select filter-select">
                            <option value="">Hamısı</option>
                            @foreach($executors as $executor)
                                <option value="{{ $executor->id }}">{{ $executor->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-xl-2 col-md-3">
                        <label class="form-label">İcra qeydi</label>
                        <select id="filter_execution_note" class="form-select filter-select">
                            <option value="">Hamısı</option>
                            @foreach($executionNotes as $note)
                                <option value="{{ $note->id }}">{{ Str::limit($note->note, 40) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-xl-2 col-md-3">
                        <label class="form-label">Sənəd tarixi</label>
                        <input type="text" id="filter_date_range" class="form-control filter-el"
                            placeholder="Tarix aralığı seçin..." readonly>
                    </div>
                    <div class="col-xl-2 col-md-3">
                        <label class="form-label">İcra müddəti</label>
                        <input type="text" id="filter_deadline_range" class="form-control filter-el"
                            placeholder="Tarix aralığı seçin..." readonly>
                    </div>
                    <div class="col-xl-2 col-md-3">
                        <label class="form-label">Müddət statusu</label>
                        <select id="filter_deadline_status" class="form-select filter-select">
                            <option value="">Hamısı</option>
                            <option value="expired">Müddəti bitib</option>
                            <option value="0day">İcra müddəti son gün</option>
                            <option value="1day">1 gün qalıb</option>
                            <option value="2days">2 gün qalıb</option>
                            <option value="3days">3 gün qalıb</option>
                            <option value="executed">İcra olunub</option>
                        </select>
                    </div>
                    <div class="col-xl-2 col-md-3">
                        <label class="form-label">Tapşırıq nömrəsi</label>
                        <input type="text" id="filter_task_number" class="form-control filter-el" placeholder="Axtar...">
                    </div>
                    <div class="col-xl-2 col-md-3">
                        <label class="form-label">Bölmə</label>
                        <select id="filter_department" class="form-select filter-select">
                            <option value="">Hamısı</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-xl-1 col-md-3 d-flex align-items-end gap-2">
                        <button type="button" id="filtersSearchBtn" class="btn btn-primary">
                            <i class="bi bi-search"></i>
                        </button>
                        <button type="button" id="filtersResetBtn" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div style="overflow-x:auto;">
                <table class="table table-hover table-bordered mb-0" id="legalActsTable" style="width:100%">
                    <thead>
                        <tr class="band-header">
                            <!-- <th rowspan="2" class="bg-band-index">#</th> -->
                            <th colspan="5" class="bg-band-doc">Sənəd Məlumatları</th>
                            <th colspan="2" class="bg-band-task">Tapşırıq Məlumatları</th>
                            <th colspan="4" class="bg-band-exec">İcraçı Məlumatları</th>
                            <th colspan="3" class="bg-band-icra">İcra Məlumatları</th>
                            <th rowspan="2" class="bg-band-actions last-custom-col"></th>
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
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

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
                                            {{ $executor->name }}{{ $executor->department ? ' - ' . $executor->department->name : '' }}
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
                                        <option value="{{ $note->id }}" {{ old('execution_note_id') == $note->id ? 'selected' : '' }}>{{ Str::limit($note->note, 60) }}</option>
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
            var $parent = $('.container-fluid');

            ['#filter_act_type', '#filter_issued_by', '#filter_executor', '#filter_execution_note', '#filter_deadline_status', '#filter_department'].forEach(function (sel) {
                $(sel).select2({
                    theme: 'bootstrap-5',
                    dropdownParent: $parent,
                    placeholder: $(sel).find('option:first').text(),
                    allowClear: true,
                    width: '100%'
                });
            });

            var fpRangeConfig = {
                mode: 'range',
                dateFormat: 'd.m.Y',
                locale: flatpickr.l10ns.az,
                allowInput: false,
                clickOpens: true
            };
            fpRangeConfig.locale.rangeSeparator = ' — ';

            var fpDateRange = flatpickr('#filter_date_range', Object.assign({}, fpRangeConfig));
            var fpDeadlineRange = flatpickr('#filter_deadline_range', Object.assign({}, fpRangeConfig));

            function readRange(fp) {
                var result = { from: '', to: '' };
                if (fp.selectedDates.length > 0) {
                    var d1 = fp.selectedDates[0];
                    var d2 = fp.selectedDates.length > 1 ? fp.selectedDates[1] : d1;
                    result.from = d1.getFullYear() + '-' + String(d1.getMonth() + 1).padStart(2, '0') + '-' + String(d1.getDate()).padStart(2, '0');
                    result.to = d2.getFullYear() + '-' + String(d2.getMonth() + 1).padStart(2, '0') + '-' + String(d2.getDate()).padStart(2, '0');
                }
                return result;
            }

            function getFiltersForDT(d) {
                d.col = {};
                var num = $('#filter_legal_act_number').val();
                var sum = $('#filter_summary').val();
                var actType = $('#filter_act_type').val();
                var issuedBy = $('#filter_issued_by').val();
                var executor = $('#filter_executor').val();
                var execNote = $('#filter_execution_note').val();
                var deadStatus = $('#filter_deadline_status').val();
                var taskNum = $('#filter_task_number').val();
                var dept = $('#filter_department').val();
                if (num) d.col.legal_act_number = num.trim();
                if (sum) d.col.summary = sum.trim();
                if (actType) d.col.act_type_id = actType;
                if (issuedBy) d.col.issued_by_id = issuedBy;
                if (executor) d.col.executor_id = executor;
                if (execNote) d.col.execution_note_id = execNote;
                if (deadStatus) d.col.deadline_status = deadStatus;
                if (taskNum) d.col.task_number = taskNum.trim();
                if (dept) d.col.department_id = dept;
                var dateR = readRange(fpDateRange);
                if (dateR.from) d.col.legal_act_date_from = dateR.from;
                if (dateR.to) d.col.legal_act_date_to = dateR.to;
                var deadR = readRange(fpDeadlineRange);
                if (deadR.from) d.col.deadline_from = deadR.from;
                if (deadR.to) d.col.deadline_to = deadR.to;
            }

            var table = $('#legalActsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('legal-acts.load') }}",
                    type: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken },
                    data: function (d) {
                        getFiltersForDT(d);
                    }
                },
                columns: [
                    {
                        data: 'actType',
                        className: 'text-center',
                        render: function (data) {
                            if (!data || data === '-') return '-';
                            return '<span class="badge" style="background:var(--accent-dark)">' + escapeHtml(data) + '</span>';
                        }
                    },
                    { data: 'legalActNumber', className: 'fw-semibold text-center' },
                    { data: 'legalActDate', className: 'text-center' },
                    { data: 'issuingAuthority' },
                    { data: 'summary', className: 'wrap-cell' },
                    { data: 'taskNumber', className: 'text-center' },
                    { data: 'taskDescription', className: 'wrap-cell' },
                    { data: 'executor' },
                    { data: 'department' },
                    { data: 'deadlineHtml', className: 'text-center' },
                    { data: 'noteHtml' },
                    { data: 'relatedDocNumber', className: 'text-center' },
                    { data: 'relatedDocDate', className: 'text-center' },
                    { data: 'insertedUser' },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function (data) {
                            var html = '<div class="action-btns">';
                            html += '<button type="button" class="btn btn-sm btn-info" title="Bax" onclick="showDetails(' + data.id + ')"><i class="bi bi-eye"></i></button>';
                            if (data.canEdit) {
                                html += '<button type="button" class="btn btn-sm btn-warning" title="Redaktə et" onclick="editRecord(' + data.id + ')"><i class="bi bi-pencil"></i></button>';
                            }
                            if (data.canDelete) {
                                html += '<button type="button" class="btn btn-sm btn-danger" title="Sil" onclick="deleteRecord(' + data.id + ')"><i class="bi bi-trash"></i></button>';
                            }
                            html += '</div>';
                            return html;
                        }
                    }
                ],
                order: [[2, 'desc']],
                pageLength: 25,
                lengthMenu: [10, 25, 50, 100],
                autoWidth: true,
                orderCellsTop: true,
                fixedColumns: false,
                autoWidth: false,
                dom: '<"d-flex justify-content-between align-items-center flex-wrap px-3 pt-2"lB>rt<"d-flex justify-content-between align-items-center flex-wrap px-3 pb-2"ip>',
                buttons: [
                    {
                        extend: 'colvis',
                        text: '<i class="bi bi-eye me-1"></i> Sütunlar',
                        className: 'btn btn-secondary btn-sm',
                        columns: ':not(:last-child)'
                    },
                    {
                        text: '<i class="bi bi-file-earmark-excel me-1"></i> Excel',
                        className: 'btn btn-primary btn-sm',
                        action: function () { exportFile('excel'); }
                    },
                    {
                        text: '<i class="bi bi-file-earmark-word me-1"></i> Word',
                        className: 'btn btn-info btn-sm',
                        action: function () { exportFile('word'); }
                    }
                ],
                columnDefs: [
                    { targets: -1, orderable: false, searchable: false },
                    { targets: 0, width: '70px' },
                    { targets: 1, width: '70px' },
                    { targets: 2, width: '70px' },
                    { targets: 3, width: '150px' },
                    { targets: 4, width: '200px' },
                    { targets: 5, width: '70px' },
                    { targets: 6, width: '150px' },
                    { targets: 7, width: '100px' },
                    { targets: 8, width: '100px' },
                    { targets: 9, width: '70px' },
                    { targets: [10, 11], orderable: true, searchable: false, width: '150px' },
                    { targets: 14, width: '50px'}
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
                },
                drawCallback: function () {
                },
                initComplete: function () {
                    var $scrollDiv = $('#legalActsTable').closest('[style*="overflow"]');
                    var $wrapper = $('#legalActsTable_wrapper');
                    
                    var $header = $wrapper.children('.d-flex').first();
                    $scrollDiv.before($header);
                    
                    var $footer = $wrapper.find('.dataTables_info, .dataTables_paginate').closest('.d-flex');
                    $scrollDiv.after($footer);
                }
            });

            function syncHeaderColors() {
                var scrollHead = $('.dataTables_scrollHead thead');
                if (scrollHead.length) {
                    var origRows = $('#legalActsTable thead tr');
                    var scrollRows = scrollHead.find('tr');
                    origRows.each(function (i) {
                        if (scrollRows[i]) {
                            scrollRows[i].className = this.className;
                            $(this).find('th').each(function (j) {
                                var scrollTh = $(scrollRows[i]).find('th').eq(j);
                                if (scrollTh.length) {
                                    var bgClasses = Array.from(this.classList).filter(c => c.startsWith('bg-band-') || c === 'last-custom-col');
                                    scrollTh.addClass(bgClasses.join(' '));
                                }
                            });
                        }
                    });
                }
            }

            $('#filtersSearchBtn').on('click', function () {
                table.ajax.reload();
            });

            $('#filtersResetBtn').on('click', function () {
                $('#filter_legal_act_number, #filter_summary, #filter_task_number').val('');
                $('#filter_act_type, #filter_issued_by, #filter_executor, #filter_execution_note, #filter_deadline_status, #filter_department').val(null).trigger('change');
                fpDateRange.clear();
                fpDeadlineRange.clear();
                table.ajax.reload();
            });

            $parent.on('keydown', 'input.filter-el', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    table.ajax.reload();
                }
            });

            window.buildExportParams = function () {
                var params = new URLSearchParams();
                var num = $('#filter_legal_act_number').val();
                var sum = $('#filter_summary').val();
                var actType = $('#filter_act_type').val();
                var issuedBy = $('#filter_issued_by').val();
                var executor = $('#filter_executor').val();
                var execNote = $('#filter_execution_note').val();
                var deadStatus = $('#filter_deadline_status').val();
                var taskNum = $('#filter_task_number').val();
                var dept = $('#filter_department').val();
                if (num && num.trim()) params.set('col[legal_act_number]', num.trim());
                if (sum && sum.trim()) params.set('col[summary]', sum.trim());
                if (actType) params.set('col[act_type_id]', actType);
                if (issuedBy) params.set('col[issued_by_id]', issuedBy);
                if (executor) params.set('col[executor_id]', executor);
                if (execNote) params.set('col[execution_note_id]', execNote);
                if (deadStatus) params.set('col[deadline_status]', deadStatus);
                if (taskNum && taskNum.trim()) params.set('col[task_number]', taskNum.trim());
                if (dept) params.set('col[department_id]', dept);
                var dateR = readRange(fpDateRange);
                if (dateR.from) params.set('col[legal_act_date_from]', dateR.from);
                if (dateR.to) params.set('col[legal_act_date_to]', dateR.to);
                var deadR = readRange(fpDeadlineRange);
                if (deadR.from) params.set('col[deadline_from]', deadR.from);
                if (deadR.to) params.set('col[deadline_to]', deadR.to);
                return params;
            };

            var $createModal = $('#createModal');
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

        function exportFile(type) {
            var params = buildExportParams();
            var url = type === 'excel'
                ? "{{ route('legal-acts.export.excel') }}"
                : "{{ route('legal-acts.export.word') }}";
            window.location.href = url + '?' + params.toString();
        }

        async function showDetails(id) {
            var data = await fetchJson('/legal-acts/' + id);
            if (!data) return;
            document.getElementById('showModalBody').innerHTML =
                '<table class="table table-bordered detail-table mb-0">' +
                '<tr><th width="30%">Sənədin növü</th><td>' + escapeHtml(data.act_type || '-') + '</td></tr>' +
                '<tr><th>Sənədin nömrəsi</th><td class="fw-bold">' + escapeHtml(data.legal_act_number || '-') + '</td></tr>' +
                '<tr><th>Sənədin tarixi</th><td>' + escapeHtml(data.legal_act_date || '-') + '</td></tr>' +
                '<tr><th>Qısa məzmun</th><td style="white-space:pre-wrap">' + escapeHtml(data.summary || '-') + '</td></tr>' +
                '<tr><th>Kim qəbul edib</th><td>' + escapeHtml(data.issuing_authority || '-') + '</td></tr>' +
                '<tr><th>İcraçı</th><td>' + escapeHtml(data.executor || '-') + '</td></tr>' +
                '<tr><th>İcraçının vəzifəsi</th><td>' + escapeHtml(data.executor_position || '-') + '</td></tr>' +
                '<tr><th>İcraçının bölməsi</th><td>' + escapeHtml(data.executor_department || '-') + '</td></tr>' +
                '<tr><th>Tapşırığın nömrəsi</th><td>' + escapeHtml(data.task_number || '-') + '</td></tr>' +
                '<tr><th>Tapşırıq</th><td style="white-space:pre-wrap">' + escapeHtml(data.task_description || '-') + '</td></tr>' +
                '<tr><th>İcra müddəti</th><td>' + escapeHtml(data.execution_deadline || '-') + '</td></tr>' +
                '<tr><th>Qeyd</th><td style="white-space:pre-wrap">' + escapeHtml(data.execution_note || '-') + '</td></tr>' +
                '<tr><th>Əlaqəli sənəd nömrəsi</th><td>' + escapeHtml(data.related_document_number || '-') + '</td></tr>' +
                '<tr><th>Əlaqəli sənəd tarixi</th><td>' + escapeHtml(data.related_document_date || '-') + '</td></tr>' +
                '<tr><th>Daxil edən</th><td>' + escapeHtml(data.inserted_user || '-') + '</td></tr>' +
                '<tr><th>Yaradılma tarixi</th><td>' + escapeHtml(data.created_at || '-') + '</td></tr>' +
                '</table>';
            new bootstrap.Modal(document.getElementById('showModal')).show();
        }

        async function editRecord(id) {
            document.getElementById('editModalBody').innerHTML =
                '<div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div><p class="mt-2 text-muted">Yüklənir...</p></div>';

            var editModalEl = document.getElementById('editModal');
            var editModal = bootstrap.Modal.getOrCreateInstance(editModalEl);
            editModal.show();

            var data = await fetchJson('/legal-acts/' + id + '/edit');
            if (!data) return;

            function buildOptions(items, selectedId, labelKey) {
                labelKey = labelKey || 'name';
                return items.map(function (item) {
                    var sel = item.id == selectedId ? 'selected' : '';
                    var label = labelKey === 'note' ? (item.note || '').substring(0, 60) : item[labelKey];
                    return '<option value="' + item.id + '" ' + sel + '>' + escapeHtml(label) + '</option>';
                }).join('');
            }

            function buildExecutorOptions(items, selectedId) {
                return items.map(function (item) {
                    var sel = item.id == selectedId ? 'selected' : '';
                    var dept = item.department ? ' - ' + item.department.name : '';
                    return '<option value="' + item.id + '" ' + sel + '>' + escapeHtml(item.name + dept) + '</option>';
                }).join('');
            }

            document.getElementById('editModalBody').innerHTML =
                '<div class="row g-3">' +
                '<div class="col-md-6"><label class="form-label">Sənədin nömrəsi <span class="text-danger">*</span></label><input type="text" name="legal_act_number" class="form-control" value="' + escapeHtml(data.legal_act_number || '') + '" required></div>' +
                '<div class="col-md-6"><label class="form-label">Sənədin tarixi <span class="text-danger">*</span></label><input type="text" name="legal_act_date" class="form-control edit-datepicker" value="' + (data.legal_act_date || '') + '" placeholder="Tarix seçin..." required></div>' +
                '<div class="col-md-6"><label class="form-label">Sənədin növü <span class="text-danger">*</span></label><select name="act_type_id" class="form-select edit-select2" required><option value="">Seç</option>' + buildOptions(data.act_types || [], data.act_type_id) + '</select></div>' +
                '<div class="col-md-6"><label class="form-label">Kim qəbul edib <span class="text-danger">*</span></label><select name="issued_by_id" class="form-select edit-select2" required><option value="">Seç</option>' + buildOptions(data.authorities || [], data.issued_by_id) + '</select></div>' +
                '<div class="col-md-6"><label class="form-label">İcraçı <span class="text-danger">*</span></label><select name="executor_id" class="form-select edit-select2" required><option value="">Seç</option>' + buildExecutorOptions(data.executors || [], data.executor_id) + '</select></div>' +
                '<div class="col-md-6"><label class="form-label">İcra müddəti</label><input type="text" name="execution_deadline" class="form-control edit-datepicker" value="' + (data.execution_deadline || '') + '" placeholder="Tarix seçin..."></div>' +
                '<div class="col-12"><label class="form-label">Qısa məzmun <span class="text-danger">*</span></label><textarea name="summary" class="form-control" rows="3" required>' + escapeHtml(data.summary || '') + '</textarea></div>' +
                '<div class="col-md-6"><label class="form-label">Tapşırığın nömrəsi</label><input type="text" name="task_number" class="form-control" value="' + escapeHtml(data.task_number || '') + '"></div>' +
                '<div class="col-md-6"><label class="form-label">Qeyd</label><select name="execution_note_id" class="form-select edit-select2"><option value="">Seç (ixtiyari)</option>' + buildOptions(data.execution_notes || [], data.execution_note_id, 'note') + '</select></div>' +
                '<div class="col-12"><label class="form-label">Tapşırıq</label><textarea name="task_description" class="form-control" rows="2">' + escapeHtml(data.task_description || '') + '</textarea></div>' +
                '<div class="col-md-6"><label class="form-label">Əlaqəli sənəd nömrəsi</label><input type="text" name="related_document_number" class="form-control" value="' + escapeHtml(data.related_document_number || '') + '"></div>' +
                '<div class="col-md-6"><label class="form-label">Əlaqəli sənəd tarixi</label><input type="text" name="related_document_date" class="form-control edit-datepicker" value="' + (data.related_document_date || '') + '" placeholder="Tarix seçin..."></div>' +
                '</div>';

            document.getElementById('editForm').action = '/legal-acts/' + id;

            var $editModal = $('#editModal');
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

        function deleteRecord(id) {
            if (confirm('Bu sənədi silmək istədiyinizə əminsiniz?')) {
                var form = document.getElementById('deleteForm');
                form.action = '/legal-acts/' + id;
                form.submit();
            }
        }

        @if($errors->any() && old('_token'))
            document.addEventListener('DOMContentLoaded', function () {
                new bootstrap.Modal(document.getElementById('createModal')).show();
            });
        @endif
    </script>
@endpush