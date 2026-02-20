<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterKomponenBelanja extends Model
{
    use HasFactory;

    protected $table = 'master_komponen_belanja';
    protected $guarded = ['id'];

    protected $casts = [
        'is_active' => 'boolean',
        'objek_pajak' => 'boolean',
        'harga_referensi' => 'decimal:2',
    ];

    // ==================== RELATIONS ====================

    public function kegiatans()
    {
        return $this->belongsToMany(
            MasterKegiatan::class,
            'kegiatan_komponen_belanja',
            'master_komponen_belanja_id',
            'master_kegiatan_id'
        )->withPivot('is_wajib', 'urutan');
    }

    public function ssh()
    {
        return $this->hasMany(MasterSsh::class, 'komponen_belanja_id');
    }

    public function sbu()
    {
        return $this->hasMany(MasterSbu::class, 'komponen_belanja_id');
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

    public function scopeHonor($query)
    {
        return $query->byKategori('honor');
    }

    public function scopeKonsumsi($query)
    {
        return $query->byKategori('konsumsi');
    }

    public function scopeBarang($query)
    {
        return $query->byKategori('barang');
    }

    public function scopeJasa($query)
    {
        return $query->byKategori('jasa');
    }

    public function scopePerjalanan($query)
    {
        return $query->byKategori('perjalanan');
    }

    public function scopeObjekPajak($query)
    {
        return $query->where('objek_pajak', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('urutan')->orderBy('kode_komponen');
    }

    // ==================== ACCESSORS ====================

    public function getKategoriLabelAttribute()
    {
        return match ($this->kategori) {
            'honor' => 'Honor',
            'konsumsi' => 'Konsumsi',
            'barang' => 'Barang',
            'jasa' => 'Jasa',
            'perjalanan' => 'Perjalanan',
            default => ucfirst($this->kategori),
        };
    }

    public function getPajakLabelAttribute()
    {
        return $this->objek_pajak ? 'Kena Pajak' : 'Tidak Kena Pajak';
    }
}
