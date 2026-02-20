<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembangunanDokumenSpj extends Model
{
    use HasFactory;

    protected $table = 'pembangunan_dokumen_spj';
    protected $guarded = ['id'];

    protected $casts = [
        'is_wajib' => 'boolean',
        'uploaded_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    // ==================== RELATIONS ====================

    public function pembangunanDesa()
    {
        return $this->belongsTo(PembangunanDesa::class, 'pembangunan_desa_id');
    }

    public function masterDokumen()
    {
        return $this->belongsTo(MasterDokumen::class, 'master_dokumen_spj_id');
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    // ==================== SCOPES ====================

    public function scopeWajib($query)
    {
        return $query->where('is_wajib', true);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeUploaded($query)
    {
        return $query->where('status', 'uploaded');
    }

    public function scopeVerified($query)
    {
        return $query->where('status', 'verified');
    }
}
