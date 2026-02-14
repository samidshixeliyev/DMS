@extends('layouts.app')

@section('title', 'Hüquqi Aktlar')

@push('styles')
<style>
    /* Row coloring for deadline status */
    .row-overdue {
        background-color: #ff4444 !important;
        color: #fff;
    }
    .row-overdue:hover {
        background-color: #ff3333 !important;
        color: #fff;
    }
    .row-overdue td {
        color: #fff !important;
    }
    .row-overdue .badge {
        background: rgba(255,255,255,0.25) !important;
        color: #fff !important;
    }
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
</style>
@endpush

@section('content')
<div class="page-header">
    <h2><i class="bi bi-file-text me-2"></i>Hüquqi Aktlar</h2>
    <div class="d-flex gap-2 flex-wrap">
        @if(in_array(auth()->user()->user_role, ['admin', 'manager']))
        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createModal">
            <i class="bi bi-plus-circle me-1"></i> Yeni əlavə et
        </button>
        @endif
        <button type="button" class="btn btn-primary" onclick="exportToExcel()">
            <i class="bi bi-file-earmark-excel me-1"></i> Excel
        </button>
        <button type="button" class="btn btn-info" onclick="exportToWord()">
            <i class="bi bi-file-earmark-word me-1"></i> Word
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
        <div class="card-body">
            <form method="GET" action="{{ route('legal-acts.index') }}" id="filterForm">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Sənədin nömrəsi</label>
                        <input type="text" name="legal_act_number" class="form-control" 
                               value="{{ request('legal_act_number') }}" placeholder="Axtar...">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Qısa məzmun</label>
                        <input type="text" name="summary" class="form-control" 
                               value="{{ request('summary') }}" placeholder="Axtar...">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Sənədin növü</label>
                        <select name="act_type_id" class="form-select">
                            <option value="">Hamısı</option>
                            @foreach($actTypes as $type)
                                <option value="{{ $type->id }}" {{ request('act_type_id') == $type->id ? 'selected' : '' }}>
                                    {{ $type->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Kim qəbul edib</label>
                        <select name="issued_by_id" class="form-select">
                            <option value="">Hamısı</option>
                            @foreach($issuingAuthorities as $authority)
                                <option value="{{ $authority->id }}" {{ request('issued_by_id') == $authority->id ? 'selected' : '' }}>
                                    {{ $authority->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">İcraçı</label>
                        <select name="executor_id" class="form-select">
                            <option value="">Hamısı</option>
                            @foreach($executors as $executor)
                                <option value="{{ $executor->id }}" {{ request('executor_id') == $executor->id ? 'selected' : '' }}>
                                    {{ $executor->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tarixdən</label>
                        <input type="date" name="legal_act_date_from" class="form-control" 
                               value="{{ request('legal_act_date_from') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tarixədək</label>
                        <input type="date" name="legal_act_date_to" class="form-control" 
                               value="{{ request('legal_act_date_to') }}">
                    </div>
                    <div class="col-md-3 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search me-1"></i> Axtar
                        </button>
                        <a href="{{ route('legal-acts.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle me-1"></i> Sıfırla
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Data Table with Banded Headers --}}
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span class="fw-bold">
            <i class="bi bi-list-ul me-1"></i> 
            Cəmi: {{ $legalActs->total() }} qeyd
        </span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-bordered mb-0">
                <thead>
                    {{-- Band (group) header row --}}
                    <tr class="band-header">
                        <th rowspan="2" style="vertical-align:middle; width:50px">#</th>
                        <th colspan="5" class="band-doc">Sənəd məlumatları</th>
                        <th colspan="2" class="band-task">Tapşırıq məlumatları</th>
                        <th colspan="4" class="band-executor">İcraçı məlumatları</th>
                        <th colspan="3" class="band-execution">İcra məlumatları</th>
                        <th rowspan="2" style="vertical-align:middle; width:120px">Əməliyyatlar</th>
                    </tr>
                    {{-- Sub-header row --}}
                    <tr class="sub-header">
                        {{-- Sənəd məlumatları --}}
                        <th>Sənədin<br>növü</th>
                        <th>Sənədin<br>nömrəsi</th>
                        <th>Sənədin<br>tarixi</th>
                        <th>Kim qəbul<br>edib</th>
                        <th>Qısa<br>məzmunu</th>
                        {{-- Tapşırıq məlumatları --}}
                        <th>Tapşırığın<br>nömrəsi</th>
                        <th>Tapşırıq</th>
                        {{-- İcraçı məlumatları --}}
                        <th>İcraçı</th>
                        <th>Bölməsi</th>
                        <th>İcra<br>müddəti</th>
                        <th>Qeyd</th>
                        {{-- İcra məlumatları --}}
                        <th>Sənədin<br>nömrəsi</th>
                        <th>Sənədin<br>tarixi</th>
                        <th>Daxil<br>edən</th>
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

                            // Check if current user is the creator
                            $isCreator = auth()->id() === $legalAct->inserted_user_id;
                        @endphp
                        <tr class="{{ $rowClass }}">
                            <td><span class="badge bg-secondary">{{ $legalActs->firstItem() + $index }}</span></td>
                            {{-- Sənəd məlumatları --}}
                            <td>
                                @if($legalAct->actType)
                                    <span class="badge" style="background: var(--accent-dark)">{{ $legalAct->actType->name }}</span>
                                @else - @endif
                            </td>
                            <td class="fw-semibold">{{ $legalAct->legal_act_number }}</td>
                            <td>{{ $legalAct->legal_act_date?->format('d.m.Y') }}</td>
                            <td>{{ $legalAct->issuingAuthority?->name ?? '-' }}</td>
                            <td>{{ Str::limit($legalAct->summary, 50) }}</td>
                            {{-- Tapşırıq məlumatları --}}
                            <td>{{ $legalAct->task_number ?? '-' }}</td>
                            <td>{{ Str::limit($legalAct->task_description, 40) ?: '-' }}</td>
                            {{-- İcraçı məlumatları --}}
                            <td>{{ $legalAct->executor?->name ?? '-' }}</td>
                            <td>{{ $legalAct->executor?->department?->name ?? '-' }}</td>
                            <td>
                                @if($legalAct->execution_deadline)
                                    {{ $legalAct->execution_deadline->format('d.m.Y') }}
                                    @if(!$isExecuted)
                                        @if($daysLeft !== null && $daysLeft < 0)
                                            <br><small class="fw-bold">İcra müddəti bitib</small>
                                        @elseif($daysLeft !== null && $daysLeft <= 3)
                                            <br><span class="badge bg-warning text-dark mt-1">{{ $daysLeft }} gün qalıb</span>
                                        @endif
                                    @endif
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if($legalAct->executionNote)
                                    @if($isExecuted)
                                        <span class="badge bg-success">{{ $legalAct->executionNote->note }}</span>
                                    @else
                                        <span class="badge bg-secondary">{{ Str::limit($legalAct->executionNote->note, 30) }}</span>
                                    @endif
                                @else
                                    -
                                @endif
                            </td>
                            {{-- İcra məlumatları --}}
                            <td>{{ $legalAct->related_document_number ?? '-' }}</td>
                            <td>{{ $legalAct->related_document_date?->format('d.m.Y') ?? '-' }}</td>
                            <td>
                                @if($legalAct->insertedUser)
                                    {{ $legalAct->insertedUser->name }} {{ $legalAct->insertedUser->surname }}
                                @else
                                    -
                                @endif
                            </td>
                            {{-- Əməliyyatlar --}}
                            <td>
                                <div class="action-btns">
                                    <button type="button" class="btn btn-sm btn-info" title="Bax"
                                            onclick="showDetails({{ $legalAct->id }})">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    @if($isCreator || auth()->user()->user_role === 'admin')
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
        @if($legalActs->hasPages())
            <div class="p-3 border-top">
                {{ $legalActs->links() }}
            </div>
        @endif
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
                            <input type="text" name="legal_act_number" class="form-control" value="{{ old('legal_act_number') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Sənədin tarixi <span class="text-danger">*</span></label>
                            <input type="date" name="legal_act_date" class="form-control" value="{{ old('legal_act_date') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Sənədin növü <span class="text-danger">*</span></label>
                            <select name="act_type_id" class="form-select" required>
                                <option value="">Seç</option>
                                @foreach($actTypes as $type)
                                    <option value="{{ $type->id }}" {{ old('act_type_id') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Kim qəbul edib <span class="text-danger">*</span></label>
                            <select name="issued_by_id" class="form-select" required>
                                <option value="">Seç</option>
                                @foreach($issuingAuthorities as $authority)
                                    <option value="{{ $authority->id }}" {{ old('issued_by_id') == $authority->id ? 'selected' : '' }}>{{ $authority->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">İcraçı <span class="text-danger">*</span></label>
                            <select name="executor_id" class="form-select" required>
                                <option value="">Seç</option>
                                @foreach($executors as $executor)
                                    <option value="{{ $executor->id }}" {{ old('executor_id') == $executor->id ? 'selected' : '' }}>
                                        {{ $executor->name }} {{ $executor->department ? '- ' . $executor->department->name : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">İcra müddəti</label>
                            <input type="date" name="execution_deadline" class="form-control" value="{{ old('execution_deadline') }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Qısa məzmun <span class="text-danger">*</span></label>
                            <textarea name="summary" class="form-control" rows="3" required>{{ old('summary') }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tapşırığın nömrəsi</label>
                            <input type="text" name="task_number" class="form-control" value="{{ old('task_number') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Qeyd</label>
                            <select name="execution_note_id" class="form-select">
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
                            <textarea name="task_description" class="form-control" rows="2">{{ old('task_description') }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Əlaqəli sənədin nömrəsi</label>
                            <input type="text" name="related_document_number" class="form-control" value="{{ old('related_document_number') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Əlaqəli sənədin tarixi</label>
                            <input type="date" name="related_document_date" class="form-control" value="{{ old('related_document_date') }}">
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
function exportToExcel() {
    const params = new URLSearchParams(new FormData(document.querySelector('#filterForm')));
    window.location.href = "{{ route('legal-acts.export.excel') }}?" + params.toString();
}

function exportToWord() {
    const params = new URLSearchParams(new FormData(document.querySelector('#filterForm')));
    window.location.href = "{{ route('legal-acts.export.word') }}?" + params.toString();
}

async function showDetails(id) {
    const data = await fetchJson(`/legal-acts/${id}`);
    if (!data) return;
    
    document.getElementById('showModalBody').innerHTML = `
        <table class="table table-bordered detail-table mb-0">
            <tr><th width="30%">Sənədin növü</th><td>${escapeHtml(data.act_type || '-')}</td></tr>
            <tr><th>Sənədin nömrəsi</th><td class="fw-bold">${escapeHtml(data.legal_act_number || '-')}</td></tr>
            <tr><th>Sənədin tarixi</th><td>${escapeHtml(data.legal_act_date || '-')}</td></tr>
            <tr><th>Qısa məzmun</th><td style="white-space: pre-wrap">${escapeHtml(data.summary || '-')}</td></tr>
            <tr><th>Kim qəbul edib</th><td>${escapeHtml(data.issuing_authority || '-')}</td></tr>
            <tr><th>İcraçı</th><td>${escapeHtml(data.executor || '-')}</td></tr>
            <tr><th>İcraçının vəzifəsi</th><td>${escapeHtml(data.executor_position || '-')}</td></tr>
            <tr><th>İcraçının bölməsi</th><td>${escapeHtml(data.executor_department || '-')}</td></tr>
            <tr><th>Tapşırığın nömrəsi</th><td>${escapeHtml(data.task_number || '-')}</td></tr>
            <tr><th>Tapşırıq</th><td style="white-space: pre-wrap">${escapeHtml(data.task_description || '-')}</td></tr>
            <tr><th>İcra müddəti</th><td>${escapeHtml(data.execution_deadline || '-')}</td></tr>
            <tr><th>Qeyd</th><td style="white-space: pre-wrap">${escapeHtml(data.execution_note || '-')}</td></tr>
            <tr><th>Əlaqəli sənəd nömrəsi</th><td>${escapeHtml(data.related_document_number || '-')}</td></tr>
            <tr><th>Əlaqəli sənəd tarixi</th><td>${escapeHtml(data.related_document_date || '-')}</td></tr>
            <tr><th>Daxil edən</th><td>${escapeHtml(data.inserted_user || '-')}</td></tr>
            <tr><th>Yaradılma tarixi</th><td>${escapeHtml(data.created_at || '-')}</td></tr>
        </table>
    `;
    new bootstrap.Modal(document.getElementById('showModal')).show();
}

async function editRecord(id) {
    document.getElementById('editModalBody').innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status"></div>
            <p class="mt-2 text-muted">Yüklənir...</p>
        </div>
    `;
    new bootstrap.Modal(document.getElementById('editModal')).show();
    
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
                <input type="date" name="legal_act_date" class="form-control" value="${data.legal_act_date || ''}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Sənədin növü <span class="text-danger">*</span></label>
                <select name="act_type_id" class="form-select" required>
                    <option value="">Seç</option>
                    ${buildOptions(data.act_types || [], data.act_type_id)}
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Kim qəbul edib <span class="text-danger">*</span></label>
                <select name="issued_by_id" class="form-select" required>
                    <option value="">Seç</option>
                    ${buildOptions(data.authorities || [], data.issued_by_id)}
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">İcraçı <span class="text-danger">*</span></label>
                <select name="executor_id" class="form-select" required>
                    <option value="">Seç</option>
                    ${buildExecutorOptions(data.executors || [], data.executor_id)}
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">İcra müddəti</label>
                <input type="date" name="execution_deadline" class="form-control" value="${data.execution_deadline || ''}">
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
                <select name="execution_note_id" class="form-select">
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
                <input type="date" name="related_document_date" class="form-control" value="${data.related_document_date || ''}">
            </div>
        </div>
    `;
    
    document.getElementById('editForm').action = `/legal-acts/${id}`;
}

function deleteRecord(id) {
    if (confirm('Bu sənədi silmək istədiyinizə əminsiniz?')) {
        const form = document.getElementById('deleteForm');
        form.action = `/legal-acts/${id}`;
        form.submit();
    }
}

@if($errors->any() && old('_token'))
    document.addEventListener('DOMContentLoaded', function() {
        new bootstrap.Modal(document.getElementById('createModal')).show();
    });
@endif
</script>
@endpush