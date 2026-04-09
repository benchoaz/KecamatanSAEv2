<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class UmkmLocal extends Model
{
    use \App\Traits\Auditable, \App\Traits\OperationalStatus;

    protected $fillable = [
        'name',
        'product',
        'address',
        'price',
        'original_price',
        'description',
        'contact_wa',
        'image_path',
        'is_active',
        'is_featured',
        'is_verified',
        'is_flagged',
        'owner_pin',
        'last_toggle_at',
        'module',
        'operating_hours',
        'is_on_holiday',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'is_verified' => 'boolean',
        'is_flagged' => 'boolean',
        'price' => 'decimal:2',
        'original_price' => 'decimal:2',
        'last_toggle_at' => 'datetime',
        'is_on_holiday' => 'boolean',
    ];

    // Auto-hash PIN when setting
    public function setOwnerPinAttribute($value)
    {
        if (!empty($value)) {
            $this->attributes['owner_pin'] = Hash::make($value);
        }
    }

    // Module Constants
    public const MODULE_UMKM = 'umkm';
    public const MODULE_JASA = 'jasa';

    /**
     * Scope for verified and active listings
     */
    public function scopeVerified($query)
    {
        return $query->where('is_active', true)
            ->where('is_verified', true)
            ->where('is_flagged', false);
    }

    /**
     * Scope for UMKM module
     */
    public function scopeUmkm($query)
    {
        return $query->where('module', self::MODULE_UMKM);
    }

    /**
     * Scope for Jasa module
     */
    public function scopeJasa($query)
    {
        return $query->where('module', self::MODULE_JASA);
    }

    /**
     * Owner relationship - links to User account (optional)
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }
}
