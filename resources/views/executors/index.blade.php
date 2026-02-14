@extends('layouts.app')

@section('title', 'Executors')

@section('content')
<div class="page-header">
    <h2><i class="bi bi-people me-2"></i>İcraçılar</h2>
    @if(in_array(auth()->user()->user_role, ['admin', 'manager']))
    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createModal">
        <i class="bi bi-plus-circle me-1"></i> Yeni əlavə et
    </button>
    @endif
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

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th style="width: 70px">ID</th>
                        <th>Name</th>
                        <th>Position</th>
                        <th>Department</th>
                        <th style="width: 150px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($executors as $executor)
                        <tr>
                            <td><span class="badge bg-secondary">{{ $executor->id }}</span></td>
                            <td>{{ $executor->name }}</td>
                            <td>{{ $executor->position }}</td>
                            <td>
                                @if($executor->department)
                                    <span class="badge" style="background: var(--primary-light)">{{ $executor->department->name }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <div class="action-btns">
                                    <button type="button" class="btn btn-sm btn-info" title="Bax"
                                            onclick="showDetails({{ $executor->id }})">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    @if(in_array(auth()->user()->user_role, ['admin', 'manager']))
                                    <button type="button" class="btn btn-sm btn-warning" title="Redaktə"
                                            onclick="editRecord({{ $executor->id }})">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    @endif
                                    @if(auth()->user()->user_role === 'admin')
                                    <button type="button" class="btn btn-sm btn-danger" title="Sil"
                                            onclick="deleteRecord({{ $executor->id }})">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <div class="empty-state">
                                    <i class="bi bi-people d-block"></i>
                                    <p class="mb-0">No executors found</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($executors->hasPages())
            <div class="p-3 border-top">
                {{ $executors->links() }}
            </div>
        @endif
    </div>
</div>

{{-- Create Modal --}}
<div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('executors.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Create Executor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Position <span class="text-danger">*</span></label>
                        <input type="text" name="position" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Department <span class="text-danger">*</span></label>
                        <select name="department_id" class="form-select" required>
                            <option value="">Select Department</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}">{{ $department->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Create</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Edit Modal --}}
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-pencil me-2"></i>Edit Executor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="edit_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Position <span class="text-danger">*</span></label>
                        <input type="text" name="position" id="edit_position" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Department <span class="text-danger">*</span></label>
                        <select name="department_id" id="edit_department_id" class="form-select" required>
                            <option value="">Select Department</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Show Modal --}}
<div class="modal fade" id="showModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-info-circle me-2"></i>Executor Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="showModalBody"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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
async function showDetails(id) {
    const data = await fetchJson(`/executors/${id}`);
    if (!data) return;
    
    document.getElementById('showModalBody').innerHTML = `
        <table class="table table-bordered detail-table mb-0">
            <tr><th width="35%">ID</th><td>${escapeHtml(String(data.id))}</td></tr>
            <tr><th>Name</th><td>${escapeHtml(data.name)}</td></tr>
            <tr><th>Position</th><td>${escapeHtml(data.position || '-')}</td></tr>
            <tr><th>Department</th><td>${escapeHtml(data.department || '-')}</td></tr>
            <tr><th>Created At</th><td>${escapeHtml(data.created_at || '-')}</td></tr>
        </table>
    `;
    new bootstrap.Modal(document.getElementById('showModal')).show();
}

async function editRecord(id) {
    const data = await fetchJson(`/executors/${id}/edit`);
    if (!data) return;
    
    document.getElementById('edit_name').value = data.name || '';
    document.getElementById('edit_position').value = data.position || '';
    
    // Populate department dropdown
    const select = document.getElementById('edit_department_id');
    select.innerHTML = '<option value="">Select Department</option>';
    if (data.departments && Array.isArray(data.departments)) {
        data.departments.forEach(dept => {
            const option = document.createElement('option');
            option.value = dept.id;
            option.textContent = dept.name;
            if (dept.id == data.department_id) option.selected = true;
            select.appendChild(option);
        });
    }
    
    document.getElementById('editForm').action = `/executors/${id}`;
    new bootstrap.Modal(document.getElementById('editModal')).show();
}

function deleteRecord(id) {
    if (confirm('Are you sure you want to delete this executor?')) {
        const form = document.getElementById('deleteForm');
        form.action = `/executors/${id}`;
        form.submit();
    }
}
</script>
@endpush