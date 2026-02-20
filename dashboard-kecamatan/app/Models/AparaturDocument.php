<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AparaturDocument extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'aparatur_documents';
    protected $guarded = [];

    public function aparatur()
    {
        return $this->belongsTo(AparaturDesa::class, 'aparatur_desa_id');
    }
}