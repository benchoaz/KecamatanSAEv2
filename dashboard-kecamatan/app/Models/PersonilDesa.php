<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonilDesa extends Model
{
    use HasFactory;

    protected $table = 'personil_desa';
    protected $guarded = ['id'];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'masa_jabatan_mulai' => 'date',
        'masa_jabatan_selesai' => 'date',
        'tanggal_sk' => 'date',
        'is_active' => 'boolean',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new \App\Models\Scopes\DesaScope);

        static::saving(function ($model) {
            if ($model->kategori == 'perangkat') {
                if (str_contains(strtolower($model->jabatan), 'kepala desa')) {
                    // Kades 8 tahun from masa_jabatan_mulai
                    if ($model->masa_jabatan_mulai) {
                        $model->masa_jabatan_selesai = \Carbon\Carbon::parse($model->masa_jabatan_mulai)->addYears(8)->subDay();
                    }
                } else {
                    // Perangkat lainnya pensiun 60 tahun from tanggal_lahir
                    if ($model->tanggal_lahir) {
                        $model->masa_jabatan_selesai = \Carbon\Carbon::parse($model->tanggal_lahir)->addYears(60);
                    }
                }
            }
        });
    }

    public function riwayatJabatan()
    {
        return $this->hasMany(RiwayatJabatanPersonil::class, 'personil_desa_id');
    }

    public function isEditable()
    {
        return in_array($this->status, ['draft', 'dikembalikan']);
    }

    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            'draft' => 'Draft',
            'dikirim' => 'Verifikasi Kecamatan',
            'dikembalikan' => 'Perlu Revisi',
            'diterima' => 'Terverifikasi',
            default => 'Draft'
        };
    }

    public function getStatusBadgeAttribute()
    {
        return match ($this->status) {
            'draft' => 'bg-secondary',
            'dikirim' => 'bg-primary',
            'dikembalikan' => 'bg-danger',
            'diterima' => 'bg-success',
            default => 'bg-secondary'
        };
    }
}
