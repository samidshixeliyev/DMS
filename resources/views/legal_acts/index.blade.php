@extends('layouts.app')

@section('title', 'Hüquqi Aktlar')

@push('styles')
    <style>
        .row-overdue td { background-color: #fef2f2 !important; }
        .row-overdue:hover td { background-color: #fee2e2 !important; }
        .row-overdue td:first-child { box-shadow: inset 3px 0 0 #dc2626; }
        .row-warning td { background-color: #fefce8 !important; }
        .row-warning:hover td { background-color: #fef9c3 !important; }
        .row-warning td:first-child { box-shadow: inset 3px 0 0 #ca8a04; }
        .row-executed td { background-color: #f0fdf4 !important; }
        .row-executed:hover td { background-color: #dcfce7 !important; }
        .row-executed td:first-child { box-shadow: inset 3px 0 0 #16a34a; }

        .filter-row .flatpickr-input { background: #fff !important; cursor: pointer; }

        #legalActsTable thead th { text-align: center !important; vertical-align: middle !important; font-weight: 700; white-space: nowrap; color: #fff !important; border: 1px solid rgba(255, 255, 255, 0.18) !important; }
        #legalActsTable thead tr.band-header th { font-size: 0.82rem; padding: 0.6rem; letter-spacing: 0.3px; }
        #legalActsTable thead tr.sub-header th { font-size: 0.74rem; font-weight: 600; padding: 0.5rem 0.45rem; }
        th.bg-band-doc { background: #1e3a5f !important; } th.bg-band-doc-sub { background: #2a5298 !important; }
        th.bg-band-task { background: #065f46 !important; } th.bg-band-task-sub { background: #10a37f !important; }
        th.bg-band-exec { background: #5b21b6 !important; } th.bg-band-exec-sub { background: #7c4ddb !important; }
        th.bg-band-icra { background: #92400e !important; } th.bg-band-icra-sub { background: #d97706 !important; }
        th.bg-band-actions { background: #374151 !important; }

        #legalActsTable { min-width: 2100px; }
        #legalActsTable tbody td { font-size: 0.82rem; padding: 0.5rem 0.65rem; vertical-align: middle; text-align: center; }
        #legalActsTable tbody td.wrap-cell { white-space: normal; word-break: break-word; text-align: left; min-width: 180px; max-width: 280px; }
        #legalActsTable tbody tr:nth-child(even):not([class*="row-"]) { background-color: #f8fafc; }
        #legalActsTable td:last-child { white-space: nowrap; position: sticky; right: 0; background-color: #fff; box-shadow: -2px 0 4px rgba(0,0,0,0.06); z-index: 999 !important; }
        .row-overdue td:last-child { background-color: #fef2f2 !important; }
        .row-warning td:last-child { background-color: #fefce8 !important; }
        .row-executed td:last-child { background-color: #f0fdf4 !important; }
        #legalActsTable tbody tr:nth-child(even):not([class*="row-"]) td:last-child { background-color: #f8fafc; }

        .action-btns { display: flex; flex-direction: column; gap: 4px; justify-content: center; align-items: center; }
        .action-btns .btn { width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center; border-radius: 8px; }

        .timeline { position: relative; padding-left: 2rem; }
        .timeline::before { content: ''; position: absolute; left: 8px; top: 0; bottom: 0; width: 2px; background: #e2e8f0; }
        .timeline-item { position: relative; margin-bottom: 1.25rem; }
        .timeline-item::before { content:''; position:absolute; left:-1.65rem; top:4px; width:12px; height:12px; border-radius:50%; background:var(--accent, #3b82f6); border:2px solid #fff; box-shadow:0 0 0 2px var(--accent, #3b82f6); }
        .timeline-item .tl-date { font-size:0.72rem; color:#94a3b8; font-weight:600; }
        .timeline-item .tl-user { font-size:0.78rem; color:#64748b; }
        .timeline-item .tl-note { font-size:0.85rem; font-weight:600; color:#1e293b; margin-top:2px; }
        .timeline-item .tl-custom { font-size:0.8rem; color:#64748b; margin-top:2px; font-style:italic; }
        .timeline-item .tl-attachment { font-size:0.78rem; margin-top:6px; }
    </style>
@endpush

@section('content')
    <div class="page-header">
        <h2><i class="bi bi-file-text me-2"></i>Hüquqi Aktlar</h2>
        @if($canManage)
        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createModal">
            <i class="bi bi-plus-circle me-1"></i> Yeni əlavə et
        </button>
        @endif
    </div>

    @if(session('success'))<div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle me-1"></i> {{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif
    @if($errors->any())<div class="alert alert-danger alert-dismissible fade show"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif

    {{-- Filters --}}
    <div class="card filter-card mb-3">
        <div class="card-header" data-bs-toggle="collapse" data-bs-target="#filterBody" aria-expanded="true"><h5 class="mb-0 d-flex align-items-center"><i class="bi bi-funnel me-2"></i> Filtrlər <i class="bi bi-chevron-down ms-auto"></i></h5></div>
        <div class="collapse show" id="filterBody"><div class="card-body filter-row"><div class="row g-2">
            <div class="col-xl-2 col-md-3"><label class="form-label">Sənədin nömrəsi</label><input type="text" id="filter_legal_act_number" class="form-control filter-el" placeholder="Axtar..."></div>
            <div class="col-xl-2 col-md-3"><label class="form-label">Qısa məzmun</label><input type="text" id="filter_summary" class="form-control filter-el" placeholder="Axtar..."></div>
            <div class="col-xl-2 col-md-3"><label class="form-label">Sənədin növü</label><select id="filter_act_type" class="form-select filter-select"><option value="">Hamısı</option>@foreach($actTypes as $type)<option value="{{ $type->id }}">{{ $type->name }}</option>@endforeach</select></div>
            <div class="col-xl-2 col-md-3"><label class="form-label">Kim qəbul edib</label><select id="filter_issued_by" class="form-select filter-select"><option value="">Hamısı</option>@foreach($issuingAuthorities as $a)<option value="{{ $a->id }}">{{ $a->name }}</option>@endforeach</select></div>
            <div class="col-xl-2 col-md-3"><label class="form-label">İcraçı</label><select id="filter_executor" class="form-select filter-select"><option value="">Hamısı</option>@foreach($executors as $e)<option value="{{ $e->id }}">{{ $e->name }}</option>@endforeach</select></div>
            <div class="col-xl-2 col-md-3"><label class="form-label">Sənəd tarixi</label><input type="text" id="filter_date_range" class="form-control filter-el" placeholder="Tarix aralığı..." readonly></div>
            <div class="col-xl-2 col-md-3"><label class="form-label">İcra müddəti</label><input type="text" id="filter_deadline_range" class="form-control filter-el" placeholder="Tarix aralığı..." readonly></div>
            <div class="col-xl-2 col-md-3"><label class="form-label">Müddət statusu</label><select id="filter_deadline_status" class="form-select filter-select"><option value="">Hamısı</option><option value="expired">Müddəti bitib</option><option value="0day">Son gün</option><option value="1day">1 gün</option><option value="2days">2 gün</option><option value="3days">3 gün</option><option value="executed">İcra olunub</option></select></div>
            <div class="col-xl-2 col-md-3"><label class="form-label">Tapşırıq №</label><input type="text" id="filter_task_number" class="form-control filter-el" placeholder="Axtar..."></div>
            <div class="col-xl-2 col-md-3"><label class="form-label">Bölmə</label><select id="filter_department" class="form-select filter-select"><option value="">Hamısı</option>@foreach($departments as $d)<option value="{{ $d->id }}">{{ $d->name }}</option>@endforeach</select></div>
            <div class="col-xl-1 col-md-3 d-flex align-items-end gap-2"><button id="filtersSearchBtn" class="btn btn-primary"><i class="bi bi-search"></i></button><button id="filtersResetBtn" class="btn btn-secondary"><i class="bi bi-x-circle"></i></button></div>
        </div></div></div>
    </div>

    {{-- Table --}}
    <div class="card"><div class="card-body p-0"><div style="overflow-x:auto;">
        <table class="table table-hover table-bordered mb-0" id="legalActsTable" style="width:100%">
            <thead>
                <tr class="band-header">
                    <th colspan="5" class="bg-band-doc">Sənəd Məlumatları</th><th colspan="2" class="bg-band-task">Tapşırıq</th><th colspan="4" class="bg-band-exec">İcraçı Məlumatları</th><th colspan="3" class="bg-band-icra">İcra Məlumatları</th><th rowspan="2" class="bg-band-actions" style="position:sticky;right:0;z-index:4;"></th>
                </tr>
                <tr class="sub-header">
                    <th class="bg-band-doc-sub">Növü</th><th class="bg-band-doc-sub">Nömrəsi</th><th class="bg-band-doc-sub">Tarixi</th><th class="bg-band-doc-sub">Kim Qəbul Edib</th><th class="bg-band-doc-sub">Qısa Məzmun</th>
                    <th class="bg-band-task-sub">Tapşırıq №</th><th class="bg-band-task-sub">Tapşırıq</th>
                    <th class="bg-band-exec-sub">İcraçı</th><th class="bg-band-exec-sub">Bölmə</th><th class="bg-band-exec-sub">İcra Müddəti</th><th class="bg-band-exec-sub">Qeyd</th>
                    <th class="bg-band-icra-sub">Sənəd №</th><th class="bg-band-icra-sub">Sənəd Tarixi</th><th class="bg-band-icra-sub">Daxil Edən</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div></div></div>

    {{-- Create Modal --}}
    <div class="modal fade" id="createModal" tabindex="-1"><div class="modal-dialog modal-lg"><div class="modal-content">
        <form action="{{ route('legal-acts.store') }}" method="POST">@csrf
            <div class="modal-header"><h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Yeni sənəd</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body"><div class="row g-3">
                <div class="col-md-6"><label class="form-label">Sənədin nömrəsi <span class="text-danger">*</span></label><input type="text" name="legal_act_number" class="form-control" value="{{ old('legal_act_number') }}" required></div>
                <div class="col-md-6"><label class="form-label">Sənədin tarixi <span class="text-danger">*</span></label><input type="text" name="legal_act_date" class="form-control modal-datepicker" value="{{ old('legal_act_date') }}" required></div>
                <div class="col-md-6"><label class="form-label">Növü <span class="text-danger">*</span></label><select name="act_type_id" class="form-select modal-select2" required><option value="">Seç</option>@foreach($actTypes as $t)<option value="{{ $t->id }}" {{ old('act_type_id')==$t->id?'selected':'' }}>{{ $t->name }}</option>@endforeach</select></div>
                <div class="col-md-6"><label class="form-label">Kim qəbul edib <span class="text-danger">*</span></label><select name="issued_by_id" class="form-select modal-select2" required><option value="">Seç</option>@foreach($issuingAuthorities as $a)<option value="{{ $a->id }}" {{ old('issued_by_id')==$a->id?'selected':'' }}>{{ $a->name }}</option>@endforeach</select></div>
                <div class="col-md-6"><label class="form-label">Əsas icraçı <span class="text-danger">*</span></label><select name="main_executor_id" class="form-select modal-select2" required><option value="">Seç</option>@foreach($executors as $e)<option value="{{ $e->id }}" {{ old('main_executor_id')==$e->id?'selected':'' }}>{{ $e->name }}{{ $e->department?' — '.$e->department->name:'' }}</option>@endforeach</select></div>
                <div class="col-md-6"><label class="form-label">Köməkçi icraçı</label><select name="helper_executor_id" class="form-select modal-select2"><option value="">Seç</option>@foreach($executors as $e)<option value="{{ $e->id }}" {{ old('helper_executor_id')==$e->id?'selected':'' }}>{{ $e->name }}{{ $e->department?' — '.$e->department->name:'' }}</option>@endforeach</select></div>
                <div class="col-md-6"><label class="form-label">İcra müddəti</label><input type="text" name="execution_deadline" class="form-control modal-datepicker" value="{{ old('execution_deadline') }}"></div>
                <div class="col-md-6"><label class="form-label">Tapşırıq №</label><input type="text" name="task_number" class="form-control" value="{{ old('task_number') }}"></div>
                <div class="col-12"><label class="form-label">Qısa məzmun <span class="text-danger">*</span></label><textarea name="summary" class="form-control" rows="3" required>{{ old('summary') }}</textarea></div>
                <div class="col-12"><label class="form-label">Tapşırıq</label><textarea name="task_description" class="form-control" rows="2">{{ old('task_description') }}</textarea></div>
                <div class="col-md-6"><label class="form-label">Əlaqəli sənəd №</label><input type="text" name="related_document_number" class="form-control" value="{{ old('related_document_number') }}"></div>
                <div class="col-md-6"><label class="form-label">Əlaqəli sənəd tarixi</label><input type="text" name="related_document_date" class="form-control modal-datepicker" value="{{ old('related_document_date') }}"></div>
            </div></div>
            <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İmtina</button><button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Yarat</button></div>
        </form>
    </div></div></div>

    {{-- Edit Modal --}}
    <div class="modal fade" id="editModal" tabindex="-1"><div class="modal-dialog modal-lg"><div class="modal-content">
        <form id="editForm" method="POST">@csrf @method('PUT')
            <div class="modal-header"><h5 class="modal-title"><i class="bi bi-pencil me-2"></i>Redaktə</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body" id="editModalBody"><div class="text-center py-4"><div class="spinner-border text-primary"></div></div></div>
            <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İmtina</button><button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Yenilə</button></div>
        </form>
    </div></div></div>

    {{-- Show Modal --}}
    <div class="modal fade" id="showModal" tabindex="-1"><div class="modal-dialog modal-xl"><div class="modal-content">
        <div class="modal-header"><h5 class="modal-title"><i class="bi bi-info-circle me-2"></i>Sənəd məlumatı</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body" id="showModalBody"></div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bağla</button></div>
    </div></div></div>

    {{-- Preview Modal (shared partial) --}}
    @include('partials.preview-modal')

    <form id="deleteForm" method="POST" style="display:none;">@csrf @method('DELETE')</form>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/mammoth/1.6.0/mammoth.browser.min.js"></script>
<script src="{{ asset('js/document-preview.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var $p = $('.container-fluid');
    ['#filter_act_type','#filter_issued_by','#filter_executor','#filter_deadline_status','#filter_department'].forEach(function(s){$(s).select2({theme:'bootstrap-5',dropdownParent:$p,placeholder:$(s).find('option:first').text(),allowClear:true,width:'100%'});});
    var fpCfg={mode:'range',dateFormat:'d.m.Y',locale:flatpickr.l10ns.az,allowInput:false};fpCfg.locale.rangeSeparator=' — ';
    var fpDate=flatpickr('#filter_date_range',Object.assign({},fpCfg)),fpDead=flatpickr('#filter_deadline_range',Object.assign({},fpCfg));
    function rr(fp){var r={from:'',to:''};if(fp.selectedDates.length>0){var a=fp.selectedDates[0],b=fp.selectedDates.length>1?fp.selectedDates[1]:a;r.from=a.getFullYear()+'-'+String(a.getMonth()+1).padStart(2,'0')+'-'+String(a.getDate()).padStart(2,'0');r.to=b.getFullYear()+'-'+String(b.getMonth()+1).padStart(2,'0')+'-'+String(b.getDate()).padStart(2,'0');}return r;}
    function gfp(){var p={},v;if((v=$('#filter_legal_act_number').val())&&v.trim())p['col[legal_act_number]']=v.trim();if((v=$('#filter_summary').val())&&v.trim())p['col[summary]']=v.trim();if((v=$('#filter_act_type').val()))p['col[act_type_id]']=v;if((v=$('#filter_issued_by').val()))p['col[issued_by_id]']=v;if((v=$('#filter_executor').val()))p['col[executor_id]']=v;if((v=$('#filter_deadline_status').val()))p['col[deadline_status]']=v;if((v=$('#filter_task_number').val())&&v.trim())p['col[task_number]']=v.trim();if((v=$('#filter_department').val()))p['col[department_id]']=v;var d=rr(fpDate);if(d.from)p['col[legal_act_date_from]']=d.from;if(d.to)p['col[legal_act_date_to]']=d.to;var l=rr(fpDead);if(l.from)p['col[deadline_from]']=l.from;if(l.to)p['col[deadline_to]']=l.to;return p;}

    var table=$('#legalActsTable').DataTable({processing:true,serverSide:true,
        ajax:{url:"{{ route('legal-acts.load') }}",type:'POST',headers:{'X-CSRF-TOKEN':csrfToken},data:function(d){var f=gfp();d.col={};Object.keys(f).forEach(function(k){var m=k.match(/^col\[(.+)\]$/);if(m)d.col[m[1]]=f[k];});}},
        columns:[
            {data:'actType',className:'text-center',render:function(d){return(!d||d==='-')?'-':'<span class="badge" style="background:var(--accent-dark,#1e3a5f)">'+escapeHtml(d)+'</span>';}},
            {data:'legalActNumber',className:'fw-semibold text-center'},{data:'legalActDate',className:'text-center'},{data:'issuingAuthority'},{data:'summary',className:'wrap-cell'},
            {data:'taskNumber',className:'text-center'},{data:'taskDescription',className:'wrap-cell'},
            {data:'executor'},{data:'department'},{data:'deadlineHtml',className:'text-center'},{data:'noteHtml'},
            {data:'relatedDocNumber',className:'text-center'},{data:'relatedDocDate',className:'text-center'},{data:'insertedUser'},
            {data:null,orderable:false,searchable:false,render:function(d){var h='<div class="action-btns">';h+='<button class="btn btn-sm btn-info" title="Bax" onclick="showDetails('+d.id+')"><i class="bi bi-eye"></i></button>';if(d.canEdit)h+='<button class="btn btn-sm btn-warning" title="Redaktə" onclick="editRecord('+d.id+')"><i class="bi bi-pencil"></i></button>';if(d.canDelete)h+='<button class="btn btn-sm btn-danger" title="Sil" onclick="deleteRecord('+d.id+')"><i class="bi bi-trash"></i></button>';return h+'</div>';}}
        ],
        order:[[2,'desc']],pageLength:25,lengthMenu:[10,25,50,100],autoWidth:false,orderCellsTop:true,
        dom:'<"d-flex justify-content-between align-items-center flex-wrap px-3 pt-2"lB>rt<"d-flex justify-content-between align-items-center flex-wrap px-3 pb-2"ip>',
        buttons:[{extend:'colvis',text:'<i class="bi bi-eye me-1"></i> Sütunlar',className:'btn btn-secondary btn-sm',columns:':not(:last-child)'},{text:'<i class="bi bi-file-earmark-excel me-1"></i> Excel',className:'btn btn-primary btn-sm',action:function(){xf('excel');}},{text:'<i class="bi bi-file-earmark-word me-1"></i> Word',className:'btn btn-info btn-sm',action:function(){xf('word');}}],
        language:{paginate:{previous:"&laquo;",next:"&raquo;"},emptyTable:"Məlumat yoxdur",info:"_START_-_END_ / _TOTAL_",infoEmpty:"Məlumat yoxdur",lengthMenu:"_MENU_ nəticə",processing:"Yüklənir...",zeroRecords:"Tapılmadı"}
    });
    $('#filtersSearchBtn').on('click',function(){table.ajax.reload();});
    $('#filtersResetBtn').on('click',function(){$('#filter_legal_act_number,#filter_summary,#filter_task_number').val('');$('#filter_act_type,#filter_issued_by,#filter_executor,#filter_deadline_status,#filter_department').val(null).trigger('change');fpDate.clear();fpDead.clear();table.ajax.reload();});
    $p.on('keydown','input.filter-el',function(e){if(e.key==='Enter'){e.preventDefault();table.ajax.reload();}});
    window.xf=function(t){var p=new URLSearchParams(gfp());window.location.href=(t==='excel'?"{{ route('legal-acts.export.excel') }}":"{{ route('legal-acts.export.word') }}")+'?'+p.toString();};
    var $cm=$('#createModal');
    $cm.on('shown.bs.modal',function(){$(this).find('.modal-select2').each(function(){if(!$(this).data('select2'))$(this).select2({theme:'bootstrap-5',dropdownParent:$cm.find('.modal-body'),placeholder:'Seç',allowClear:true,width:'100%'});});$(this).find('.modal-datepicker').each(function(){if(!this._flatpickr)flatpickr(this,{dateFormat:'Y-m-d',locale:flatpickr.l10ns.az,allowInput:true});});});
    $cm.on('hidden.bs.modal',function(){$(this).find('.modal-select2').each(function(){if($(this).data('select2'))$(this).select2('destroy');});$(this).find('.modal-datepicker').each(function(){if(this._flatpickr)this._flatpickr.destroy();});});
});

async function showDetails(id) {
    var data = await fetchJson('/legal-acts/'+id); if(!data) return;
    var logsHtml='<p class="text-muted fst-italic">Hələ status dəyişikliyi yoxdur.</p>';
    if(data.status_logs&&data.status_logs.length>0){logsHtml='<div class="timeline">';data.status_logs.forEach(function(log){
        logsHtml+='<div class="timeline-item"><div class="tl-date">'+escapeHtml(log.date||'')+'</div><div class="tl-user"><i class="bi bi-person me-1"></i>'+escapeHtml(log.user||'')+'</div><div class="tl-note">'+escapeHtml(log.note||'')+'</div>'+(log.custom_note?'<div class="tl-custom">"'+escapeHtml(log.custom_note)+'"</div>':'')+buildAttachmentHtml(log.attachments)+'</div>';
    });logsHtml+='</div>';}
    document.getElementById('showModalBody').innerHTML='<div class="row"><div class="col-lg-7"><h6 class="fw-bold mb-3"><i class="bi bi-file-text me-1"></i> Sənəd</h6><table class="table table-bordered detail-table mb-0"><tr><th width="35%">Növ</th><td>'+escapeHtml(data.act_type||'-')+'</td></tr><tr><th>Nömrə</th><td class="fw-bold">'+escapeHtml(data.legal_act_number||'-')+'</td></tr><tr><th>Tarix</th><td>'+escapeHtml(data.legal_act_date||'-')+'</td></tr><tr><th>Kim qəbul edib</th><td>'+escapeHtml(data.issuing_authority||'-')+'</td></tr><tr><th>Qısa məzmun</th><td style="white-space:pre-wrap">'+escapeHtml(data.summary||'-')+'</td></tr><tr><th>Tapşırıq №</th><td>'+escapeHtml(data.task_number||'-')+'</td></tr><tr><th>Tapşırıq</th><td style="white-space:pre-wrap">'+escapeHtml(data.task_description||'-')+'</td></tr><tr><th>Əsas icraçı</th><td>'+escapeHtml(data.main_executor||'-')+(data.main_executor_department?'<br><small class="text-muted">'+escapeHtml(data.main_executor_department)+'</small>':'')+'</td></tr><tr><th>Köməkçi icraçı</th><td>'+escapeHtml(data.helper_executor||'-')+(data.helper_executor_department?'<br><small class="text-muted">'+escapeHtml(data.helper_executor_department)+'</small>':'')+'</td></tr><tr><th>İcra müddəti</th><td>'+escapeHtml(data.execution_deadline||'-')+'</td></tr><tr><th>Əlaqəli sənəd</th><td>'+escapeHtml(data.related_document_number||'-')+'</td></tr><tr><th>Daxil edən</th><td>'+escapeHtml(data.inserted_user||'-')+'</td></tr><tr><th>Yaradılma</th><td>'+escapeHtml(data.created_at||'-')+'</td></tr></table></div><div class="col-lg-5"><h6 class="fw-bold mb-3"><i class="bi bi-clock-history me-1"></i> Status Tarixçəsi</h6>'+logsHtml+'</div></div>';
    new bootstrap.Modal(document.getElementById('showModal')).show();
}

async function editRecord(id){var data=await fetchJson('/legal-acts/'+id+'/edit');if(!data)return;var $m=$('#editModal');var h='<div class="row g-3"><div class="col-md-6"><label class="form-label">Nömrə *</label><input type="text" name="legal_act_number" class="form-control" value="'+escapeHtml(data.legal_act_number||'')+'" required></div><div class="col-md-6"><label class="form-label">Tarix *</label><input type="text" name="legal_act_date" class="form-control edit-datepicker" value="'+escapeHtml(data.legal_act_date||'')+'" required></div><div class="col-md-6"><label class="form-label">Növ *</label><select name="act_type_id" class="form-select edit-select2" required><option value="">Seç</option>';if(data.act_types)data.act_types.forEach(function(t){h+='<option value="'+t.id+'"'+(t.id==data.act_type_id?' selected':'')+'>'+escapeHtml(t.name)+'</option>';});h+='</select></div><div class="col-md-6"><label class="form-label">Kim qəbul edib *</label><select name="issued_by_id" class="form-select edit-select2" required><option value="">Seç</option>';if(data.authorities)data.authorities.forEach(function(a){h+='<option value="'+a.id+'"'+(a.id==data.issued_by_id?' selected':'')+'>'+escapeHtml(a.name)+'</option>';});h+='</select></div><div class="col-md-6"><label class="form-label">Əsas icraçı *</label><select name="main_executor_id" class="form-select edit-select2" required><option value="">Seç</option>';if(data.executors)data.executors.forEach(function(e){var d=e.department?' — '+e.department.name:'';h+='<option value="'+e.id+'"'+(e.id==data.main_executor_id?' selected':'')+'>'+escapeHtml(e.name+d)+'</option>';});h+='</select></div><div class="col-md-6"><label class="form-label">Köməkçi icraçı</label><select name="helper_executor_id" class="form-select edit-select2"><option value="">Seç</option>';if(data.executors)data.executors.forEach(function(e){var d=e.department?' — '+e.department.name:'';h+='<option value="'+e.id+'"'+(e.id==data.helper_executor_id?' selected':'')+'>'+escapeHtml(e.name+d)+'</option>';});h+='</select></div><div class="col-md-6"><label class="form-label">İcra müddəti</label><input type="text" name="execution_deadline" class="form-control edit-datepicker" value="'+escapeHtml(data.execution_deadline||'')+'"></div><div class="col-md-6"><label class="form-label">Tapşırıq №</label><input type="text" name="task_number" class="form-control" value="'+escapeHtml(data.task_number||'')+'"></div><div class="col-12"><label class="form-label">Qısa məzmun *</label><textarea name="summary" class="form-control" rows="3" required>'+escapeHtml(data.summary||'')+'</textarea></div><div class="col-12"><label class="form-label">Tapşırıq</label><textarea name="task_description" class="form-control" rows="2">'+escapeHtml(data.task_description||'')+'</textarea></div><div class="col-md-6"><label class="form-label">Əlaqəli sənəd №</label><input type="text" name="related_document_number" class="form-control" value="'+escapeHtml(data.related_document_number||'')+'"></div><div class="col-md-6"><label class="form-label">Əlaqəli sənəd tarixi</label><input type="text" name="related_document_date" class="form-control edit-datepicker" value="'+escapeHtml(data.related_document_date||'')+'"></div></div>';document.getElementById('editModalBody').innerHTML=h;document.getElementById('editForm').action='/legal-acts/'+data.id;$m.find('.edit-select2').each(function(){$(this).select2({theme:'bootstrap-5',dropdownParent:$m.find('.modal-body'),placeholder:'Seç',allowClear:true,width:'100%'});});$m.find('.edit-datepicker').each(function(){flatpickr(this,{dateFormat:'Y-m-d',locale:flatpickr.l10ns.az,allowInput:true});});new bootstrap.Modal(document.getElementById('editModal')).show();}
function deleteRecord(id){if(confirm('Silmək istədiyinizə əminsiniz?')){var f=document.getElementById('deleteForm');f.action='/legal-acts/'+id;f.submit();}}
</script>
@endpush