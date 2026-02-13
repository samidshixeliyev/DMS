@extends('layouts.app')

@section('title', 'Legal Act Details')

@section('content')
<div class="row">
    <div class="col-md-10 mx-auto">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-file-text"></i> Legal Act Details</h5>
                <div>
                    <a href="{{ route('legal-acts.edit', $legalAct) }}" class="btn btn-warning btn-sm">
                        <i class="bi bi-pencil"></i> Edit
                    </a>
                    <a href="{{ route('legal-acts.index') }}" class="btn btn-secondary btn-sm">
                        <i class="bi bi-arrow-left"></i> Back
                    </a>
                </div>
            </div>
            <div class="card-body">
                <!-- Deadline Status Alert -->
                @if($legalAct->deadline_status == 'overdue')
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle"></i>
                        <strong>Overdue!</strong> This legal act is {{ abs($legalAct->days_remaining) }} days overdue.
                    </div>
                @elseif($legalAct->deadline_status == 'warning')
                    <div class="alert alert-warning">
                        <i class="bi bi-clock"></i>
                        <strong>Warning!</strong> Deadline is in {{ $legalAct->days_remaining }} day(s).
                    </div>
                @endif

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <h6 class="text-muted">Legal Act Number</h6>
                        <p class="fw-bold">{{ $legalAct->legal_act_number }}</p>
                    </div>

                    <div class="col-md-6 mb-3">
                        <h6 class="text-muted">Legal Act Date</h6>
                        <p>{{ $legalAct->legal_act_date->format('d.m.Y') }}</p>
                    </div>

                    <div class="col-md-6 mb-3">
                        <h6 class="text-muted">Act Type</h6>
                        <p>{{ $legalAct->actType->name ?? '-' }}</p>
                    </div>

                    <div class="col-md-6 mb-3">
                        <h6 class="text-muted">Issuing Authority</h6>
                        <p>{{ $legalAct->issuedBy->name ?? '-' }}</p>
                    </div>

                    <div class="col-md-6 mb-3">
                        <h6 class="text-muted">Executor</h6>
                        <p>{{ $legalAct->executor->name ?? '-' }}</p>
                    </div>

                    <div class="col-md-6 mb-3">
                        <h6 class="text-muted">Department</h6>
                        <p>{{ $legalAct->executor->department->name ?? '-' }}</p>
                    </div>

                    <div class="col-md-6 mb-3">
                        <h6 class="text-muted">Executor Position</h6>
                        <p>{{ $legalAct->executor->position ?? '-' }}</p>
                    </div>

                    <div class="col-md-6 mb-3">
                        <h6 class="text-muted">Execution Deadline</h6>
                        <p class="fw-bold {{ $legalAct->deadline_status == 'overdue' ? 'text-danger' : ($legalAct->deadline_status == 'warning' ? 'text-warning' : '') }}">
                            {{ $legalAct->execution_deadline->format('d.m.Y') }}
                            @if($legalAct->days_remaining < 0)
                                <span class="badge bg-danger">{{ abs($legalAct->days_remaining) }} days overdue</span>
                            @elseif($legalAct->days_remaining <= 3)
                                <span class="badge bg-warning text-dark">{{ $legalAct->days_remaining }} days left</span>
                            @else
                                <span class="badge bg-success">{{ $legalAct->days_remaining }} days left</span>
                            @endif
                        </p>
                    </div>

                    <div class="col-md-6 mb-3">
                        <h6 class="text-muted">Task Number</h6>
                        <p>{{ $legalAct->task_number ?? '-' }}</p>
                    </div>

                    <div class="col-md-6 mb-3">
                        <h6 class="text-muted">Related Document Number</h6>
                        <p>{{ $legalAct->related_document_number ?? '-' }}</p>
                    </div>

                    <div class="col-md-6 mb-3">
                        <h6 class="text-muted">Related Document Date</h6>
                        <p>{{ $legalAct->related_document_date?->format('d.m.Y') ?? '-' }}</p>
                    </div>

                    <div class="col-md-6 mb-3">
                        <h6 class="text-muted">Created By</h6>
                        <p>{{ $legalAct->created_by ?? ($legalAct->insertedUser->full_name ?? '-') }}</p>
                    </div>

                    <div class="col-md-6 mb-3">
                        <h6 class="text-muted">Created Date</h6>
                        <p>{{ $legalAct->created_date?->format('d.m.Y') ?? $legalAct->created_at->format('d.m.Y') }}</p>
                    </div>

                    <div class="col-12 mb-3">
                        <h6 class="text-muted">Task Description</h6>
                        <p>{{ $legalAct->task_description ?? '-' }}</p>
                    </div>

                    <div class="col-12 mb-3">
                        <h6 class="text-muted">Summary</h6>
                        <p>{{ $legalAct->summary ?? '-' }}</p>
                    </div>

                    @if($legalAct->executionNote)
                        <div class="col-12 mb-3">
                            <h6 class="text-muted">Execution Note</h6>
                            <div class="alert alert-info">
                                {{ $legalAct->executionNote->note }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
