<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Desa extends Model
{
    protected $table = 'desa';
    protected $guarded = ['id']; // Allow mass assignment for now, but will protect kode_desa in controller

    const STATUS_AKTIF = 'aktif';
    const STATUS_TIDAK_AKTIF = 'tidak_aktif';

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_AKTIF);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }

    public function personil()
    {
        return $this->hasMany(PersonilDesa::class, 'desa_id');
    }

    public function lembaga()
    {
        return $this->hasMany(LembagaDesa::class, 'desa_id');
    }

    public function inventaris()
    {
        return $this->hasMany(InventarisDesa::class, 'desa_id');
    }

    public function perencanaan()
    {
        return $this->hasMany(PerencanaanDesa::class, 'desa_id');
    }

    public function dokumens()
    {
        return $this->hasMany(DokumenDesa::class, 'desa_id');
    }

    public function trantibumKejadians()
    {
        return $this->hasMany(TrantibumKejadian::class, 'desa_id');
    }

    public function relawans()
    {
        return $this->hasMany(TrantibumRelawan::class, 'desa_id');
    }
}
