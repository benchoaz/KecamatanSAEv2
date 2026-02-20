<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Loker extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'title',
        'job_category',
        'desa_id',
        'nama_desa_manual',
        'contact_wa',
        'description',
        'work_time',
        'is_available_today',
        'status',
        'is_sensitive',
        'manage_token',
        'source',
        'internal_notes',
        'is_verified',
        'is_flagged',
        'owner_pin',
        'last_toggle_at',
    ];

    protected $casts = [
        'is_available_today' => 'boolean',
        'is_sensitive' => 'boolean',
        'is_verified' => 'boolean',
        'is_flagged' => 'boolean',
        'last_toggle_at' => 'datetime',
    ];

    // Status Constants
    public const STATUS_MENUNGGU = 'menunggu_verifikasi';
    public const STATUS_AKTIF = 'aktif';
    public const STATUS_NONAKTIF = 'nonaktif';

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (!$model->uuid) {
                $model->uuid = (string) Str::uuid();
            }
            if (!$model->manage_token) {
                $model->manage_token = Str::random(40);
            }
        });
    }

    public function desa()
    {
        return $this->belongsTo(Desa::class, 'desa_id');
    }

    /**
     * Scope for verified and active listings
     */
    public function scopeVerified($query)
    {
        return $query->where('status', self::STATUS_AKTIF)
            ->where('is_verified', true)
            ->where('is_flagged', false);
    }

    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            self::STATUS_MENUNGGU => 'Menunggu Verifikasi',
            self::STATUS_AKTIF => 'Aktif',
            self::STATUS_NONAKTIF => 'Nonaktif',
            default => ucfirst($this->status)
        };
    }

    public function getStatusBadgeAttribute()
    {
        return match ($this->status) {
            self::STATUS_MENUNGGU => 'bg-soft-warning text-warning',
            self::STATUS_AKTIF => 'bg-soft-success text-success',
            self::STATUS_NONAKTIF => 'bg-soft-danger text-danger',
            default => 'bg-soft-secondary text-secondary'
        };
    }
}
