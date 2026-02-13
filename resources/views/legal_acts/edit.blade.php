@extends('layouts.app')

@section('title', 'Edit Legal Act')

@section('content')
<div class="row">
    <div class="col-md-10 mx-auto">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-pencil"></i> Edit Legal Act: {{ $legalAct->legal_act_number }}</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('legal-acts.update', $legalAct) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="legal_act_number" class="form-label">Legal Act Number <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('legal_act_number') is-invalid @enderror" 
                                   id="legal_act_number" name="legal_act_number" value="{{ old('legal_act_number', $legalAct->legal_act_number) }}" required>
                            @error('legal_act_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="legal_act_date" class="form-label">Legal Act Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('legal_act_date') is-invalid @enderror" 
                                   id="legal_act_date" name="legal_act_date" value="{{ old('legal_act_date', $legalAct->legal_act_date->format('Y-m-d')) }}" required>
                            @error('legal_act_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="act_type_id" class="form-label">Act Type <span class="text-danger">*</span></label>
                            <select class="form-select @error('act_type_id') is-invalid @enderror" 
                                    id="act_type_id" name="act_type_id" required>
                                <option value="">Select Act Type</option>
                                @foreach($actTypes as $type)
                                    <option value="{{ $type->id }}" {{ old('act_type_id', $legalAct->act_type_id) == $type->id ? 'selected' : '' }}>
                                        {{ $type->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('act_type_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="issued_by_id" class="form-label">Issuing Authority <span class="text-danger">*</span></label>
                            <select class="form-select @error('issued_by_id') is-invalid @enderror" 
                                    id="issued_by_id" name="issued_by_id" required>
                                <option value="">Select Issuing Authority</option>
                                @foreach($issuingAuthorities as $authority)
                                    <option value="{{ $authority->id }}" {{ old('issued_by_id', $legalAct->issued_by_id) == $authority->id ? 'selected' : '' }}>
                                        {{ $authority->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('issued_by_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="executor_id" class="form-label">Executor <span class="text-danger">*</span></label>
                            <select class="form-select @error('executor_id') is-invalid @enderror" 
                                    id="executor_id" name="executor_id" required>
                                <option value="">Select Executor</option>
                                @foreach($executors as $executor)
                                    <option value="{{ $executor->id }}" {{ old('executor_id', $legalAct->executor_id) == $executor->id ? 'selected' : '' }}>
                                        {{ $executor->name }} - {{ $executor->department->name ?? '' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('executor_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="execution_deadline" class="form-label">Execution Deadline <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('execution_deadline') is-invalid @enderror" 
                                   id="execution_deadline" name="execution_deadline" value="{{ old('execution_deadline', $legalAct->execution_deadline->format('Y-m-d')) }}" required>
                            @error('execution_deadline')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="task_number" class="form-label">Task Number</label>
                            <input type="text" class="form-control @error('task_number') is-invalid @enderror" 
                                   id="task_number" name="task_number" value="{{ old('task_number', $legalAct->task_number) }}">
                            @error('task_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="execution_note_id" class="form-label">Execution Note</label>
                            <select class="form-select @error('execution_note_id') is-invalid @enderror" 
                                    id="execution_note_id" name="execution_note_id">
                                <option value="">Select Execution Note (Optional)</option>
                                @foreach($executionNotes as $note)
                                    <option value="{{ $note->id }}" {{ old('execution_note_id', $legalAct->execution_note_id) == $note->id ? 'selected' : '' }}>
                                        {{ Str::limit($note->note, 50) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('execution_note_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 mb-3">
                            <label for="task_description" class="form-label">Task Description</label>
                            <textarea class="form-control @error('task_description') is-invalid @enderror" 
                                      id="task_description" name="task_description" rows="3">{{ old('task_description', $legalAct->task_description) }}</textarea>
                            @error('task_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 mb-3">
                            <label for="summary" class="form-label">Summary</label>
                            <textarea class="form-control @error('summary') is-invalid @enderror" 
                                      id="summary" name="summary" rows="3">{{ old('summary', $legalAct->summary) }}</textarea>
                            @error('summary')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="related_document_number" class="form-label">Related Document Number</label>
                            <input type="text" class="form-control @error('related_document_number') is-invalid @enderror" 
                                   id="related_document_number" name="related_document_number" value="{{ old('related_document_number', $legalAct->related_document_number) }}">
                            @error('related_document_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="related_document_date" class="form-label">Related Document Date</label>
                            <input type="date" class="form-control @error('related_document_date') is-invalid @enderror" 
                                   id="related_document_date" name="related_document_date" value="{{ old('related_document_date', $legalAct->related_document_date?->format('Y-m-d')) }}">
                            @error('related_document_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('legal-acts.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Update Legal Act
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
