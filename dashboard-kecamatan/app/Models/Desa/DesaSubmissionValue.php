<?php

namespace App\Models\Desa;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DesaSubmissionValue extends Model
{
    use HasFactory;

    protected $table = 'desa_submission_values';
    protected $guarded = ['id'];
    public $timestamps = false;

    protected $casts = [
        'field_meta' => 'array',
    ];

    public function submission()
    {
        return $this->belongsTo(DesaSubmission::class, 'submission_id');
    }
}
