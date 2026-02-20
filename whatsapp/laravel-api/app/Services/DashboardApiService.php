<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class DashboardApiService
{
    private $client;
    private $baseUrl;
    private $apiToken;
    private $cache;

    public function __construct()
    {
        $this->baseUrl = rtrim($_ENV['DASHBOARD_API_URL'], '/');
        $this->apiToken = $_ENV['DASHBOARD_API_TOKEN'];

        require_once __DIR__ . '/CacheService.php';
        $this->cache = new CacheService();

        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'timeout' => 30,
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->apiToken
            ]
        ]);
    }

    /**
     * Get Bot Configuration from Dashboard
     */
    public function getBotConfig()
    {
        try {
            $response = $this->client->get('/api/v1/external/config');
            $body = json_decode($response->getBody()->getContents(), true);
            return $body['data'] ?? ['is_ai_active' => false];
        } catch (\Exception $e) {
            return ['is_ai_active' => false];
        }
    }

    public function addConversationHistory($chatId, $role, $message)
    {
        $this->cache->addHistory($chatId, $role, $message);
    }

    public function getConversationHistory($chatId)
    {
        return $this->cache->getHistory($chatId);
    }

    /**
     * Search UMKM Products
     */
    public function searchUmkm($query)
    {
        // Try to get from cache first
        $cached = $this->cache->get($query, 'umkm');
        if ($cached)
            return ['success' => true, 'data' => $cached, 'cached' => true];

        try {
            $response = $this->client->get('/api/v1/external/umkm/search', [
                'query' => ['q' => $query]
            ]);

            $body = json_decode($response->getBody()->getContents(), true);

            // Clean data for n8n (extract nested data if exists)
            $result = $body['data'] ?? $body;

            // Save to cache
            if (!empty($result)) {
                $this->cache->set($query, 'umkm', $result);
            }

            return ['success' => true, 'data' => $result];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Search Job Vacancies
     */
    public function searchLoker($query)
    {
        $cached = $this->cache->get($query, 'loker');
        if ($cached)
            return ['success' => true, 'data' => $cached, 'cached' => true];

        try {
            $response = $this->client->get('/api/v1/external/loker/search', [
                'query' => ['q' => $query]
            ]);

            $body = json_decode($response->getBody()->getContents(), true);
            $result = $body['data'] ?? $body;

            if (!empty($result)) {
                $this->cache->set($query, 'loker', $result);
            }

            return ['success' => true, 'data' => $result];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function createInboxEntry($data)
    {
        try {
            $response = $this->client->post('/api/inbox/whatsapp', [
                'json' => $data
            ]);

            $body = json_decode($response->getBody()->getContents(), true);

            return [
                'success' => true,
                'data' => $body
            ];

        } catch (RequestException $e) {
            $statusCode = $e->getResponse() ? $e->getResponse()->getStatusCode() : 0;
            $errorBody = $e->getResponse() ? $e->getResponse()->getBody()->getContents() : '';

            $this->logError('API Request Failed', [
                'status_code' => $statusCode,
                'error_body' => $errorBody,
                'data_sent' => $data
            ]);

            return [
                'success' => false,
                'error' => 'Dashboard API error: ' . $statusCode,
                'details' => json_decode($errorBody, true)
            ];

        } catch (\Exception $e) {
            $this->logError('Unexpected Error', [
                'message' => $e->getMessage(),
                'data_sent' => $data
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Search FAQ by query
     * 
     * @param string $query
     * @return array
     */
    public function searchFaq($query)
    {
        try {
            $response = $this->client->get('/api/faq/search', [
                'query' => ['q' => $query]
            ]);

            $body = json_decode($response->getBody()->getContents(), true);

            return [
                'success' => true,
                'data' => $body
            ];

        } catch (RequestException $e) {
            $statusCode = $e->getResponse() ? $e->getResponse()->getStatusCode() : 0;
            $errorBody = $e->getResponse() ? $e->getResponse()->getBody()->getContents() : '';

            $this->logError('FAQ Search Failed', [
                'status_code' => $statusCode,
                'error_body' => $errorBody,
                'query' => $query
            ]);

            return [
                'success' => false,
                'error' => 'FAQ search failed: ' . $statusCode,
                'details' => json_decode($errorBody, true)
            ];

        } catch (\Exception $e) {
            $this->logError('FAQ Search Unexpected Error', [
                'message' => $e->getMessage(),
                'query' => $query
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Check status by identifier (UUID or phone)
     * 
     * @param string $identifier
     * @return array
     */
    public function checkStatus($identifier)
    {
        try {
            $response = $this->client->get('/api/status/check', [
                'query' => ['identifier' => $identifier]
            ]);

            $body = json_decode($response->getBody()->getContents(), true);

            return [
                'success' => true,
                'data' => $body
            ];

        } catch (RequestException $e) {
            $statusCode = $e->getResponse() ? $e->getResponse()->getStatusCode() : 0;
            $errorBody = $e->getResponse() ? $e->getResponse()->getBody()->getContents() : '';

            $this->logError('Status Check Failed', [
                'status_code' => $statusCode,
                'error_body' => $errorBody,
                'identifier' => $identifier
            ]);

            return [
                'success' => false,
                'error' => 'Status check failed: ' . $statusCode,
                'details' => json_decode($errorBody, true)
            ];

        } catch (\Exception $e) {
            $this->logError('Status Check Unexpected Error', [
                'message' => $e->getMessage(),
                'identifier' => $identifier
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Send reply to WhatsApp
     * 
     * @param array $data
     * @return array
     */
    public function sendReply($data)
    {
        try {
            $response = $this->client->post('/api/reply/send', [
                'json' => $data
            ]);

            $body = json_decode($response->getBody()->getContents(), true);

            return [
                'success' => true,
                'data' => $body
            ];

        } catch (RequestException $e) {
            $statusCode = $e->getResponse() ? $e->getResponse()->getStatusCode() : 0;
            $errorBody = $e->getResponse() ? $e->getResponse()->getBody()->getContents() : '';

            $this->logError('Send Reply Failed', [
                'status_code' => $statusCode,
                'error_body' => $errorBody,
                'data_sent' => $data
            ]);

            return [
                'success' => false,
                'error' => 'Send reply failed: ' . $statusCode,
                'details' => json_decode($errorBody, true)
            ];

        } catch (\Exception $e) {
            $this->logError('Send Reply Unexpected Error', [
                'message' => $e->getMessage(),
                'data_sent' => $data
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Verify owner PIN
     * 
     * @param string $phone
     * @param string $pin
     * @return array
     */
    public function verifyOwnerPin($phone, $pin)
    {
        try {
            $response = $this->client->post('/api/v1/external/owner/verify-pin', [
                'json' => [
                    'phone' => $phone,
                    'pin' => $pin
                ]
            ]);

            $body = json_decode($response->getBody()->getContents(), true);

            return [
                'success' => true,
                'data' => $body['data'] ?? $body
            ];

        } catch (RequestException $e) {
            $statusCode = $e->getResponse() ? $e->getResponse()->getStatusCode() : 0;
            $errorBody = $e->getResponse() ? $e->getResponse()->getBody()->getContents() : '';

            $this->logError('Owner PIN Verification Failed', [
                'status_code' => $statusCode,
                'error_body' => $errorBody,
                'phone' => $phone
            ]);

            $errorData = json_decode($errorBody, true);

            return [
                'success' => false,
                'message' => $errorData['message'] ?? 'PIN salah atau nomor tidak terdaftar sebagai owner'
            ];

        } catch (\Exception $e) {
            $this->logError('Owner PIN Verification Unexpected Error', [
                'message' => $e->getMessage(),
                'phone' => $phone
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Toggle listing visibility
     * 
     * @param int $listingId
     * @param string $listingType (umkm, jasa)
     * @param string $action (open, close)
     * @return array
     */
    public function toggleListing($listingId, $listingType, $action)
    {
        try {
            $response = $this->client->post('/api/v1/external/owner/toggle-listing', [
                'json' => [
                    'listing_id' => $listingId,
                    'listing_type' => $listingType,
                    'action' => $action
                ]
            ]);

            $body = json_decode($response->getBody()->getContents(), true);

            return [
                'success' => true,
                'data' => $body['data'] ?? $body
            ];

        } catch (RequestException $e) {
            $statusCode = $e->getResponse() ? $e->getResponse()->getStatusCode() : 0;
            $errorBody = $e->getResponse() ? $e->getResponse()->getBody()->getContents() : '';

            $this->logError('Toggle Listing Failed', [
                'status_code' => $statusCode,
                'error_body' => $errorBody,
                'listing_id' => $listingId,
                'listing_type' => $listingType,
                'action' => $action
            ]);

            $errorData = json_decode($errorBody, true);

            return [
                'success' => false,
                'message' => $errorData['message'] ?? 'Gagal mengubah status listing'
            ];

        } catch (\Exception $e) {
            $this->logError('Toggle Listing Unexpected Error', [
                'message' => $e->getMessage(),
                'listing_id' => $listingId
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Get owner's listings
     * 
     * @param string $phone
     * @return array
     */
    public function getOwnerListings($phone)
    {
        try {
            $response = $this->client->get('/api/v1/external/owner/listings', [
                'query' => ['phone' => $phone]
            ]);

            $body = json_decode($response->getBody()->getContents(), true);

            return [
                'success' => true,
                'data' => $body['data'] ?? $body
            ];

        } catch (RequestException $e) {
            $statusCode = $e->getResponse() ? $e->getResponse()->getStatusCode() : 0;
            $errorBody = $e->getResponse() ? $e->getResponse()->getBody()->getContents() : '';

            $this->logError('Get Owner Listings Failed', [
                'status_code' => $statusCode,
                'error_body' => $errorBody,
                'phone' => $phone
            ]);

            return [
                'success' => false,
                'message' => 'Tidak dapat mengambil data listing'
            ];

        } catch (\Exception $e) {
            $this->logError('Get Owner Listings Unexpected Error', [
                'message' => $e->getMessage(),
                'phone' => $phone
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Search Jasa (services)
     * 
     * @param string $query
     * @return array
     */
    public function searchJasa($query)
    {
        $cached = $this->cache->get($query, 'jasa');
        if ($cached) {
            return ['success' => true, 'data' => $cached, 'cached' => true];
        }

        try {
            $response = $this->client->get('/api/v1/external/jasa/search', [
                'query' => ['q' => $query]
            ]);

            $body = json_decode($response->getBody()->getContents(), true);
            $result = $body['data'] ?? $body;

            if (!empty($result)) {
                $this->cache->set($query, 'jasa', $result);
            }

            return ['success' => true, 'data' => $result];

        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Check document status by phone or ticket number
     * 
     * @param string $identifier
     * @return array
     */
    public function checkBerkas($identifier)
    {
        try {
            $response = $this->client->get('/api/v1/external/cek-berkas', [
                'query' => ['identifier' => $identifier]
            ]);

            $body = json_decode($response->getBody()->getContents(), true);

            return [
                'success' => true,
                'data' => $body['data'] ?? $body
            ];

        } catch (RequestException $e) {
            $statusCode = $e->getResponse() ? $e->getResponse()->getStatusCode() : 0;
            $errorBody = $e->getResponse() ? $e->getResponse()->getBody()->getContents() : '';

            $errorData = json_decode($errorBody, true);

            return [
                'success' => false,
                'message' => $errorData['message'] ?? 'Data tidak ditemukan'
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    private function logError($message, $context = [])
    {
        $logDir = __DIR__ . '/../../storage/logs';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0777, true);
        }

        $logFile = $logDir . '/api-errors-' . date('Y-m-d') . '.log';

        $logEntry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'message' => $message,
            'context' => $context
        ];

        file_put_contents(
            $logFile,
            json_encode($logEntry, JSON_PRETTY_PRINT) . "\n---\n",
            FILE_APPEND
        );
    }
}
