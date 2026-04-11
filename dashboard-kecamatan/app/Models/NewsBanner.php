<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class NewsBanner extends Model
{
    use HasFactory, Auditable;

    protected $fillable = [
        'title',
        'image_path',
        'link_url',
        'priority',
        'is_active',
        'created_by'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'priority' => 'integer',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->orderBy('priority', 'desc');
    }
}
