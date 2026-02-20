<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BltDesa extends Model
{
    use HasFactory;

    protected $table = 'blt_desa';
    protected $guarded = ['id'];

    protected $casts = [
        'total_dana_tersalurkan' => 'decimal:2',
    ];

    public function desa()
    {
        return $this->belongsTo(Desa::class);
    }
}
