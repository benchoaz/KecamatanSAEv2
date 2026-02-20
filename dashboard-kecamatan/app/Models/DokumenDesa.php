<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DokumenDesa extends Model
{
    use HasFactory;

    protected $table = 'dokumen_desa';
    protected $guarded = ['id'];

    protected $casts = [
        'tanggal_penyampaian' => 'date',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new \App\Models\Scopes\DesaScope);
    }

    public function desa()
    {
        return $this->belongsTo(Desa::class);
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
}
