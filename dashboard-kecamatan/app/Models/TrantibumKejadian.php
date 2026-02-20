<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrantibumKejadian extends Model
{
    use HasFactory;

    protected $fillable = [
        'desa_id',
        'kategori',
        'jenis_kejadian',
        'waktu_kejadian',
        'lokasi_koordinat',
        'lokasi_deskripsi',
        'kronologi',
        'dampak_kerusakan',
        'kondisi_mutakhir',
        'upaya_penanganan',
        'pihak_terlibat',
        'status',
        'foto_kejadian'
    ];

    protected $casts = [
        'waktu_kejadian' => 'datetime',
    ];

    public function desa()
    {
        return $this->belongsTo(Desa::class);
    }
}
