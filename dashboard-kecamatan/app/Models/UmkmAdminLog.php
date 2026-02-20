<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UmkmAdminLog extends Model
{
    use HasFactory;

    protected $table = 'umkm_admin_log';

    protected $fillable = [
        'umkm_id',
        'action',
        'actor',
        'notes'
    ];

    public function umkm()
    {
        return $this->belongsTo(Umkm::class, 'umkm_id');
    }
}
