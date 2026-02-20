<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\Auditable;

class Announcement extends Model
{
    use Auditable;

    protected $fillable = [
        'title',
        'content',
        'target_type',
        'target_desa_ids',
        'display_mode',
        'start_date',
        'end_date',
        'priority',
        'is_active',
        'created_by'
    ];

    protected $casts = [
        'target_desa_ids' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where('start_date', '<=', now()->toDateString())
            ->where('end_date', '>=', now()->toDateString());
    }
}
