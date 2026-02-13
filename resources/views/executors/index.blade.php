@extends('layouts.app')

@section('title', 'Executors')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h2>Executors</h2>
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createModal">
                <i class="bi bi-plus-circle"></i> Add New
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Position</th>
                            <th>Department</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($executors as $executor)
                            <tr>
                                <td>{{ $executor->id }}</td>
                                <td>{{ $executor->name }}</td>
                                <td>{{ $executor->position }}</td>
                                <td>{{ $executor->department?->name }}</td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-info" 
                                            onclick="showDetails({{ $executor->id }})">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-warning" 
                                            onclick="editRecord({{ $executor->id }})">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger" 
                                            onclick="deleteRecord({{ $executor->id }})">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No records found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $executors->links() }}
            </div>
        </div>
    </div>
</div>

{{-- Create Modal --}}
<div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('executors.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Create Executor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Name *</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Position *</label>
                        <input type="text" name="position" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Department *</label>
                        <select name="department_id" class="form-select" required>
                            <option value="">Select Department</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}">{{ $department->name }}</option>
                            @endforeach
                        </select>
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
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Executor</h5>
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
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Executor Details</h5>
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
function showDetails(id) {
    fetch(`/executors/${id}`)
        .then(response => response.json())
        .then(data => {
            const content = `
                <table class="table table-bordered">
                    <tr><th width="30%">ID</th><td>${data.id}</td></tr>
                    <tr><th>Name</th><td>${data.name}</td></tr>
                    <tr><th>Position</th><td>${data.position}</td></tr>
                    <tr><th>Department</th><td>${data.department || '-'}</td></tr>
                    <tr><th>Created At</th><td>${data.created_at}</td></tr>
                </table>
            `;
            document.getElementById('showModalBody').innerHTML = content;
            new bootstrap.Modal(document.getElementById('showModal')).show();
        });
}

function editRecord(id) {
    fetch(`/executors/${id}/edit`)
        .then(response => response.json())
        .then(data => {
            const content = `
                <div class="mb-3">
                    <label class="form-label">Name *</label>
                    <input type="text" name="name" class="form-control" value="${data.name}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Position *</label>
                    <input type="text" name="position" class="form-control" value="${data.position}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Department *</label>
                    <select name="department_id" class="form-select" required>
                        <option value="">Select Department</option>
                        ${data.departments.map(d => `<option value="${d.id}" ${d.id == data.department_id ? 'selected' : ''}>${d.name}</option>`).join('')}
                    </select>
                </div>
            `;
            document.getElementById('editModalBody').innerHTML = content;
            document.getElementById('editForm').action = `/executors/${id}`;
            new bootstrap.Modal(document.getElementById('editModal')).show();
        });
}

function deleteRecord(id) {
    if (confirm('Are you sure you want to delete this record?')) {
        const form = document.getElementById('deleteForm');
        form.action = `/executors/${id}`;
        form.submit();
    }
}
</script>
@endpush