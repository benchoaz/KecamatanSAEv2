<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class WhatsAppReplyController extends Controller
{
    /**
     * Send reply to WhatsApp via n8n webhook
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function send(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|regex:/^[0-9+]+$/',
            'message' => 'required|string|max:2000',
            'type' => 'required|in:status_update,faq_match,auto_reply,manual_reply,help,command',
            'service_id' => 'nullable|integer|exists:public_services,id',
            'uuid' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $phoneNormalized = $this->normalizePhoneNumber($request->phone);
            
            // 1. PRIMARY: Active WhatsApp Provider (WAHA / Fonnte / UltraMsg / Generic HTTP)
            try {
                $provider = \App\Services\WhatsApp\WhatsAppManager::driver();
                $result = $provider->sendMessage($phoneNormalized, $request->message);

                if ($result['success'] ?? false) {
                    Log::info('WhatsApp reply sent successfully via active provider', [
                        'provider' => $provider->getProviderType(),
                        'phone' => $phoneNormalized,
                        'type' => $request->type,
                        'service_id' => $request->service_id,
                        'uuid' => $request->uuid,
                    ]);

                    return response()->json([
                        'success' => true,
                        'message' => 'Reply sent successfully',
                        'data' => [
                            'phone' => $phoneNormalized,
                            'type' => $request->type,
                            'sent_at' => now()->toISOString()
                        ]
                    ]);
                }
                
                Log::warning('WhatsApp provider failed, falling back to n8n', [
                    'error' => $result['message'] ?? 'Unknown error'
                ]);
            } catch (\Exception $e) {
                Log::warning('WhatsApp provider threw exception, falling back to n8n', [
                    'error' => $e->getMessage()
                ]);
            }

            // 2. FALLBACK: n8n webhook
            $n8nWebhookUrl = config('services.n8n.reply_webhook_url', env('N8N_REPLY_WEBHOOK_URL'));

            if (empty($n8nWebhookUrl)) {
                Log::error('N8N_REPLY_WEBHOOK_URL not configured and primary provider failed');
                return response()->json([
                    'success' => false,
                    'message' => 'WhatsApp service not configured or failed'
                ], 500);
            }

            // Prepare payload for n8n with enhanced compatibility
            $payload = [
                'phone' => $phoneNormalized,
                'chatId' => $phoneNormalized . '@c.us',
                'message' => $request->message,
                'msg' => $request->message,
                'replyText' => $request->message,
                'reply' => $request->message,
                'type' => $request->type,
                'timestamp' => now()->toISOString(),
            ];

            // Add optional fields
            if ($request->filled('service_id')) {
                $payload['service_id'] = $request->service_id;
            }
            if ($request->filled('uuid')) {
                $payload['uuid'] = $request->uuid;
            }

            // Send to n8n webhook
            $response = Http::timeout(10)->post($n8nWebhookUrl, $payload);

            if (!$response->successful()) {
                Log::error('Failed to send WhatsApp reply via n8n (fallback)', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'payload' => $payload
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send reply via primary provider AND n8n fallback',
                    'n8n_status' => $response->status()
                ], 502);
            }

            // Log successful reply
            Log::info('WhatsApp reply sent successfully via n8n fallback', [
                'phone' => $phoneNormalized,
                'type' => $request->type,
                'service_id' => $request->service_id,
                'uuid' => $request->uuid,
                'n8n_response' => $response->json()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Reply sent successfully',
                'data' => [
                    'phone' => $request->phone,
                    'type' => $request->type,
                    'sent_at' => now()->toISOString()
                ]
            ]);

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('Connection error sending WhatsApp reply', [
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Connection error: Unable to reach n8n service'
            ], 503);

        } catch (\Exception $e) {
            Log::error('Unexpected error sending WhatsApp reply', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Internal server error',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Send bulk reply to multiple phone numbers
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendBulk(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phones' => 'required|array|min:1|max:100',
            'phones.*' => 'required|string|regex:/^[0-9+]+$/',
            'message' => 'required|string|max:2000',
            'type' => 'required|in:announcement,broadcast',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $results = [];
        $successCount = 0;
        $failureCount = 0;

        foreach ($request->phones as $phone) {
            try {
                $response = $this->send(new Request([
                    'phone' => $phone,
                    'message' => $request->message,
                    'type' => $request->type
                ]));

                $results[$phone] = [
                    'success' => $response->getStatusCode() === 200,
                    'status' => $response->getStatusCode()
                ];

                if ($response->getStatusCode() === 200) {
                    $successCount++;
                } else {
                    $failureCount++;
                }

            } catch (\Exception $e) {
                $results[$phone] = [
                    'success' => false,
                    'error' => $e->getMessage()
                ];
                $failureCount++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Bulk reply completed: {$successCount} sent, {$failureCount} failed",
            'summary' => [
                'total' => count($request->phones),
                'success' => $successCount,
                'failed' => $failureCount
            ],
            'results' => $results
        ]);
    }

    /**
     * Test WhatsApp connection
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function testConnection(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|regex:/^[0-9+]+$/',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $response = $this->send(new Request([
                'phone' => $request->phone,
                'message' => '🧪 Test pesan dari Dashboard Kecamatan. Jika Anda menerima pesan ini, koneksi WhatsApp berhasil!',
                'type' => 'manual_reply'
            ]));

            return response()->json([
                'success' => $response->getStatusCode() === 200,
                'message' => $response->getStatusCode() === 200
                    ? 'Test message sent successfully'
                    : 'Failed to send test message'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Test failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Normalize phone number to international format
     * 
     * @param string $phone
     * @return string
     */
    private function normalizePhoneNumber($phone)
    {
        // Remove all non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // If starts with 0, replace with 62 (Indonesia country code)
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        return $phone;
    }

    /**
     * Get WhatsApp service status
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStatus()
    {
        $n8nWebhookUrl = config('services.n8n.reply_webhook_url', env('N8N_REPLY_WEBHOOK_URL'));

        return response()->json([
            'success' => true,
            'service' => 'whatsapp-reply',
            'configured' => !empty($n8nWebhookUrl),
            'webhook_url' => !empty($n8nWebhookUrl) ? '***configured***' : null,
            'timestamp' => now()->toISOString()
        ]);
    }
}
