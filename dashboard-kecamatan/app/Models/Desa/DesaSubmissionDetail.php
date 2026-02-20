<?php

namespace App\Models\Desa;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DesaSubmissionDetail extends Model
{
    use HasFactory;

    protected $table = 'desa_submission_details';
    protected $guarded = ['id'];

    public function submission()
    {
        return $this->belongsTo(DesaSubmission::class, 'submission_id');
    }
}
