<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsulanMusrenbang extends Model
{
    use HasFactory;

    protected $table = 'usulan_musrenbang';
    protected $guarded = ['id'];

    public function perencanaan()
    {
        return $this->belongsTo(PerencanaanDesa::class, 'perencanaan_id');
    }
}
