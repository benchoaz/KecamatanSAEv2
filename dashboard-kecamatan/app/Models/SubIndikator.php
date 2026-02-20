<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubIndikator extends Model
{
    protected $table = 'sub_indikator';
    protected $guarded = ['id'];

    protected $casts = [
        'opsi_select' => 'array',
        'is_active' => 'boolean',
    ];

    public function indikator()
    {
        return $this->belongsTo(Indikator::class);
    }
}
