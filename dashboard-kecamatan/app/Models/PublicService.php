<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

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

    /**
     * Get human readable category label
     */
    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            self::CATEGORY_PELAYANAN => 'Pelayanan Berkas',
            self::CATEGORY_PENGADUAN => 'Pengaduan Masyarakat',
            self::CATEGORY_UMKM => 'UMKM',
            self::CATEGORY_LOKER => 'Loker',
            default => ucfirst($this->category ?? 'Layanan')
        };
    }

    protected $casts = [
        'is_agreed' => 'boolean',
        'handled_at' => 'datetime',
        'ready_at' => 'datetime',
    ];

    /**
     * Bootstrap the model and its traits.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->tracking_code) {
                $model->tracking_code = static::generateUniqueTrackingCode();
            }
        });
    }

    /**
     * Generate a unique 6-digit numeric tracking code.
     */
    public static function generateUniqueTrackingCode()
    {
        do {
            $code = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
        } while (static::where('tracking_code', $code)->exists());

        return $code;
    }

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

    /**
     * Get the explicit public response or the default one for pending/processing reports.
     */
    public function getEffectivePublicResponseAttribute()
    {
        // Safe check: If tracking_code is missing for legacy records, generate it on the fly
        if (!$this->tracking_code) {
            $this->tracking_code = static::generateUniqueTrackingCode();
            $this->save();
        }

        if ($this->public_response) {
            return $this->public_response;
        }

        if (in_array($this->status, [self::STATUS_MENUNGGU, self::STATUS_DIPROSES])) {
            return "akan segera di tindak lanjuti 2 x24 jam anda akan mendapat laporan";
        }

        return null;
    }
}
