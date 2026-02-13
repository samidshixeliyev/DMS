<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'username',
        'password',
        'name',
        'surname',
        'user_role',
        'is_deleted',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'is_deleted' => 'boolean',
    ];

    // Soft delete scope
    public function scopeActive($query)
    {
        return $query->where('is_deleted', false);
    }

    public function legalActs()
    {
        return $this->hasMany(LegalAct::class, 'inserted_user_id');
    }

    public function getFullNameAttribute()
    {
        return "{$this->name} {$this->surname}";
    }
}
