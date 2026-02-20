<?php

namespace App\Http\Controllers;

use App\Services\DashboardApiService;
use App\Services\RateLimitService;

/**
 * Complaint Controller
 * 
 * Handles complaint creation with confirmation flow
 * Prevents spam by requiring explicit confirmation
 */
class ComplaintController
{
    private $dashboardApi;
    private $cachePath;

    public function __construct()
    {
        require_once __DIR__ . '/../../Services/DashboardApiService.php';
        $this->dashboardApi = new DashboardApiService();
        $this->cachePath = __DIR__ . '/../../../storage/framework/complaint_confirmations';
        $this->ensureDirectoryExists();
    }

    /**
     * Store pending complaint (awaiting confirmation)
     * 
     * POST /api/complaint/pending
     * 
     * Request body:
     * {
     *   "phone": "6281234567890",
     *   "message": "Jalan rusak di RT 05",
     *   "sender_name": "John Doe"
     * }
     * 
     * Response:
     * {
     *   "success": true,
     *   "message": "Konfirmasi diperlukan",
     *   "confirmation_id": "abc123",
     *   "preview": "Jalan rusak di RT 05"
     * }
     */
    public function storePending()
    {
        header('Content-Type: application/json');

        try {
            $input = file_get_contents('php://input');
            $data = json_decode($input, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid JSON payload'
                ]);
                return;
            }

            // Validate required fields
            if (empty($data['phone'])) {
                http_response_code(422);
                echo json_encode([
                    'success' => false,
                    'message' => 'Nomor telepon wajib diisi'
                ]);
                return;
            }

            if (empty($data['message'])) {
                http_response_code(422);
                echo json_encode([
                    'success' => false,
                    'message' => 'Pesan wajib diisi'
                ]);
                return;
            }

            // Generate confirmation ID
            $confirmationId = $this->generateConfirmationId();

            // Store pending complaint
            $pendingData = [
                'phone' => $data['phone'],
                'message' => $data['message'],
                'sender_name' => $data['sender_name'] ?? 'WhatsApp User',
                'category' => $data['category'] ?? 'pengaduan',
                'village_name' => $data['village_name'] ?? null,
                'created_at' => time(),
                'expires_at' => time() + 300  // 5 minutes expiry
            ];

            $this->storePendingComplaint($confirmationId, $pendingData);

            // Log pending complaint
            $this->logComplaintAction($data['phone'], 'pending_created', [
                'confirmation_id' => $confirmationId,
                'message_preview' => substr($data['message'], 0, 50)
            ]);

            http_response_code(200);
            echo json_encode([
                'success' => true,
                'message' => 'Konfirmasi diperlukan',
                'confirmation_id' => $confirmationId,
                'preview' => substr($data['message'], 0, 100),
                'expires_in' => 300
            ]);

        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Internal server error',
                'error' => $_ENV['APP_DEBUG'] === 'true' ? $e->getMessage() : null
            ]);
        }
    }

    /**
     * Confirm and create complaint
     * 
     * POST /api/complaint/confirm
     * 
     * Request body:
     * {
     *   "phone": "6281234567890",
     *   "confirmation_id": "abc123"
     * }
     */
    public function confirm()
    {
        header('Content-Type: application/json');

        try {
            $input = file_get_contents('php://input');
            $data = json_decode($input, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid JSON payload'
                ]);
                return;
            }

            // Validate required fields
            if (empty($data['phone'])) {
                http_response_code(422);
                echo json_encode([
                    'success' => false,
                    'message' => 'Nomor telepon wajib diisi'
                ]);
                return;
            }

            if (empty($data['confirmation_id'])) {
                http_response_code(422);
                echo json_encode([
                    'success' => false,
                    'message' => 'ID konfirmasi wajib diisi'
                ]);
                return;
            }

            // Get pending complaint
            $pendingComplaint = $this->getPendingComplaint($data['confirmation_id']);

            if (!$pendingComplaint) {
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'message' => 'Konfirmasi tidak ditemukan atau sudah kedaluwarsa'
                ]);
                return;
            }

            // Verify phone number matches
            $normalizedPhone = preg_replace('/[^0-9]/', '', $data['phone']);
            $pendingPhone = preg_replace('/[^0-9]/', '', $pendingComplaint['phone']);

            if ($normalizedPhone !== $pendingPhone) {
                http_response_code(403);
                echo json_encode([
                    'success' => false,
                    'message' => 'Nomor telepon tidak sesuai'
                ]);
                return;
            }

            // Check expiry
            if (time() > $pendingComplaint['expires_at']) {
                $this->deletePendingComplaint($data['confirmation_id']);
                http_response_code(410);
                echo json_encode([
                    'success' => false,
                    'message' => 'Konfirmasi sudah kedaluwarsa. Silakan kirim ulang pesan Anda.'
                ]);
                return;
            }

            // Create actual complaint
            $complaintData = [
                'phone' => $pendingComplaint['phone'],
                'message' => $pendingComplaint['message'],
                'sender_name' => $pendingComplaint['sender_name'],
                'category' => $pendingComplaint['category'],
                'village_name' => $pendingComplaint['village_name']
            ];

            $response = $this->dashboardApi->createInboxEntry($complaintData);

            if ($response['success']) {
                // Delete pending complaint
                $this->deletePendingComplaint($data['confirmation_id']);

                // Log successful creation
                $this->logComplaintAction($data['phone'], 'complaint_created', [
                    'confirmation_id' => $data['confirmation_id'],
                    'ticket' => $response['data']['ticket_number'] ?? null
                ]);

                http_response_code(201);
                echo json_encode([
                    'success' => true,
                    'message' => 'Pengaduan berhasil dibuat',
                    'data' => $response['data']
                ]);
            } else {
                http_response_code(502);
                echo json_encode([
                    'success' => false,
                    'message' => $response['error'] ?? 'Gagal membuat pengaduan'
                ]);
            }

        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Internal server error',
                'error' => $_ENV['APP_DEBUG'] === 'true' ? $e->getMessage() : null
            ]);
        }
    }

    /**
     * Cancel pending complaint
     * 
     * POST /api/complaint/cancel
     */
    public function cancel()
    {
        header('Content-Type: application/json');

        try {
            $input = file_get_contents('php://input');
            $data = json_decode($input, true);

            if (empty($data['confirmation_id'])) {
                http_response_code(422);
                echo json_encode([
                    'success' => false,
                    'message' => 'ID konfirmasi wajib diisi'
                ]);
                return;
            }

            $deleted = $this->deletePendingComplaint($data['confirmation_id']);

            if ($deleted) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Pengaduan dibatalkan'
                ]);
            } else {
                echo json_encode([
                    'success' => true,
                    'message' => 'Tidak ada pengaduan yang dibatalkan'
                ]);
            }

        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Internal server error'
            ]);
        }
    }

    /**
     * Check if there's a pending confirmation for phone number
     * 
     * GET /api/complaint/pending?phone=6281234567890
     */
    public function checkPending()
    {
        header('Content-Type: application/json');

        try {
            $phone = $_GET['phone'] ?? '';

            if (empty($phone)) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'Parameter phone wajib diisi'
                ]);
                return;
            }

            $pending = $this->findPendingByPhone($phone);

            if ($pending) {
                echo json_encode([
                    'success' => true,
                    'has_pending' => true,
                    'data' => [
                        'confirmation_id' => $pending['confirmation_id'],
                        'message_preview' => substr($pending['message'], 0, 50),
                        'expires_in' => max(0, $pending['expires_at'] - time())
                    ]
                ]);
            } else {
                echo json_encode([
                    'success' => true,
                    'has_pending' => false
                ]);
            }

        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Internal server error'
            ]);
        }
    }

    /**
     * Generate unique confirmation ID
     */
    private function generateConfirmationId(): string
    {
        return bin2hex(random_bytes(8));
    }

    /**
     * Store pending complaint
     */
    private function storePendingComplaint(string $id, array $data): void
    {
        $file = $this->cachePath . '/' . $id . '.json';
        file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));
    }

    /**
     * Get pending complaint
     */
    private function getPendingComplaint(string $id): ?array
    {
        $file = $this->cachePath . '/' . $id . '.json';

        if (!file_exists($file)) {
            return null;
        }

        return json_decode(file_get_contents($file), true);
    }

    /**
     * Delete pending complaint
     */
    private function deletePendingComplaint(string $id): bool
    {
        $file = $this->cachePath . '/' . $id . '.json';

        if (file_exists($file)) {
            unlink($file);
            return true;
        }

        return false;
    }

    /**
     * Find pending complaint by phone
     */
    private function findPendingByPhone(string $phone): ?array
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        $files = glob($this->cachePath . '/*.json');

        foreach ($files as $file) {
            $data = json_decode(file_get_contents($file), true);
            $pendingPhone = preg_replace('/[^0-9]/', '', $data['phone'] ?? '');

            if ($phone === $pendingPhone) {
                // Check if expired
                if (time() > $data['expires_at']) {
                    unlink($file);
                    continue;
                }

                $data['confirmation_id'] = basename($file, '.json');
                return $data;
            }
        }

        return null;
    }

    /**
     * Ensure cache directory exists
     */
    private function ensureDirectoryExists(): void
    {
        if (!is_dir($this->cachePath)) {
            mkdir($this->cachePath, 0777, true);
        }
    }

    /**
     * Log complaint action
     */
    private function logComplaintAction(string $phone, string $action, array $data): void
    {
        $logDir = __DIR__ . '/../../../storage/logs/complaints';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0777, true);
        }

        $logFile = $logDir . '/actions-' . date('Y-m-d') . '.log';

        $logEntry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'phone' => $phone,
            'action' => $action,
            'data' => $data,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ];

        file_put_contents(
            $logFile,
            json_encode($logEntry, JSON_PRETTY_PRINT) . "\n---\n",
            FILE_APPEND
        );
    }
}
