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

    /**
     * Executors assigned to this legal act (many-to-many via pivot).
     */
    public function executors()
    {
        return $this->belongsToMany(Executor::class, 'legal_act_executor')
                    ->withPivot('role')
                    ->withTimestamps();
    }

    /**
     * Main executor for this legal act.
     */
    public function mainExecutor()
    {
        return $this->executors()->wherePivot('role', 'main');
    }

    /**
     * Helper executor for this legal act.
     */
    public function helperExecutor()
    {
        return $this->executors()->wherePivot('role', 'helper');
    }

    /**
     * Status logs for this legal act.
     */
    public function statusLogs()
    {
        return $this->hasMany(ExecutorStatusLog::class)->orderBy('created_at', 'desc');
    }

    /**
     * Attachments for this legal act.
     */
    public function attachments()
    {
        return $this->hasMany(ExecutionAttachment::class);
    }

    /**
     * Get the latest status log for this legal act.
     */
    public function latestStatusLog()
    {
        return $this->hasOne(ExecutorStatusLog::class)->latestOfMany();
    }

    /**
     * User who inserted this legal act.
     */
    public function insertedUser()
    {
        return $this->belongsTo(User::class, 'inserted_user_id');
    }

    /**
     * Check if the legal act has been executed (latest status contains "İcra olundu").
     */
    public function getIsExecutedAttribute(): bool
    {
        $latest = $this->latestStatusLog;
        if (!$latest) return false;

        $noteText = $latest->executionNote?->note ?? '';
        return $noteText && mb_stripos($noteText, 'İcra olunub') !== false;
    }
}
