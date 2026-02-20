<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UmkmVerification extends Model
{
    use HasFactory;

    protected $table = 'umkm_verification';

    protected $fillable = [
        'umkm_id',
        'kode_verifikasi',
        'expired_at',
        'is_verified'
    ];

    protected $casts = [
        'expired_at' => 'datetime',
        'is_verified' => 'boolean'
    ];

    public function umkm()
    {
        return $this->belongsTo(Umkm::class, 'umkm_id');
    }
}
