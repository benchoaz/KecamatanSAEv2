<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PelayananFaq extends Model
{
    protected $table = 'pelayanan_faqs';

    protected $fillable = [
        'category',
        'module',
        'keywords',
        'question',
        'answer',
        'priority',
        'is_active',
        'last_updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'priority' => 'integer',
    ];

    // Module Constants
    public const MODULE_PELAYANAN = 'pelayanan';
    public const MODULE_UMKM = 'umkm';
    public const MODULE_LOKER = 'loker';

    /**
     * Get the user who last updated this FAQ
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'last_updated_by');
    }

    /**
     * Scope for active FAQs
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for specific module
     */
    public function scopeModule($query, $module)
    {
        return $query->where('module', $module);
    }

    /**
     * Scope for ordering by priority
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc');
    }
}
