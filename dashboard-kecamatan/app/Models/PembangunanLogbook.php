<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembangunanLogbook extends Model
{
    use HasFactory;

    protected $fillable = [
        'pembangunan_desa_id',
        'progres_fisik',
        'catatan',
        'kendala',
        'foto_progres'
    ];

    public function pembangunan()
    {
        return $this->belongsTo(PembangunanDesa::class, 'pembangunan_desa_id');
    }
}
