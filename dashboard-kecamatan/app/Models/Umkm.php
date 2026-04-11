<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Umkm extends Model
{
    use HasFactory, \App\Traits\OperationalStatus;

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
        'nik',
        'no_wa',
        'desa',
        'patokan_lokasi',
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
        'tiktok_url',
        'operating_hours',
        'is_on_holiday',
        'name_updated_at',
        'is_verified',
        'nib_number'
    ];

    protected $casts = [
        'name_updated_at' => 'datetime',
        'is_on_holiday' => 'boolean',
        'is_verified' => 'boolean'
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
     * Owner relationship - links to User account (optional)
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_user_id');
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

    /**
     * Verification Level Accessor
     * Level 1: Basic (WA Only)
     * Level 2: Warga (WA + NIK)
     * Level 3: Legal (WA + NIK + NIB)
     */
    public function getVerificationLevelAttribute()
    {
        if ($this->nib_number && $this->nik && $this->is_verified) {
            return 'legal'; // Centang Biru
        }
        
        if ($this->nik && $this->is_verified) {
            return 'warga'; // Verified Citizen
        }
        
        return 'basic'; // Just registered
    }

    public function getVerificationLevelLabelAttribute()
    {
        return match($this->verification_level) {
            'legal' => 'Terverifikasi Legal (OSS NIB)',
            'warga' => 'Warga Terverifikasi',
            default => 'Penyedia Umum'
        };
    }

    /**
     * Master Kategori UMKM Terpusat
     * Digunakan untuk pendaftaran seller, filter etalase, dan pemetaan ikon.
     */
    public static function getStandardCategories()
    {
        return [
            [
                'name' => 'Oleh-oleh',
                'slug' => 'oleh-oleh',
                'icon' => 'fa-gift',
                'color_class' => 'bg-amber-50 text-amber-600',
                'keywords' => ['oleh-oleh', 'khas', 'cinderamata']
            ],
            [
                'name' => 'Kuliner',
                'slug' => 'kuliner',
                'icon' => 'fa-utensils',
                'color_class' => 'bg-rose-50 text-rose-500',
                'keywords' => ['makanan', 'minuman', 'bakso', 'snack', 'warung']
            ],
            [
                'name' => 'Fashion',
                'slug' => 'fashion',
                'icon' => 'fa-tshirt',
                'color_class' => 'bg-blue-50 text-blue-500',
                'keywords' => ['pakaian', 'baju', 'hijab', 'konveksi', 'sepatu', 'tas']
            ],
            [
                'name' => 'Elektronik',
                'slug' => 'elektronik',
                'icon' => 'fa-tv',
                'color_class' => 'bg-indigo-50 text-indigo-500',
                'keywords' => ['hp', 'komputer', 'laptop', 'servis', 'gadget']
            ],
            [
                'name' => 'Kecantikan',
                'slug' => 'kecantikan',
                'icon' => 'fa-magic',
                'color_class' => 'bg-pink-50 text-pink-500',
                'keywords' => ['skincare', 'kosmetik', 'salon', 'makeup']
            ],
            [
                'name' => 'Kesehatan',
                'slug' => 'kesehatan',
                'icon' => 'fa-heartbeat',
                'color_class' => 'bg-emerald-50 text-emerald-600',
                'keywords' => ['obat', 'jamu', 'herbal', 'pijat', 'apotek']
            ],
            [
                'name' => 'Perabotan',
                'slug' => 'perabotan',
                'icon' => 'fa-home',
                'color_class' => 'bg-orange-50 text-orange-600',
                'keywords' => ['furniture', 'rumah tangga', 'mebel', 'kasur']
            ],
            [
                'name' => 'Pertanian',
                'slug' => 'pertanian',
                'icon' => 'fa-seedling',
                'color_class' => 'bg-green-50 text-green-600',
                'keywords' => ['bibit', 'pupuk', 'padi', 'sayur', 'buah', 'ternak']
            ],
            [
                'name' => 'Jasa',
                'slug' => 'jasa',
                'icon' => 'fa-tools',
                'color_class' => 'bg-cyan-50 text-cyan-600',
                'keywords' => ['servis', 'tukang', 'jahit', 'angkut', 'laundry']
            ],
            [
                'name' => 'Lainnya',
                'slug' => 'lainnya',
                'icon' => 'fa-th-large',
                'color_class' => 'bg-slate-50 text-slate-500',
                'keywords' => []
            ],
        ];
    }
}
