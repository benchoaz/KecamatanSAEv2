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
        'slug',
        'deskripsi_syarat',
        'attachment_requirements',
        'estimasi_waktu',
        'ikon',
        'warna_bg',
        'warna_text',
        'is_active',
        'urutan',
        'is_popular',
        'link_type',
        'custom_link'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_popular' => 'boolean',
        'urutan' => 'integer',
        'attachment_requirements' => 'array'
    ];
}
