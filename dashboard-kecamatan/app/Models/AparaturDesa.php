<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AparaturDesa extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'aparatur_desa';
    protected $guarded = [];

    // Status Jabatan Constants
    const STATUS_AKTIF = 'Aktif';
    const STATUS_PJ = 'Pj';
    const STATUS_BERAKHIR = 'Berakhir';
    const STATUS_BERHENTI = 'Berhenti';

    // Status Verifikasi Constants
    const VERIFIKASI_BELUM = 'Belum Diverifikasi';
    const VERIFIKASI_SUDAH = 'Terverifikasi';
    const VERIFIKASI_REVISI = 'Perlu Perbaikan';

    protected $casts = [
        'tanggal_sk' => 'date',
        'tanggal_mulai' => 'date',
        'tanggal_akhir' => 'date',
    ];

    public function desa()
    {
        return $this->belongsTo(Desa::class, 'desa_id');
    }

    public function documents()
    {
        return $this->hasMany(AparaturDocument::class, 'aparatur_desa_id');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}