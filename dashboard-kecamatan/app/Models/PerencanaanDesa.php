<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerencanaanDesa extends Model
{
    use HasFactory;

    protected $table = 'perencanaan_desa';
    protected $guarded = ['id'];

    const MODE_ARSIP = 'arsip';
    const MODE_TRANSISI = 'transisi';
    const MODE_TERSTRUKTUR = 'terstruktur';

    const TIPE_RPJMDES = 'RPJMDes';
    const TIPE_RKPDES = 'RKPDes';
    const TIPE_APBDES = 'APBDes';

    const STATUS_DRAFT = 'draft';
    const STATUS_DIKIRIM = 'dikirim';
    const STATUS_DIKEMBALIKAN = 'dikembalikan';
    const STATUS_DITERIMA = 'diterima';
    const STATUS_LENGKAP = 'lengkap';

    protected $casts = [
        'tanggal_kegiatan' => 'date',
        'tanggal_perdes' => 'date',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new \App\Models\Scopes\DesaScope);
    }

    public function desa()
    {
        return $this->belongsTo(Desa::class);
    }

    public function usulan()
    {
        return $this->hasMany(UsulanMusrenbang::class, 'perencanaan_id');
    }
}
