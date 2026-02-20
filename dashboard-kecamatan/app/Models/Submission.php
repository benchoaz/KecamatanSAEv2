<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    protected $table = 'submission';
    protected $guarded = ['id'];

    // Status Constants
    // Status Constants
    const STATUS_DRAFT = 'draft';
    const STATUS_SUBMITTED = 'submitted';
    const STATUS_RETURNED = 'returned';
    const STATUS_REVIEWED = 'reviewed';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    protected $casts = [
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'approved_at' => 'datetime',
        'approved_backdate_at' => 'datetime',
        'is_backdate' => 'boolean',
        'skor_total' => 'decimal:2',
    ];

    // Relations to Master
    public function desa()
    {
        return $this->belongsTo(Desa::class);
    }

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    public function aspek()
    {
        return $this->belongsTo(Aspek::class);
    }

    // Relations to Users
    public function submittedBy()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function reviewedBy()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function approvedBackdateBy()
    {
        return $this->belongsTo(User::class, 'approved_backdate_by');
    }

    // Relations to Details
    public function jawabanIndikator()
    {
        return $this->hasMany(JawabanIndikator::class);
    }

    public function buktiDukung()
    {
        return $this->hasMany(BuktiDukung::class);
    }

    public function verifikasi()
    {
        return $this->hasMany(Verifikasi::class)->latest();
    }

    protected static function booted()
    {
        static::addGlobalScope(new \App\Models\Scopes\DesaScope);
    }

    // Helper Methods
    public function isEditable()
    {
        return in_array($this->status, [self::STATUS_DRAFT, self::STATUS_RETURNED]);
    }

    public function isLocked()
    {
        return in_array($this->status, [self::STATUS_SUBMITTED, self::STATUS_REVIEWED, self::STATUS_APPROVED, self::STATUS_REJECTED]);
    }

    public function scopeForDesa($query, $desaId)
    {
        return $query->where('desa_id', $desaId);
    }
}
