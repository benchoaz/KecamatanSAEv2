<?php

namespace App\Models\Desa;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DesaSubmissionNote extends Model
{
    use HasFactory;

    protected $table = 'desa_submission_notes';
    protected $guarded = ['id'];

    public function submission()
    {
        return $this->belongsTo(DesaSubmission::class, 'submission_id');
    }

    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }
}
