<?php

namespace App\Models\Desa;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DesaSubmissionLog extends Model
{
    use HasFactory;

    protected $table = 'desa_submission_logs';
    protected $guarded = ['id'];
    public $timestamps = false;

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
    ];

    public function submission()
    {
        return $this->belongsTo(DesaSubmission::class, 'submission_id');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
}
