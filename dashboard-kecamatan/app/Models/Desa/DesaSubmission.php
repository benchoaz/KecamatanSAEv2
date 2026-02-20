<?php

namespace App\Models\Desa;

use App\Models\Scopes\DesaScope;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DesaSubmission extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'desa_submissions';
    protected $guarded = ['id'];

    protected $casts = [
        'submitted_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new DesaScope);
    }

    // Status Constants
    const STATUS_DRAFT = 'draft';
    const STATUS_SUBMITTED = 'submitted';
    const STATUS_RETURNED = 'returned';
    const STATUS_COMPLETED = 'completed';

    // Helpers
    public function isEditable()
    {
        return in_array($this->status, [self::STATUS_DRAFT, self::STATUS_RETURNED]);
    }

    // Relationships
    public function details()
    {
        return $this->hasMany(DesaSubmissionDetail::class, 'submission_id');
    }

    public function files()
    {
        return $this->hasMany(DesaSubmissionFile::class, 'submission_id');
    }

    public function notes()
    {
        return $this->hasMany(DesaSubmissionNote::class, 'submission_id')->latest();
    }

    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function desa()
    {
        return $this->belongsTo(\App\Models\Desa::class, 'desa_id');
    }

    public function logs()
    {
        return $this->hasMany(DesaSubmissionLog::class, 'submission_id');
    }
}
