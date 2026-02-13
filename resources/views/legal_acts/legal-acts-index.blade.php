@extends('layouts.app')

@section('title', 'Legal Acts')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <h2>Legal Acts</h2>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Filters --}}
    <div class="card mb-3">
        <div class="card-header">
            <h5 class="mb-0">Filters</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('legal-acts.index') }}" id="filterForm">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Document Number</label>
                        <input type="text" name="document_number" class="form-control" 
                               value="{{ request('document_number') }}" placeholder="Search...">
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label">Title</label>
                        <input type="text" name="title" class="form-control" 
                               value="{{ request('title') }}" placeholder="Search...">
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label">Issuing Authority</label>
                        <select name="issued_by_id" class="form-select">
                            <option value="">All</option>
                            @foreach($issuingAuthorities as $authority)
                                <option value="{{ $authority->id }}" 
                                    {{ request('issued_by_id') == $authority->id ? 'selected' : '' }}>
                                    {{ $authority->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label">Executor</label>
                        <select name="executor_id" class="form-select">
                            <option value="">All</option>
                            @foreach($executors as $executor)
                                <option value="{{ $executor->id }}" 
                                    {{ request('executor_id') == $executor->id ? 'selected' : '' }}>
                                    {{ $executor->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label">Category</label>
                        <select name="category_id" class="form-select">
                            <option value="">All</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" 
                                    {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select name="status_id" class="form-select">
                            <option value="">All</option>
                            @foreach($statuses as $status)
                                <option value="{{ $status->id }}" 
                                    {{ request('status_id') == $status->id ? 'selected' : '' }}>
                                    {{ $status->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label">Date From</label>
                        <input type="date" name="document_date_from" class="form-control" 
                               value="{{ request('document_date_from') }}">
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label">Date To</label>
                        <input type="date" name="document_date_to" class="form-control" 
                               value="{{ request('document_date_to') }}">
                    </div>
                </div>
                
                <div class="row mt-3">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search"></i> Filter
                        </button>
                        <a href="{{ route('legal-acts.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Clear
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Action Buttons --}}
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Legal Acts List ({{ $legalActs->total() }} records)</h5>
            <div>
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createModal">
                    <i class="bi bi-plus-circle"></i> Add New
                </button>
                
                <button type="button" class="btn btn-primary" onclick="exportToExcel()">
                    <i class="bi bi-file-earmark-excel"></i> Export Excel
                </button>
                
                <button type="button" class="btn btn-info" onclick="exportToWord()">
                    <i class="bi bi-file-earmark-word"></i> Export Word
                </button>
            </div>
        </div>
        
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Document Number</th>
                            <th>Document Date</th>
                            <th>Title</th>
                            <th>Issuing Authority</th>
                            <th>Executor</th>
                            <th>Category</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($legalActs as $legalAct)
                            <tr>
                                <td>{{ $legalAct->id }}</td>
                                <td>{{ $legalAct->document_number }}</td>
                                <td>{{ $legalAct->document_date?->format('d.m.Y') }}</td>
                                <td>{{ Str::limit($legalAct->title, 50) }}</td>
                                <td>{{ $legalAct->issuingAuthority?->name }}</td>
                                <td>{{ $legalAct->executor?->name }}</td>
                                <td>{{ $legalAct->category?->name }}</td>
                                <td>
                                    <span class="badge bg-secondary">
                                        {{ $legalAct->status?->name }}
                                    </span>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-info" 
                                            onclick="showDetails({{ $legalAct->id }})">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    
                                    <button type="button" class="btn btn-sm btn-warning" 
                                            onclick="editRecord({{ $legalAct->id }})">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    
                                    <button type="button" class="btn btn-sm btn-danger" 
                                            onclick="deleteRecord({{ $legalAct->id }})">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">No records found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-3">
                {{ $legalActs->links() }}
            </div>
        </div>
    </div>
</div>

{{-- Create Modal --}}
<div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('legal-acts.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Create Legal Act</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Document Number *</label>
                            <input type="text" name="document_number" class="form-control" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Document Date *</label>
                            <input type="date" name="document_date" class="form-control" required>
                        </div>
                        
                        <div class="col-12">
                            <label class="form-label">Title *</label>
                            <textarea name="title" class="form-control" rows="3" required></textarea>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Issuing Authority *</label>
                            <select name="issued_by_id" class="form-select" required>
                                <option value="">Select Authority</option>
                                @foreach($issuingAuthorities as $authority)
                                    <option value="{{ $authority->id }}">{{ $authority->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Executor *</label>
                            <select name="executor_id" class="form-select" required>
                                <option value="">Select Executor</option>
                                @foreach($executors as $executor)
                                    <option value="{{ $executor->id }}">{{ $executor->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Category *</label>
                            <select name="category_id" class="form-select" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Status *</label>
                            <select name="status_id" class="form-select" required>
                                <option value="">Select Status</option>
                                @foreach($statuses as $status)
                                    <option value="{{ $status->id }}">{{ $status->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Execution Deadline</label>
                            <input type="date" name="execution_deadline" class="form-control">
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Upload Document</label>
                            <input type="file" name="file_path" class="form-control" accept=".pdf,.doc,.docx">
                        </div>
                        
                        <div class="col-12">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Create</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Edit Modal --}}
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="editForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Legal Act</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="editModalBody">
                    <!-- Content loaded via AJAX -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update</button>
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
                <h5 class="modal-title">Legal Act Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="showModalBody">
                <!-- Content loaded via AJAX -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- Delete Form --}}
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

function showDetails(id) {
    fetch(`/legal-acts/${id}`)
        .then(response => response.json())
        .then(data => {
            const content = `
                <table class="table table-bordered">
                    <tr><th width="30%">Document Number</th><td>${data.document_number || '-'}</td></tr>
                    <tr><th>Document Date</th><td>${data.document_date || '-'}</td></tr>
                    <tr><th>Title</th><td>${data.title || '-'}</td></tr>
                    <tr><th>Issuing Authority</th><td>${data.issuing_authority?.name || '-'}</td></tr>
                    <tr><th>Executor</th><td>${data.executor?.name || '-'}</td></tr>
                    <tr><th>Category</th><td>${data.category?.name || '-'}</td></tr>
                    <tr><th>Status</th><td>${data.status?.name || '-'}</td></tr>
                    <tr><th>Execution Deadline</th><td>${data.execution_deadline || '-'}</td></tr>
                    <tr><th>Notes</th><td>${data.notes || '-'}</td></tr>
                    <tr><th>Created At</th><td>${data.created_at || '-'}</td></tr>
                </table>
            `;
            document.getElementById('showModalBody').innerHTML = content;
            new bootstrap.Modal(document.getElementById('showModal')).show();
        });
}

function editRecord(id) {
    fetch(`/legal-acts/${id}/edit`)
        .then(response => response.json())
        .then(data => {
            const content = `
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Document Number *</label>
                        <input type="text" name="document_number" class="form-control" value="${data.document_number}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Document Date *</label>
                        <input type="date" name="document_date" class="form-control" value="${data.document_date}" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Title *</label>
                        <textarea name="title" class="form-control" rows="3" required>${data.title}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Issuing Authority *</label>
                        <select name="issued_by_id" class="form-select" required>
                            ${data.authorities.map(a => `<option value="${a.id}" ${a.id == data.issued_by_id ? 'selected' : ''}>${a.name}</option>`).join('')}
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Executor *</label>
                        <select name="executor_id" class="form-select" required>
                            ${data.executors.map(e => `<option value="${e.id}" ${e.id == data.executor_id ? 'selected' : ''}>${e.name}</option>`).join('')}
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Category *</label>
                        <select name="category_id" class="form-select" required>
                            ${data.categories.map(c => `<option value="${c.id}" ${c.id == data.category_id ? 'selected' : ''}>${c.name}</option>`).join('')}
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Status *</label>
                        <select name="status_id" class="form-select" required>
                            ${data.statuses.map(s => `<option value="${s.id}" ${s.id == data.status_id ? 'selected' : ''}>${s.name}</option>`).join('')}
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Execution Deadline</label>
                        <input type="date" name="execution_deadline" class="form-control" value="${data.execution_deadline || ''}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Upload New Document</label>
                        <input type="file" name="file_path" class="form-control" accept=".pdf,.doc,.docx">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="3">${data.notes || ''}</textarea>
                    </div>
                </div>
            `;
            document.getElementById('editModalBody').innerHTML = content;
            document.getElementById('editForm').action = `/legal-acts/${id}`;
            new bootstrap.Modal(document.getElementById('editModal')).show();
        });
}

function deleteRecord(id) {
    if (confirm('Are you sure you want to delete this record?')) {
        const form = document.getElementById('deleteForm');
        form.action = `/legal-acts/${id}`;
        form.submit();
    }
}
</script>
@endpush
