<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhatsappLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'phone',
        'intent',
        'message',
        'response',
        'success',
    ];

    protected $casts = [
        'success' => 'boolean',
        'created_at' => 'datetime',
    ];

    /**
     * Log a WhatsApp interaction
     */
    public static function logInteraction(
        string $phone,
        string $message,
        ?string $intent = null,
        ?string $response = null,
        bool $success = true
    ): self {
        return static::create([
            'phone' => $phone,
            'intent' => $intent,
            'message' => $message,
            'response' => $response,
            'success' => $success,
        ]);
    }

    /**
     * Get success rate for a phone number
     */
    public static function successRate(string $phone): float
    {
        $total = static::where('phone', $phone)->count();
        if ($total === 0) {
            return 0;
        }

        $successful = static::where('phone', $phone)->where('success', true)->count();
        return round(($successful / $total) * 100, 2);
    }

    /**
     * Get popular intents
     */
    public static function popularIntents(int $limit = 10): array
    {
        return static::selectRaw('intent, COUNT(*) as count')
            ->whereNotNull('intent')
            ->groupBy('intent')
            ->orderByDesc('count')
            ->limit($limit)
            ->pluck('count', 'intent')
            ->toArray();
    }

    /**
     * Get daily statistics
     */
    public static function dailyStats(\DateTime $date): array
    {
        $startOfDay = $date->format('Y-m-d 00:00:00');
        $endOfDay = $date->format('Y-m-d 23:59:59');

        return [
            'total_messages' => static::whereBetween('created_at', [$startOfDay, $endOfDay])->count(),
            'unique_users' => static::whereBetween('created_at', [$startOfDay, $endOfDay])->distinct('phone')->count(),
            'success_rate' => static::whereBetween('created_at', [$startOfDay, $endOfDay])->average('success') * 100,
        ];
    }
}
