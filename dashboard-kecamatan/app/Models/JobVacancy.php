<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobVacancy extends Model
{
    use \App\Traits\Auditable;

    protected $fillable = [
        'title',
        'company_name',
        'description',
        'contact_wa',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
