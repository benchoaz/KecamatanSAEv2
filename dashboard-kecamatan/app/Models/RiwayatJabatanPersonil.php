<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiwayatJabatanPersonil extends Model
{
    use HasFactory;

    protected $table = 'riwayat_jabatan_personil';
    protected $guarded = ['id'];

    protected $casts = [
        'tmt_lama' => 'date',
        'tmt_baru' => 'date',
    ];

    public function personil()
    {
        return $this->belongsTo(PersonilDesa::class, 'personil_desa_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
