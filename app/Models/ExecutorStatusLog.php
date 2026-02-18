<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExecutorStatusLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'legal_act_id',
        'user_id',
        'execution_note_id',
        'custom_note',
        'approval_status',
        'approved_by',
        'approval_note',
        'approved_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    // ─── Approval status constants ──────────────────────────────
    const APPROVAL_PENDING  = 'pending';
    const APPROVAL_APPROVED = 'approved';
    const APPROVAL_REJECTED = 'rejected';

    // ─── Relationships ──────────────────────────────────────────

    public function legalAct()
    {
        return $this->belongsTo(LegalAct::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function executionNote()
    {
        return $this->belongsTo(ExecutionNote::class);
    }

    public function attachments()
    {
        return $this->hasMany(ExecutionAttachment::class, 'status_log_id');
    }

    public function approvedByUser()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // ─── Helpers ────────────────────────────────────────────────

    public function isPending(): bool
    {
        return $this->approval_status === self::APPROVAL_PENDING;
    }

    public function isApproved(): bool
    {
        return $this->approval_status === self::APPROVAL_APPROVED;
    }

    public function isRejected(): bool
    {
        return $this->approval_status === self::APPROVAL_REJECTED;
    }

    /**
     * Check if this log's execution note contains "İcra olunub".
     */
    public function isExecutionComplete(): bool
    {
        $noteText = $this->executionNote?->note ?? '';
        return $noteText && mb_stripos($noteText, 'İcra olunub') !== false;
    }

    // ─── Scopes ─────────────────────────────────────────────────

    public function scopePending($query)
    {
        return $query->where('approval_status', self::APPROVAL_PENDING);
    }

    public function scopeApproved($query)
    {
        return $query->where('approval_status', self::APPROVAL_APPROVED);
    }
}
