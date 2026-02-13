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
                        <label class="form-label">Legal Act Number</label>
                        <input type="text" name="legal_act_number" class="form-control" 
                               value="{{ request('legal_act_number') }}" placeholder="Search...">
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label">Summary</label>
                        <input type="text" name="summary" class="form-control" 
                               value="{{ request('summary') }}" placeholder="Search...">
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label">Act Type</label>
                        <select name="act_type_id" class="form-select">
                            <option value="">All</option>
                            @foreach($actTypes as $type)
                                <option value="{{ $type->id }}" 
                                    {{ request('act_type_id') == $type->id ? 'selected' : '' }}>
                                    {{ $type->name }}
                                </option>
                            @endforeach
                        </select>
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
                        <label class="form-label">Date From</label>
                        <input type="date" name="legal_act_date_from" class="form-control" 
                               value="{{ request('legal_act_date_from') }}">
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label">Date To</label>
                        <input type="date" name="legal_act_date_to" class="form-control" 
                               value="{{ request('legal_act_date_to') }}">
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
                            <th>Act Type</th>
                            <th>Act Number</th>
                            <th>Date</th>
                            <th>Summary</th>
                            <th>Issuing Authority</th>
                            <th>Executor</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($legalActs as $legalAct)
                            <tr>
                                <td>{{ $legalAct->id }}</td>
                                <td>{{ $legalAct->actType?->name }}</td>
                                <td>{{ $legalAct->legal_act_number }}</td>
                                <td>{{ $legalAct->legal_act_date?->format('d.m.Y') }}</td>
                                <td>{{ Str::limit($legalAct->summary, 50) }}</td>
                                <td>{{ $legalAct->issuingAuthority?->name }}</td>
                                <td>{{ $legalAct->executor?->name }}</td>
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
                                <td colspan="8" class="text-center">No records found</td>
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

{{-- Create Modal - Coming soon --}}
{{-- Edit Modal - Coming soon --}}
{{-- Show Modal - Coming soon --}}

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

function deleteRecord(id) {
    if (confirm('Are you sure you want to delete this record?')) {
        const form = document.getElementById('deleteForm');
        form.action = `/legal-acts/${id}`;
        form.submit();
    }
}
</script>
@endpush