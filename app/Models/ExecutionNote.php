<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExecutionNote extends Model
{
    use HasFactory;

    protected $fillable = ['note', 'is_deleted'];

    protected $casts = [
        'is_deleted' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_deleted', false);
    }

    public function legalActs()
    {
        return $this->hasMany(LegalAct::class);
    }
}
