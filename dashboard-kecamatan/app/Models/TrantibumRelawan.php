<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrantibumRelawan extends Model
{
    use HasFactory;

    protected $fillable = [
        'desa_id',
        'nama',
        'nik',
        'no_hp',
        'alamat',
        'jabatan',
        'status_aktif',
        'foto',
        'sk_file',
    ];

    protected $casts = [
        'status_aktif' => 'boolean',
    ];

    public function desa()
    {
        return $this->belongsTo(Desa::class);
    }
}
