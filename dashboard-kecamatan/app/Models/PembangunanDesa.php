<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembangunanDesa extends Model
{
    use HasFactory;

    protected $table = 'pembangunan_desa';
    protected $guarded = ['id'];

    protected $casts = [
        'pagu_anggaran' => 'decimal:2',
        'realisasi_anggaran' => 'decimal:2',
        'komponen_belanja' => 'array',
    ];

    public static function boot()
    {
        parent::boot();

        // Auto-generate SPJ requirements when activity is created
        static::created(function ($model) {
            if ($model->master_kegiatan_id) {
                $engine = new \App\Services\SpjRuleEngine();
                // We use the master record as basis for rules
                $recommendedDocs = $engine->getRecommendedDocuments($model->masterKegiatan, [
                    // Pass current transaction data for rule matching
                    'pagu_anggaran' => $model->pagu_anggaran,
                    'jenis_kegiatan' => $model->masterKegiatan->jenis_kegiatan,
                ]);

                foreach ($recommendedDocs as $doc) {
                    $model->dokumenSpjs()->create([
                        'master_dokumen_spj_id' => $doc->id,
                        'is_wajib' => true,
                        'status' => 'pending',
                    ]);
                }
            }
        });
    }

    public function desa()
    {
        return $this->belongsTo(Desa::class);
    }

    public function masterKegiatan()
    {
        return $this->belongsTo(MasterKegiatan::class, 'master_kegiatan_id');
    }

    public function dokumenSpjs()
    {
        return $this->hasMany(PembangunanDokumenSpj::class, 'pembangunan_desa_id');
    }

    public function logbooks()
    {
        return $this->hasMany(PembangunanLogbook::class, 'pembangunan_desa_id');
    }
}
