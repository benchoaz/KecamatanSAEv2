<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UmkmLocal extends Model
{
    use \App\Traits\Auditable;

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
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'is_verified' => 'boolean',
        'is_flagged' => 'boolean',
        'price' => 'decimal:2',
        'original_price' => 'decimal:2',
        'last_toggle_at' => 'datetime',
    ];

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
}
