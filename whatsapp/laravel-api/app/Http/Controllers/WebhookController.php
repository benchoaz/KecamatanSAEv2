<?php

namespace App\Http\Controllers;

use App\Services\DashboardApiService;

class WebhookController
{
    private $dashboardApi;

    public function __construct()
    {
        require_once __DIR__ . '/../../Services/DashboardApiService.php';
        $this->dashboardApi = new DashboardApiService();
    }

    public function handleN8nWebhook()
    {
        header('Content-Type: application/json');

        try {
            // Get JSON payload from n8n
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
            $validationErrors = $this->validatePayload($data);
            if (!empty($validationErrors)) {
                http_response_code(422);
                echo json_encode([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validationErrors
                ]);
                return;
            }

            // Transform WhatsApp data to PublicService format
            $publicServiceData = $this->transformToPublicService($data);

            // Send to dashboard API
            $response = $this->dashboardApi->createInboxEntry($publicServiceData);

            // Log the transaction
            $this->logTransaction($data, $publicServiceData, $response);

            if ($response['success']) {
                http_response_code(201);
                echo json_encode([
                    'success' => true,
                    'message' => 'WhatsApp message forwarded to dashboard successfully',
                    'data' => $response['data'] ?? null
                ]);
            } else {
                http_response_code(502);
                echo json_encode([
                    'success' => false,
                    'message' => 'Failed to forward to dashboard',
                    'error' => $response['error'] ?? 'Unknown error'
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
     * Search UMKM Products
     */
    public function searchUmkm()
    {
        header('Content-Type: application/json');
        try {
            $query = $_GET['q'] ?? '';
            $response = $this->dashboardApi->searchUmkm($query);
            echo json_encode($response);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Search Job Vacancies
     */
    public function searchLoker()
    {
        header('Content-Type: application/json');
        try {
            $query = $_GET['q'] ?? '';
            $response = $this->dashboardApi->searchLoker($query);
            echo json_encode($response);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Search Jasa/Services
     */
    public function searchJasa()
    {
        header('Content-Type: application/json');
        try {
            $query = $_GET['q'] ?? '';
            $response = $this->dashboardApi->searchJasa($query);
            echo json_encode($response);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Get Bot Config & AI status
     */
    public function getConfig()
    {
        header('Content-Type: application/json');
        try {
            $config = $this->dashboardApi->getBotConfig();
            echo json_encode(['success' => true, 'data' => $config]);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Log conversation context
     */
    public function logMessage()
    {
        header('Content-Type: application/json');
        try {
            $input = file_get_contents('php://input');
            $data = json_decode($input, true);

            if (!empty($data['chatId']) && !empty($data['message'])) {
                $this->dashboardApi->addConversationHistory(
                    $data['chatId'],
                    $data['role'] ?? 'user',
                    $data['message']
                );
            }
            echo json_encode(['success' => true]);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Search FAQ for WhatsApp bot
     */
    public function searchFaq()
    {
        header('Content-Type: application/json');

        try {
            $query = $_GET['q'] ?? '';

            if (empty($query)) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'Query parameter "q" is required'
                ]);
                return;
            }

            $response = $this->dashboardApi->searchFaq($query);

            if ($response['success']) {
                http_response_code(200);
                echo json_encode([
                    'success' => true,
                    'data' => $response['data']
                ]);
            } else {
                http_response_code(502);
                echo json_encode([
                    'success' => false,
                    'message' => 'Failed to search FAQ',
                    'error' => $response['error'] ?? 'Unknown error'
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
     * Check status for WhatsApp bot
     * 
     * @return void
     */
    public function checkStatus()
    {
        header('Content-Type: application/json');

        try {
            $identifier = $_GET['identifier'] ?? '';

            if (empty($identifier)) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'Query parameter "identifier" is required'
                ]);
                return;
            }

            $response = $this->dashboardApi->checkStatus($identifier);

            if ($response['success']) {
                http_response_code(200);
                echo json_encode([
                    'success' => true,
                    'data' => $response['data']
                ]);
            } else {
                http_response_code(502);
                echo json_encode([
                    'success' => false,
                    'message' => 'Failed to check status',
                    'error' => $response['error'] ?? 'Unknown error'
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
     * Send reply to WhatsApp
     * 
     * @return void
     */
    public function sendReply()
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
            if (empty($data['phone']) || empty($data['message'])) {
                http_response_code(422);
                echo json_encode([
                    'success' => false,
                    'message' => 'Phone and message are required'
                ]);
                return;
            }

            $response = $this->dashboardApi->sendReply($data);

            if ($response['success']) {
                http_response_code(200);
                echo json_encode([
                    'success' => true,
                    'message' => 'Reply sent successfully',
                    'data' => $response['data']
                ]);
            } else {
                http_response_code(502);
                echo json_encode([
                    'success' => false,
                    'message' => 'Failed to send reply',
                    'error' => $response['error'] ?? 'Unknown error'
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

    private function validatePayload($data)
    {
        $errors = [];

        if (empty($data['phone'])) {
            $errors['phone'] = 'Phone number is required';
        }

        if (empty($data['message'])) {
            $errors['message'] = 'Message content is required';
        }

        if (empty($data['category'])) {
            $errors['category'] = 'Category is required';
        } elseif (!in_array($data['category'], ['pengaduan', 'pelayanan', 'umkm', 'loker'])) {
            $errors['category'] = 'Invalid category. Must be: pengaduan, pelayanan, umkm, or loker';
        }

        if (empty($data['sender_name'])) {
            $errors['sender_name'] = 'Sender name is required';
        }

        return $errors;
    }

    private function transformToPublicService($data)
    {
        // Clean phone number (remove + and spaces)
        $phone = preg_replace('/[^0-9]/', '', $data['phone']);

        // Generate UUID
        $uuid = $this->generateUuid();

        return [
            'uuid' => $uuid,
            'category' => $data['category'],
            'source' => 'whatsapp',
            'whatsapp' => $phone,
            'nama_pemohon' => $data['sender_name'] ?? 'WhatsApp User',
            'uraian' => $data['message'],
            'jenis_layanan' => $this->mapCategoryToJenisLayanan($data['category']),
            'status' => 'menunggu_verifikasi',
            'desa_id' => null, // Will be assigned by admin later
            'nama_desa_manual' => $data['village_name'] ?? null,
        ];
    }

    private function mapCategoryToJenisLayanan($category)
    {
        return match ($category) {
            'pengaduan' => 'Pengaduan Umum',
            'pelayanan' => 'Pelayanan Administrasi',
            'umkm' => 'UMKM Rakyat',
            'loker' => 'Lowongan Kerja',
            default => 'Lainnya'
        };
    }

    private function generateUuid()
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }

    private function logTransaction($input, $transformed, $response)
    {
        $logDir = __DIR__ . '/../../../storage/logs';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0777, true);
        }

        $logFile = $logDir . '/transactions-' . date('Y-m-d') . '.log';

        $logEntry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'input' => $input,
            'transformed' => $transformed,
            'response' => $response
        ];

        file_put_contents(
            $logFile,
            json_encode($logEntry, JSON_PRETTY_PRINT) . "\n---\n",
            FILE_APPEND
        );
    }
}
