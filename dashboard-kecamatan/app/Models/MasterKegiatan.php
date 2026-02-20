<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterKegiatan extends Model
{
    use HasFactory;

    protected $table = 'master_kegiatan';
    protected $guarded = ['id'];

    protected $casts = [
        'is_active' => 'boolean',
        'harga_referensi' => 'decimal:2',
        'template_spj' => 'array',
    ];

    // ==================== RELATIONS ====================

    public function subBidang()
    {
        return $this->belongsTo(MasterSubBidang::class, 'sub_bidang_id');
    }

    public function bidang()
    {
        return $this->hasOneThrough(
            MasterBidang::class,
            MasterSubBidang::class,
            'id',
            'id',
            'sub_bidang_id',
            'bidang_id'
        );
    }

    public function komponenBelanjas()
    {
        return $this->belongsToMany(
            MasterKomponenBelanja::class,
            'kegiatan_komponen_belanja',
            'master_kegiatan_id',
            'master_komponen_belanja_id'
        )->withPivot('is_wajib', 'urutan')->orderByPivot('urutan');
    }

    public function komponenWajib()
    {
        return $this->komponenBelanjas()->wherePivot('is_wajib', true);
    }

    public function dokumenSpj()
    {
        return $this->belongsToMany(
            MasterDokumen::class,
            'kegiatan_dokumen_spj',
            'master_kegiatan_id',
            'master_dokumen_spj_id'
        )->withPivot('is_wajib', 'urutan')->orderByPivot('urutan');
    }

    // ==================== SCOPES ====================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByJenis($query, $jenis)
    {
        return $query->where('jenis_kegiatan', $jenis);
    }

    public function scopeFisik($query)
    {
        return $query->byJenis('fisik');
    }

    public function scopeNonFisik($query)
    {
        return $query->byJenis('non_fisik');
    }

    public function scopeMusdes($query)
    {
        return $query->byJenis('musdes');
    }

    public function scopeBlt($query)
    {
        return $query->byJenis('blt');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('urutan')->orderBy('kode_kegiatan');
    }

    // ==================== ACCESSORS ====================

    public function getJenisLabelAttribute()
    {
        return match ($this->jenis_kegiatan) {
            'fisik' => 'Fisik',
            'non_fisik' => 'Non Fisik',
            'musdes' => 'Musdes',
            'blt' => 'BLT',
            default => ucfirst($this->jenis_kegiatan),
        };
    }

    public function getTemplateDokumenAttribute()
    {
        // Default templates per jenis kegiatan
        $defaults = [
            'fisik' => ['RAB', 'Gambar Rencana', 'Foto 0%', 'Foto 50%', 'Foto 100%', 'BA Serah Terima'],
            'non_fisik' => ['TOR/KAK', 'Daftar Hadir', 'Notulensi', 'Dokumentasi'],
            'musdes' => ['Undangan', 'Daftar Hadir', 'Notulensi', 'BA Musdes'],
            'blt' => ['SK Penetapan KPM', 'Daftar Penerima', 'Kuitansi', 'BA Penyaluran'],
        ];

        return $this->template_spj ?? $defaults[$this->jenis_kegiatan] ?? [];
    }
}
