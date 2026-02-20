<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterSbu extends Model
{
    use HasFactory;

    protected $table = 'master_sbu';
    protected $guarded = ['id'];

    protected $casts = [
        'batas_maks' => 'decimal:2',
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

    // ==================== LOGIC ====================

    public function isExceed($harga)
    {
        return $harga > $this->batas_maks;
    }
}
