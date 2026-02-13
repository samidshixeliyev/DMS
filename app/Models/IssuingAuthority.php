<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IssuingAuthority extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'is_deleted'];

    protected $casts = [
        'is_deleted' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_deleted', false);
    }

    public function legalActs()
    {
        return $this->hasMany(LegalAct::class, 'issued_by_id');
    }
}
