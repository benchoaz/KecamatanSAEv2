<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengunjungKecamatan extends Model
{
    use HasFactory;

    protected $table = 'pengunjung_kecamatan';
    protected $guarded = ['id'];

    const STATUS_MENUNGGU = 'menunggu';
    const STATUS_DILAYANI = 'dilayani';
    const STATUS_SELESAI = 'selesai';

    protected $casts = [
        'jam_datang' => 'datetime',
    ];

    public function desaAsal()
    {
        return $this->belongsTo(Desa::class, 'desa_asal_id');
    }

    public function scopeHariIni($query)
    {
        return $query->whereBetween('jam_datang', [today()->startOfDay(), today()->endOfDay()]);
    }
}
