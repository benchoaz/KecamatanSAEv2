<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecruitmentScore extends Model
{
    use HasFactory;

    protected $table = 'recruitment_scores';

    protected $fillable = [
        'applicant_id',
        'nilai_tertulis',
        'nilai_wawancara',
        'nilai_total',
        'ranking',
        'catatan_penilai',
        'bukti_ujian_path',
        'scored_by',
        'scored_at',
    ];

    protected $casts = [
        'nilai_tertulis'  => 'float',
        'nilai_wawancara' => 'float',
        'nilai_total'     => 'float',
        'scored_at'       => 'datetime',
    ];

    /**
     * Hitung nilai total: (0.7 * tertulis) + (0.3 * wawancara)
     */
    public static function hitungNilaiTotal(float $tertulis, float $wawancara): float
    {
        return round((0.7 * $tertulis) + (0.3 * $wawancara), 2);
    }

    public function applicant()
    {
        return $this->belongsTo(RecruitmentApplicant::class, 'applicant_id');
    }

    public function scorer()
    {
        return $this->belongsTo(User::class, 'scored_by');
    }
}
