<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JawabanIndikator extends Model
{
    protected $table = 'jawaban_indikator';
    protected $guarded = ['id'];

    protected $casts = [
        'nilai_select' => 'array',
        'nilai_date' => 'date',
        'nilai_number' => 'decimal:2',
        'skor' => 'decimal:2',
    ];

    public function submission()
    {
        return $this->belongsTo(Submission::class);
    }

    public function indikator()
    {
        return $this->belongsTo(Indikator::class);
    }
}
