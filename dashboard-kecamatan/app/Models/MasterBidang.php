<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterBidang extends Model
{
    use HasFactory;

    protected $table = 'master_bidang';
    protected $guarded = ['id'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ==================== RELATIONS ====================

    public function subBidangs()
    {
        return $this->hasMany(MasterSubBidang::class, 'bidang_id')->orderBy('urutan');
    }

    public function kegiatans()
    {
        return $this->hasManyThrough(
            MasterKegiatan::class,
            MasterSubBidang::class,
            'bidang_id',
            'sub_bidang_id'
        );
    }

    // ==================== SCOPES ====================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('urutan')->orderBy('kode_bidang');
    }
}
