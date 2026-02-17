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
    ];

    /**
     * The legal act this log belongs to.
     */
    public function legalAct()
    {
        return $this->belongsTo(LegalAct::class);
    }

    /**
     * The user who created this log.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The predefined execution note selected.
     */
    public function executionNote()
    {
        return $this->belongsTo(ExecutionNote::class);
    }

    /**
     * Attachments uploaded with this status change.
     */
    public function attachments()
    {
        return $this->hasMany(ExecutionAttachment::class, 'status_log_id');
    }
}
