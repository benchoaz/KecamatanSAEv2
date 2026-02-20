<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Umkm extends Model
{
    use HasFactory;

    protected $table = 'umkm';
    public $incrementing = false;
    protected $keyType = 'string';

    // Statuses
    public const STATUS_PENDING = 'pending';
    public const STATUS_AKTIF = 'aktif';
    public const STATUS_NONAKTIF = 'nonaktif';

    // Sources
    const SOURCE_SELF = 'self-service';
    const SOURCE_ADMIN = 'admin';
    const SOURCE_WHATSAPP = 'whatsapp';

    protected $fillable = [
        'id',
        'nama_usaha',
        'nama_pemilik',
        'no_wa',
        'desa',
        'jenis_usaha',
        'deskripsi',
        'foto_usaha',
        'lat',
        'lng',
        'status',
        'source',
        'slug',
        'manage_token',
        'ownership_status',
        'tokopedia_url',
        'shopee_url',
        'tiktok_url'
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (!$model->getKey()) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
            if (!$model->manage_token) {
                $model->manage_token = Str::random(40);
            }
            if (!$model->slug) {
                $model->slug = Str::slug($model->nama_usaha) . '-' . Str::random(5);
            }
        });
    }

    public function products()
    {
        return $this->hasMany(UmkmProduct::class, 'umkm_id');
    }

    public function verifications()
    {
        return $this->hasMany(UmkmVerification::class, 'umkm_id');
    }

    public function logs()
    {
        return $this->hasMany(UmkmAdminLog::class, 'umkm_id');
    }

    /**
     * Helpers for Automation & UI
     */
    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'Menunggu Verifikasi',
            self::STATUS_AKTIF => 'Aktif / Publik',
            self::STATUS_NONAKTIF => 'Nonaktif',
            default => ucfirst($this->status)
        };
    }

    public function getStatusBadgeAttribute()
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'warning',
            self::STATUS_AKTIF => 'success',
            self::STATUS_NONAKTIF => 'secondary',
            default => 'light'
        };
    }
}
