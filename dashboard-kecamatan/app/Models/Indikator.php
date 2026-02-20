<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Indikator extends Model
{
    protected $table = 'indikator';
    protected $guarded = ['id'];

    protected $casts = [
        'opsi_select' => 'array',
        'wajib_bukti' => 'boolean',
        'is_active' => 'boolean',
        'bobot' => 'float',
    ];

    public function aspek()
    {
        return $this->belongsTo(Aspek::class);
    }

    public function sub_indikator()
    {
        return $this->hasMany(SubIndikator::class)->orderBy('urutan');
    }
}
