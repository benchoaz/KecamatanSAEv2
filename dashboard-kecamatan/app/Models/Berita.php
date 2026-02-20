<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

use App\Traits\Auditable;

class Berita extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $table = 'berita';

    protected $fillable = [
        'judul',
        'slug',
        'ringkasan',
        'konten',
        'kategori',
        'thumbnail',
        'status',
        'view_count',
        'author_id',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    /**
     * Scope a query to only include published news.
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    /**
     * Relationship to the author (User).
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Generate slug before saving if not provided.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($berita) {
            if (!$berita->slug) {
                $berita->slug = Str::slug($berita->judul) . '-' . Str::random(5);
            }
        });
    }
}
