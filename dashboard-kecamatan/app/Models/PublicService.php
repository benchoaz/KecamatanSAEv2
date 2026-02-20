<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PublicService extends Model
{
    use HasFactory;

    // Categories
    public const CATEGORY_PELAYANAN = 'pelayanan';
    public const CATEGORY_PENGADUAN = 'pengaduan';
    public const CATEGORY_UMKM = 'umkm';
    public const CATEGORY_LOKER = 'loker';

    // Statuses (Standardized Lowercase for API/n8n)
    public const STATUS_MENUNGGU = 'menunggu_verifikasi';
    public const STATUS_DIPROSES = 'diproses';
    public const STATUS_SELESAI = 'selesai';
    public const STATUS_DITOLAK = 'ditolak';

    protected $guarded = [];

    protected $casts = [
        'is_agreed' => 'boolean',
        'handled_at' => 'datetime',
        'ready_at' => 'datetime',
    ];

    public function desa()
    {
        return $this->belongsTo(Desa::class);
    }

    public function attachments()
    {
        return $this->hasMany(PublicServiceAttachment::class, 'public_service_id');
    }

    public function handler()
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    /**
     * Helpers for Automation & UI
     */
    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            self::STATUS_MENUNGGU => 'Menunggu Verifikasi',
            self::STATUS_DIPROSES => 'Sedang Diproses',
            self::STATUS_SELESAI => 'Selesai',
            self::STATUS_DITOLAK => 'Ditolak / Tidak Valid',
            default => $this->status // Fallback for legacy data
        };
    }

    public function getStatusColorAttribute()
    {
        return match ($this->status) {
            self::STATUS_MENUNGGU => 'amber',
            self::STATUS_DIPROSES => 'blue',
            self::STATUS_SELESAI => 'emerald',
            self::STATUS_DITOLAK => 'rose',
            default => 'slate'
        };
    }
}
