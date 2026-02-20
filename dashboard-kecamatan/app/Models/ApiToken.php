<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ApiToken extends Model
{
    use HasFactory;

    /**
     * Available token abilities with descriptions
     */
    const ABILITIES = [
        'umkm-read' => 'Read UMKM data',
        'umkm-write' => 'Create/Update UMKM data',
        'loker-read' => 'Read job vacancies',
        'loker-write' => 'Create/Update job vacancies',
        'faq-read' => 'Read FAQ data',
        'faq-write' => 'Create/Update FAQ data',
        'complaint-read' => 'Read complaints',
        'complaint-write' => 'Create complaints',
        'owner-verify' => 'Verify owner PIN',
        'owner-toggle' => 'Toggle listing status',
    ];

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'name',
        'token',
        'abilities',
        'last_used_at',
        'expires_at',
        'revoked_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'token',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'abilities' => 'array',
        'last_used_at' => 'datetime',
        'expires_at' => 'datetime',
        'revoked_at' => 'datetime',
    ];

    /**
     * Get the user that owns the token.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if the token is revoked.
     */
    public function isRevoked(): bool
    {
        return !is_null($this->revoked_at);
    }

    /**
     * Check if the token is expired.
     */
    public function isExpired(): bool
    {
        return !is_null($this->expires_at) && $this->expires_at->isPast();
    }

    /**
     * Check if the token is valid (not revoked and not expired).
     */
    public function isValid(): bool
    {
        return !$this->isRevoked() && !$this->isExpired();
    }

    /**
     * Check if the token has a specific ability.
     */
    public function can(string $ability): bool
    {
        if (is_null($this->abilities)) {
            return true; // No restrictions = full access
        }
        return in_array($ability, $this->abilities) || in_array('*', $this->abilities);
    }

    /**
     * Generate a new random token string.
     */
    public static function generateTokenString(): string
    {
        return Str::random(64);
    }

    /**
     * Hash a plain token for storage.
     */
    public static function hashToken(string $plainToken): string
    {
        return hash('sha256', $plainToken);
    }

    /**
     * Find a token by the plain text token.
     */
    public static function findByToken(string $plainToken): ?self
    {
        $hashedToken = self::hashToken($plainToken);
        return self::where('token', $hashedToken)->first();
    }

    /**
     * Scope to get only valid (non-revoked, non-expired) tokens.
     */
    public function scopeValid($query)
    {
        return $query->whereNull('revoked_at')
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
    }

    /**
     * Scope to get only revoked tokens.
     */
    public function scopeRevoked($query)
    {
        return $query->whereNotNull('revoked_at');
    }

    /**
     * Scope to get only expired tokens.
     */
    public function scopeExpired($query)
    {
        return $query->whereNotNull('expires_at')
            ->where('expires_at', '<=', now());
    }

    /**
     * Revoke the token.
     */
    public function revoke(): bool
    {
        return $this->update(['revoked_at' => now()]);
    }

    /**
     * Update the last used timestamp.
     */
    public function markAsUsed(): bool
    {
        return $this->update(['last_used_at' => now()]);
    }

    /**
     * Get ability description by key.
     */
    public static function getAbilityDescription(string $ability): string
    {
        return self::ABILITIES[$ability] ?? $ability;
    }

    /**
     * Get all abilities as options for form select.
     */
    public static function getAbilityOptions(): array
    {
        return self::ABILITIES;
    }
}