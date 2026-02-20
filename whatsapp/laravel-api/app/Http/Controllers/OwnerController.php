<?php

namespace App\Http\Controllers;

use App\Services\DashboardApiService;
use App\Services\OwnerPinService;
use App\Services\RateLimitService;

/**
 * Owner Controller
 * 
 * Handles owner verification and listing toggle operations
 * Requires PIN verification for security
 */
class OwnerController
{
    private $dashboardApi;
    private $pinService;

    public function __construct()
    {
        require_once __DIR__ . '/../../Services/DashboardApiService.php';
        require_once __DIR__ . '/../../Services/OwnerPinService.php';
        $this->dashboardApi = new DashboardApiService();
        $this->pinService = new OwnerPinService();
    }

    /**
     * Verify owner PIN
     * 
     * POST /api/owner/verify-pin
     * 
     * Request body:
     * {
     *   "phone": "6281234567890",
     *   "pin": "123456"
     * }
     * 
     * Response:
     * {
     *   "success": true,
     *   "data": {
     *     "owner_id": 123,
     *     "listings": [...]
     *   }
     * }
     */
    public function verifyPin()
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

            if (empty($data['pin'])) {
                http_response_code(422);
                echo json_encode([
                    'success' => false,
                    'message' => 'PIN wajib diisi'
                ]);
                return;
            }

            // Validate PIN format (6 digits)
            if (!preg_match('/^[0-9]{6}$/', $data['pin'])) {
                http_response_code(422);
                echo json_encode([
                    'success' => false,
                    'message' => 'PIN harus 6 digit angka'
                ]);
                return;
            }

            // Verify PIN using local PIN service
            $response = $this->pinService->verifyPin($data['phone'], $data['pin']);

            if ($response['success']) {
                // Log successful verification
                $this->logOwnerAction($data['phone'], 'pin_verified', $response['data']);

                http_response_code(200);
                echo json_encode([
                    'success' => true,
                    'message' => 'PIN verified successfully',
                    'data' => $response['data']
                ]);
            } else {
                // Log failed attempt
                $this->logOwnerAction($data['phone'], 'pin_failed', ['pin' => '******']);

                http_response_code(401);
                echo json_encode([
                    'success' => false,
                    'message' => $response['message'] ?? 'PIN salah atau nomor tidak terdaftar sebagai owner'
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
     * Toggle listing visibility
     * 
     * POST /api/owner/toggle-listing
     * 
     * Request body:
     * {
     *   "phone": "6281234567890",
     *   "pin": "123456",
     *   "listing_id": 123,
     *   "listing_type": "umkm",
     *   "action": "close"  // "open" or "close"
     * }
     */
    public function toggleListing()
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
            $errors = $this->validateToggleRequest($data);
            if (!empty($errors)) {
                http_response_code(422);
                echo json_encode([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $errors
                ]);
                return;
            }

            // First verify PIN using local PIN service
            $pinResponse = $this->pinService->verifyPin($data['phone'], $data['pin']);

            if (!$pinResponse['success']) {
                http_response_code(401);
                echo json_encode([
                    'success' => false,
                    'message' => $pinResponse['message'] ?? 'PIN salah atau nomor tidak terdaftar sebagai owner'
                ]);
                return;
            }

            // Verify ownership
            $ownershipVerified = $this->verifyOwnership(
                $pinResponse['data']['listings'] ?? [],
                $data['listing_id'],
                $data['listing_type']
            );

            if (!$ownershipVerified) {
                http_response_code(403);
                echo json_encode([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses ke listing ini'
                ]);
                return;
            }

            // Toggle listing
            $response = $this->dashboardApi->toggleListing(
                $data['listing_id'],
                $data['listing_type'],
                $data['action']
            );

            if ($response['success']) {
                // Log successful toggle
                $this->logOwnerAction($data['phone'], 'listing_toggled', [
                    'listing_id' => $data['listing_id'],
                    'listing_type' => $data['listing_type'],
                    'action' => $data['action']
                ]);

                $statusText = $data['action'] === 'close' ? 'ditutup' : 'dibuka';

                http_response_code(200);
                echo json_encode([
                    'success' => true,
                    'message' => "Lapak berhasil {$statusText}",
                    'data' => $response['data']
                ]);
            } else {
                http_response_code(502);
                echo json_encode([
                    'success' => false,
                    'message' => $response['message'] ?? 'Gagal mengubah status lapak'
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
     * Get owner's listings
     * 
     * GET /api/owner/listings?phone=6281234567890
     */
    public function getListings()
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

            $response = $this->dashboardApi->getOwnerListings($phone);

            if ($response['success']) {
                http_response_code(200);
                echo json_encode([
                    'success' => true,
                    'data' => $response['data']
                ]);
            } else {
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'message' => $response['message'] ?? 'Tidak ada listing ditemukan'
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
     * Request new PIN (for owners who forgot their PIN)
     * 
     * POST /api/owner/request-pin
     * 
     * Request body:
     * {
     *   "phone": "6281234567890"
     * }
     * 
     * Response:
     * {
     *   "success": true,
     *   "message": "PIN baru telah dikirim via WhatsApp",
     *   "data": {
     *     "pin": "123456",  // Only for WhatsApp gateway to send
     *     "listings": [...]
     *   }
     * }
     */
    public function requestNewPin()
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

            $phone = preg_replace('/[^0-9]/', '', $data['phone']);

            // Check if phone is registered as owner in dashboard
            $ownerCheck = $this->dashboardApi->getOwnerListings($phone);

            if (!$ownerCheck['success'] || empty($ownerCheck['data']['listings'])) {
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'message' => 'Nomor ini tidak terdaftar sebagai owner UMKM/Jasa'
                ]);
                return;
            }

            // Generate and store new PIN for each listing
            $newPin = $this->pinService->generatePin();

            foreach ($ownerCheck['data']['listings'] as $listing) {
                $this->pinService->storePin(
                    $phone,
                    $newPin,
                    $listing['type'],
                    $listing['id']
                );
            }

            // Log PIN request
            $this->logOwnerAction($phone, 'pin_requested', [
                'listings_count' => count($ownerCheck['data']['listings'])
            ]);

            // Return PIN (will be sent via WhatsApp by n8n)
            http_response_code(200);
            echo json_encode([
                'success' => true,
                'message' => 'PIN baru berhasil dibuat',
                'data' => [
                    'pin' => $newPin,
                    'phone' => $phone,
                    'listings' => $ownerCheck['data']['listings']
                ]
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
     * Generate PIN for new UMKM/Jasa registration
     * Called by dashboard when approving new listing
     * 
     * POST /api/owner/generate-pin
     * 
     * Request body:
     * {
     *   "phone": "6281234567890",
     *   "listing_id": 123,
     *   "listing_type": "umkm"
     * }
     */
    public function generatePinForListing()
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
            $errors = [];
            if (empty($data['phone'])) {
                $errors['phone'] = 'Nomor telepon wajib diisi';
            }
            if (empty($data['listing_id'])) {
                $errors['listing_id'] = 'ID listing wajib diisi';
            }
            if (empty($data['listing_type'])) {
                $errors['listing_type'] = 'Tipe listing wajib diisi';
            } elseif (!in_array($data['listing_type'], ['umkm', 'jasa'])) {
                $errors['listing_type'] = 'Tipe listing harus umkm atau jasa';
            }

            if (!empty($errors)) {
                http_response_code(422);
                echo json_encode([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $errors
                ]);
                return;
            }

            $phone = preg_replace('/[^0-9]/', '', $data['phone']);

            // Check if owner already has a PIN
            $existingPinData = $this->pinService->getPinData($phone);

            if ($existingPinData && !empty($existingPinData['listings'])) {
                // Owner already has PIN, just add new listing with same PIN
                // Get existing PIN from first listing (they all use same PIN)
                $existingPin = 'existing'; // We don't return the actual PIN

                $this->pinService->storePin(
                    $phone,
                    $this->pinService->generatePin(), // Will be overwritten
                    $data['listing_type'],
                    $data['listing_id']
                );

                http_response_code(200);
                echo json_encode([
                    'success' => true,
                    'message' => 'Listing ditambahkan ke akun owner yang sudah ada',
                    'data' => [
                        'phone' => $phone,
                        'listing_id' => $data['listing_id'],
                        'listing_type' => $data['listing_type'],
                        'is_new_owner' => false
                    ]
                ]);
                return;
            }

            // Generate new PIN for new owner
            $newPin = $this->pinService->generatePin();

            $this->pinService->storePin(
                $phone,
                $newPin,
                $data['listing_type'],
                $data['listing_id']
            );

            // Log PIN generation
            $this->logOwnerAction($phone, 'pin_generated', [
                'listing_id' => $data['listing_id'],
                'listing_type' => $data['listing_type']
            ]);

            // Return PIN (will be sent via WhatsApp by dashboard)
            http_response_code(200);
            echo json_encode([
                'success' => true,
                'message' => 'PIN berhasil dibuat untuk owner baru',
                'data' => [
                    'pin' => $newPin,
                    'phone' => $phone,
                    'listing_id' => $data['listing_id'],
                    'listing_type' => $data['listing_type'],
                    'is_new_owner' => true
                ]
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
     * Validate toggle request data
     */
    private function validateToggleRequest($data): array
    {
        $errors = [];

        if (empty($data['phone'])) {
            $errors['phone'] = 'Nomor telepon wajib diisi';
        }

        if (empty($data['pin'])) {
            $errors['pin'] = 'PIN wajib diisi';
        }

        if (empty($data['listing_id'])) {
            $errors['listing_id'] = 'ID listing wajib diisi';
        }

        if (empty($data['listing_type'])) {
            $errors['listing_type'] = 'Tipe listing wajib diisi';
        } elseif (!in_array($data['listing_type'], ['umkm', 'jasa'])) {
            $errors['listing_type'] = 'Tipe listing harus umkm atau jasa';
        }

        if (empty($data['action'])) {
            $errors['action'] = 'Aksi wajib diisi';
        } elseif (!in_array($data['action'], ['open', 'close'])) {
            $errors['action'] = 'Aksi harus open atau close';
        }

        return $errors;
    }

    /**
     * Verify that the listing belongs to the owner
     */
    private function verifyOwnership(array $listings, $listingId, $listingType): bool
    {
        foreach ($listings as $listing) {
            if ($listing['id'] == $listingId && $listing['type'] === $listingType) {
                return true;
            }
        }
        return false;
    }

    /**
     * Log owner action for audit trail
     */
    private function logOwnerAction(string $phone, string $action, array $data): void
    {
        $logDir = __DIR__ . '/../../../storage/logs/owner';
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
