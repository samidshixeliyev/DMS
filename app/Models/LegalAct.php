<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LegalAct extends Model
{
    use HasFactory;

    protected $fillable = [
        'act_type_id',
        'issued_by_id',
        'executor_id',
        'execution_note_id',
        'legal_act_number',
        'legal_act_date',
        'summary',
        'task_number',
        'task_description',
        'execution_deadline',
        'related_document_number',
        'related_document_date',
        'created_by',
        'created_date',
        'inserted_user_id',
        'is_active',
        'is_deleted',
    ];

    protected $casts = [
        'legal_act_date' => 'date',
        'execution_deadline' => 'date',
        'related_document_date' => 'date',
        'created_date' => 'datetime',
        'is_active' => 'boolean',
        'is_deleted' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_deleted', false);
    }

    // Relationships
    public function actType()
    {
        return $this->belongsTo(ActType::class);
    }

    public function issuingAuthority()
    {
        return $this->belongsTo(IssuingAuthority::class, 'issued_by_id');
    }

    public function executor()
    {
        return $this->belongsTo(Executor::class);
    }

    public function executionNote()
    {
        return $this->belongsTo(ExecutionNote::class);
    }

    public function insertedUser()
    {
        return $this->belongsTo(User::class, 'inserted_user_id');
    }
}