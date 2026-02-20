<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterSsh extends Model
{
    use HasFactory;

    protected $table = 'master_ssh';
    protected $guarded = ['id'];

    protected $casts = [
        'harga_wajar_min' => 'decimal:2',
        'harga_wajar_max' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // ==================== RELATIONS ====================

    public function komponenBelanja()
    {
        return $this->belongsTo(MasterKomponenBelanja::class, 'komponen_belanja_id');
    }

    // ==================== SCOPES ====================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByTahun($query, $tahun)
    {
        return $query->where('tahun', $tahun);
    }

    public function scopeByWilayah($query, $wilayah)
    {
        return $query->where('wilayah', 'LIKE', "%{$wilayah}%");
    }

    // ==================== LOGIC ====================

    public function checkWajar($harga)
    {
        return $harga >= $this->harga_wajar_min && $harga <= $this->harga_wajar_max;
    }
}
