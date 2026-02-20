<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterDokumen extends Model
{
    use HasFactory;

    protected $table = 'master_dokumen_spj';
    protected $guarded = ['id'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ==================== RELATIONS ====================

    public function kegiatans()
    {
        return $this->belongsToMany(
            MasterKegiatan::class,
            'kegiatan_dokumen_spj',
            'master_dokumen_spj_id',
            'master_kegiatan_id'
        )->withPivot('is_wajib', 'urutan');
    }

    // ==================== SCOPES ====================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByKategori($query, $kategori)
    {
        return $query->where('kategori', $kategori);
    }

    // ==================== ACCESSORS ====================

    public function getKategoriLabelAttribute()
    {
        return ucfirst($this->kategori);
    }
}
