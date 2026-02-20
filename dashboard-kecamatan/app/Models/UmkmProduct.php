<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UmkmProduct extends Model
{
    use HasFactory;

    protected $table = 'umkm_products';

    protected $fillable = [
        'umkm_id',
        'nama_produk',
        'harga',
        'deskripsi',
        'foto_produk',
        'is_available'
    ];

    protected $casts = [
        'is_available' => 'boolean',
        'harga' => 'decimal:2'
    ];

    public function umkm()
    {
        return $this->belongsTo(Umkm::class, 'umkm_id');
    }
}
