<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements FilamentUser, HasName
{
    use HasApiTokens, HasFactory, Notifiable, \App\Traits\Auditable;

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->status === self::STATUS_AKTIF;
    }

    public function getFilamentName(): string
    {
        return $this->nama_lengkap ?? $this->username;
    }

    protected $fillable = [
        'nama_lengkap',
        'username',
        'password',
        'role_id',
        'desa_id',
        'status',
        'last_login',
        'foto',
        'no_hp',
    ];

    const STATUS_AKTIF = 'aktif';
    const STATUS_NONAKTIF = 'nonaktif';

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'last_login' => 'datetime',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function desa()
    {
        return $this->belongsTo(Desa::class);
    }

    // Helper to check role
    public function hasRole($roleName)
    {
        return $this->role && $this->role->nama_role === $roleName;
    }

    public function isSuperAdmin()
    {
        return $this->hasRole('Super Admin');
    }

    public function isOperatorKecamatan()
    {
        return $this->hasRole('Operator Kecamatan');
    }

    public function isOperatorDesa()
    {
        return $this->hasRole('Operator Desa');
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class, 'submitted_by');
    }
}
