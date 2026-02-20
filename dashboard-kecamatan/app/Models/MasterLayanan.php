<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterLayanan extends Model
{
    use HasFactory;

    protected $table = 'master_layanan';

    protected $fillable = [
        'nama_layanan',
        'deskripsi_syarat',
        'estimasi_waktu',
        'ikon',
        'warna_bg',
        'warna_text',
        'is_active',
        'urutan'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'urutan' => 'integer'
    ];
}
