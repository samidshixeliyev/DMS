<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Executor extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'position', 'department_id', 'is_deleted'];

    protected $casts = [
        'is_deleted' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_deleted', false);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function legalActs()
    {
        return $this->hasMany(LegalAct::class);
    }
}
