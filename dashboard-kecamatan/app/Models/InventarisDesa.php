<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventarisDesa extends Model
{
    use HasFactory;

    protected $table = 'inventaris_desa';
    protected $guarded = ['id'];

    protected static function booted()
    {
        static::addGlobalScope(new \App\Models\Scopes\DesaScope);
    }

    const STATUS_AMAN = 'aman';
    const STATUS_SENGKETA = 'sengketa';
    const STATUS_KLAIM = 'klaim';

    public function desa()
    {
        return $this->belongsTo(Desa::class);
    }
}
