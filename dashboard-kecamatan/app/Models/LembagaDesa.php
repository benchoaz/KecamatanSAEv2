<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LembagaDesa extends Model
{
    use HasFactory;

    protected $table = 'lembaga_desa';
    protected $guarded = ['id'];

    public function desa()
    {
        return $this->belongsTo(Desa::class);
    }

    protected static function booted()
    {
        static::addGlobalScope(new \App\Models\Scopes\DesaScope);
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
