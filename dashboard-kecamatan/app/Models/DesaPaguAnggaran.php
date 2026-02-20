<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DesaPaguAnggaran extends Model
{
    use HasFactory;

    protected $table = 'desa_pagu_anggaran';

    protected $fillable = [
        'desa_id',
        'tahun',
        'sumber_dana',
        'jumlah_pagu',
        'keterangan',
    ];

    protected $casts = [
        'jumlah_pagu' => 'decimal:2',
        'tahun' => 'integer',
    ];

    public function desa()
    {
        return $this->belongsTo(Desa::class);
    }
}
