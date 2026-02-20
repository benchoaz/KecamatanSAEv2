<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterSubBidang extends Model
{
    use HasFactory;

    protected $table = 'master_sub_bidang';
    protected $guarded = ['id'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ==================== RELATIONS ====================

    public function bidang()
    {
        return $this->belongsTo(MasterBidang::class, 'bidang_id');
    }

    public function kegiatans()
    {
        return $this->hasMany(MasterKegiatan::class, 'sub_bidang_id')->orderBy('urutan');
    }

    // ==================== SCOPES ====================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('urutan')->orderBy('kode_sub_bidang');
    }

    // ==================== ACCESSORS ====================

    public function getFullKodeAttribute()
    {
        return $this->bidang->kode_bidang . '.' . $this->kode_sub_bidang;
    }
}
