<?php

namespace App\Services;

/**
 * WhatsApp Logger Service
 * 
 * Centralized logging for WhatsApp bot activities
 */
class WhatsAppLogger
{
    private string $logPath;
    private string $logLevel;

    const LEVEL_DEBUG = 'debug';
    const LEVEL_INFO = 'info';
    const LEVEL_WARNING = 'warning';
    const LEVEL_ERROR = 'error';

    public function __construct()
    {
        $this->logPath = __DIR__ . '/../../storage/logs/whatsapp';
        $this->logLevel = $_ENV['LOG_LEVEL'] ?? 'info';

        $this->ensureDirectoryExists();
    }

    /**
     * Log a message
     */
    public function log(string $level, string $message, array $context = []): void
    {
        if (!$this->shouldLog($level)) {
            return;
        }

        $entry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'level' => strtoupper($level),
            'message' => $message,
            'context' => $context
        ];

        $logFile = $this->logPath . '/bot-' . date('Y-m-d') . '.log';
        file_put_contents(
            $logFile,
            json_encode($entry, JSON_UNESCAPED_UNICODE) . "\n",
            FILE_APPEND
        );
    }

    /**
     * Log debug message
     */
    public function debug(string $message, array $context = []): void
    {
        $this->log(self::LEVEL_DEBUG, $message, $context);
    }

    /**
     * Log info message
     */
    public function info(string $message, array $context = []): void
    {
        $this->log(self::LEVEL_INFO, $message, $context);
    }

    /**
     * Log warning message
     */
    public function warning(string $message, array $context = []): void
    {
        $this->log(self::LEVEL_WARNING, $message, $context);
    }

    /**
     * Log error message
     */
    public function error(string $message, array $context = []): void
    {
        $this->log(self::LEVEL_ERROR, $message, $context);
    }

    /**
     * Log incoming message
     */
    public function logIncoming(string $phone, string $message, string $intent = null): void
    {
        $this->info('Incoming message', [
            'phone' => $this->maskPhone($phone),
            'message' => substr($message, 0, 100),
            'intent' => $intent,
            'direction' => 'incoming'
        ]);
    }

    /**
     * Log outgoing message
     */
    public function logOutgoing(string $phone, string $message): void
    {
        $this->info('Outgoing message', [
            'phone' => $this->maskPhone($phone),
            'message' => substr($message, 0, 100),
            'direction' => 'outgoing'
        ]);
    }

    /**
     * Log owner action
     */
    public function logOwnerAction(string $phone, string $action, array $data = []): void
    {
        $this->info('Owner action', [
            'phone' => $this->maskPhone($phone),
            'action' => $action,
            'data' => $data
        ]);
    }

    /**
     * Log rate limit event
     */
    public function logRateLimit(string $phone, int $count, int $limit): void
    {
        $this->warning('Rate limit exceeded', [
            'phone' => $this->maskPhone($phone),
            'count' => $count,
            'limit' => $limit
        ]);
    }

    /**
     * Log API error
     */
    public function logApiError(string $endpoint, string $error, array $context = []): void
    {
        $this->error('API error', [
            'endpoint' => $endpoint,
            'error' => $error,
            'context' => $context
        ]);
    }

    /**
     * Log PIN event
     */
    public function logPinEvent(string $phone, string $event, bool $success): void
    {
        $this->info('PIN event', [
            'phone' => $this->maskPhone($phone),
            'event' => $event,
            'success' => $success
        ]);
    }

    /**
     * Log complaint event
     */
    public function logComplaintEvent(string $phone, string $event, array $data = []): void
    {
        $this->info('Complaint event', [
            'phone' => $this->maskPhone($phone),
            'event' => $event,
            'data' => $data
        ]);
    }

    /**
     * Check if level should be logged
     */
    private function shouldLog(string $level): bool
    {
        $levels = [
            self::LEVEL_DEBUG => 0,
            self::LEVEL_INFO => 1,
            self::LEVEL_WARNING => 2,
            self::LEVEL_ERROR => 3
        ];

        $currentLevel = $levels[$this->logLevel] ?? 1;
        $messageLevel = $levels[$level] ?? 1;

        return $messageLevel >= $currentLevel;
    }

    /**
     * Mask phone number for privacy
     */
    private function maskPhone(string $phone): string
    {
        if (strlen($phone) < 8) {
            return $phone;
        }

        return substr($phone, 0, 4) . '****' . substr($phone, -4);
    }

    /**
     * Ensure log directory exists
     */
    private function ensureDirectoryExists(): void
    {
        if (!is_dir($this->logPath)) {
            mkdir($this->logPath, 0777, true);
        }
    }

    /**
     * Get log statistics
     */
    public function getStats(string $date = null): array
    {
        $date = $date ?? date('Y-m-d');
        $logFile = $this->logPath . '/bot-' . $date . '.log';

        if (!file_exists($logFile)) {
            return [
                'date' => $date,
                'exists' => false,
                'total' => 0
            ];
        }

        $content = file_get_contents($logFile);
        $lines = array_filter(explode("\n", $content));

        $stats = [
            'date' => $date,
            'exists' => true,
            'total' => count($lines),
            'by_level' => [],
            'by_direction' => [
                'incoming' => 0,
                'outgoing' => 0
            ]
        ];

        foreach ($lines as $line) {
            $entry = json_decode($line, true);
            if (!$entry)
                continue;

            // Count by level
            $level = strtolower($entry['level'] ?? 'unknown');
            $stats['by_level'][$level] = ($stats['by_level'][$level] ?? 0) + 1;

            // Count by direction
            $direction = $entry['context']['direction'] ?? null;
            if ($direction && isset($stats['by_direction'][$direction])) {
                $stats['by_direction'][$direction]++;
            }
        }

        return $stats;
    }

    /**
     * Clean old log files
     */
    public function cleanOldLogs(int $daysToKeep = 30): int
    {
        $files = glob($this->logPath . '/bot-*.log');
        $cutoff = time() - ($daysToKeep * 86400);
        $deleted = 0;

        foreach ($files as $file) {
            if (filemtime($file) < $cutoff) {
                unlink($file);
                $deleted++;
            }
        }

        return $deleted;
    }
}
