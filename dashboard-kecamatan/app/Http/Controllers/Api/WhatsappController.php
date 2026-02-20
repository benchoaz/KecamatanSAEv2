<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WhatsappSession;
use App\Models\WhatsappLog;
use App\Models\ModuleSetting;
use App\Services\WhatsApp\IntentHandler;
use App\Services\WhatsApp\StateHandler;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class WhatsappController extends Controller
{
    protected IntentHandler $intentHandler;
    protected StateHandler $stateHandler;

    public function __construct(IntentHandler $intentHandler, StateHandler $stateHandler)
    {
        $this->intentHandler = $intentHandler;
        $this->stateHandler = $stateHandler;
    }

    /**
     * Main WhatsApp message handler
     * This is the single endpoint that n8n calls
     */
    public function handle(Request $request): JsonResponse
    {
        // =====================================================
        // EMERGENCY DEBUG - Log everything before any processing
        // This uses emergency level to ensure it's always logged
        // =====================================================
        \Log::emergency('WhatsApp API Request Debug', [
            'raw_body' => $request->getContent(),
            'all_input' => $request->all(),
            'has_data_from' => $request->has('data.from'),
            'has_data_body' => $request->has('data.body'),
            'data_value' => $request->input('data'),
            'phone_direct' => $request->input('phone'),
            'message_direct' => $request->input('message'),
        ]);

        // =====================================================
        // DETAILED WAHA WEBHOOK LOGGING FOR DEBUGGING
        // =====================================================

        // 1. Log raw request body
        $rawContent = $request->getContent();
        \Log::info('=== WAHA WEBHOOK - RAW REQUEST BODY ===', [
            'raw_content' => $rawContent,
            'content_length' => strlen($rawContent),
        ]);

        // 2. Log all headers
        \Log::info('=== WAHA WEBHOOK - HEADERS ===', [
            'headers' => $request->headers->all(),
            'content_type' => $request->header('Content-Type'),
            'user_agent' => $request->header('User-Agent'),
        ]);

        // 3. Log JSON payload structure
        $jsonPayload = json_decode($rawContent, true);
        \Log::info('=== WAHA WEBHOOK - JSON PAYLOAD STRUCTURE ===', [
            'json_decode_success' => $jsonPayload !== null,
            'json_error' => json_last_error_msg(),
            'payload_keys' => is_array($jsonPayload) ? array_keys($jsonPayload) : 'NOT AN ARRAY',
            'full_payload' => $jsonPayload,
        ]);

        // 4. Log Laravel request data
        \Log::info('=== WAHA WEBHOOK - LARAVEL REQUEST DATA ===', [
            'request_all' => $request->all(),
            'request_input' => $request->input(),
            'request_method' => $request->method(),
            'request_url' => $request->url(),
            'request_ip' => $request->ip(),
        ]);

        // 5. Log specific fields we expect from WAHA
        \Log::info('=== WAHA WEBHOOK - EXPECTED FIELDS ===', [
            'phone_field' => $request->input('phone'),
            'message_field' => $request->input('message'),
            'from_field' => $request->input('from'),
            'body_field' => $request->input('body'),
            'text_field' => $request->input('text'),
            'data_field' => $request->input('data'),
            'event_field' => $request->input('event'),
            'session_field' => $request->input('session'),
        ]);

        // =====================================================
        // END DETAILED LOGGING
        // =====================================================

        // Log the incoming request for debugging
        \Log::info('WhatsApp API Request:', $request->all());

        // =====================================================
        // TRANSFORM WAHA WEBHOOK FORMAT
        // n8n sends: { phone: null, message: { payload: { from, body } } }
        // We need: { phone: string, message: string }
        // =====================================================

        $phone = $request->input('phone');
        $message = $request->input('message');

        // Check if message is an array (WAHA event object from n8n)
        if (is_array($message) && isset($message['payload'])) {
            $payload = $message['payload'];

            // Extract phone from payload.from
            if (isset($payload['from'])) {
                $phone = $payload['from'];
                // Strip @c.us or @s.whatsapp.net or @newsletter suffix
                $phone = preg_replace('/@(c\.us|s\.whatsapp\.net|newsletter)$/', '', $phone);
            }

            // Extract message text from payload.body
            $message = $payload['body'] ?? $payload['text'] ?? '';

            \Log::info('WAHA n8n format detected, transforming data', [
                'original_from' => $payload['from'] ?? null,
                'transformed_phone' => $phone,
                'message' => $message,
            ]);

            // Replace request data with transformed data
            $request->merge([
                'phone' => $phone,
                'message' => $message
            ]);
        }
        // Check if this is a direct WAHA webhook format (data.from and data.body)
        elseif ($request->has('data.from') && $request->has('data.body')) {
            $wahaData = $request->input('data');
            $phone = $wahaData['from'];
            // Strip @c.us or @s.whatsapp.net suffix from phone number
            $phone = preg_replace('/@(c\.us|s\.whatsapp\.net)$/', '', $phone);
            $message = $wahaData['body'] ?? $wahaData['text'] ?? '';

            \Log::info('WAHA webhook format detected, transforming data', [
                'original_from' => $wahaData['from'],
                'transformed_phone' => $phone,
                'message' => $message,
            ]);

            // Replace request data with transformed data
            $request->merge([
                'phone' => $phone,
                'message' => $message
            ]);
        }

        // Validate request
        $request->validate([
            'phone' => 'required|string',
            'message' => 'required|string',
        ]);

        $phone = $request->input('phone');
        $message = trim($request->input('message'));

        try {
            // Check maintenance mode
            if ($this->isMaintenanceMode()) {
                return $this->successResponse(
                    'Maaf, sistem sedang dalam pemeliharaan. Silakan coba beberapa saat lagi.'
                );
            }

            // Get or create session
            $session = WhatsappSession::getOrCreate($phone);

            // Clear stale sessions
            if ($session->isStale()) {
                $session->clear();
            }

            // Route to state handler or intent detector
            if ($session->isActive()) {
                $response = $this->stateHandler->handle($session, $message);
            } else {
                $response = $this->intentHandler->handle($phone, $message);
            }

            // =====================================================
            // CRITICAL FIX: Save state_update to database
            // The handlers return state_update but it wasn't being saved!
            // =====================================================
            if (isset($response['state_update'])) {
                if ($response['state_update'] === null) {
                    // Clear session if state_update is null
                    $session->clear();
                } else {
                    // Update session state
                    $session->updateState($response['state_update']);
                }
            }

            // Log interaction
            WhatsappLog::logInteraction(
                $phone,
                $message,
                $response['intent'] ?? null,
                $response['reply'] ?? null,
                $response['success'] ?? true
            );

            // Return response to n8n - n8n will handle sending to WAHA
            // This avoids network issues between Dashboard and WAHA containers
            $response['chatId'] = $request->input('chatId');
            return response()->json($response);
        } catch (\Exception $e) {
            // Log error
            WhatsappLog::logInteraction(
                $phone,
                $message,
                'error',
                $e->getMessage(),
                false
            );

            return $this->errorResponse(
                'Maaf, terjadi kesalahan. Silakan coba lagi atau hubungi admin.'
            );
        }
    }

    /**
     * Health check endpoint
     */
    public function health(): JsonResponse
    {
        return response()->json([
            'status' => 'ok',
            'service' => 'WhatsApp Bot API',
            'timestamp' => now()->toIso8601String(),
            'maintenance_mode' => $this->isMaintenanceMode(),
        ]);
    }

    /**
     * Check if maintenance mode is enabled
     */
    protected function isMaintenanceMode(): bool
    {
        return ModuleSetting::where('key', 'whatsapp_maintenance_mode')
            ->where('value', '1')
            ->exists();
    }

    /**
     * Standard success response
     */
    protected function successResponse(string $reply, ?string $stateUpdate = null): JsonResponse
    {
        return response()->json([
            'success' => true,
            'reply' => $reply,
            'state_update' => $stateUpdate,
        ]);
    }

    /**
     * Standard error response
     */
    protected function errorResponse(string $message): JsonResponse
    {
        return response()->json([
            'success' => false,
            'reply' => $message,
            'state_update' => null,
        ]);
    }

    /**
     * Send reply directly to WAHA API
     * 
     * @param string $phone Phone number (without @c.us suffix)
     * @param string $message Message to send
     * @return bool Whether the message was sent successfully
     */
    protected function sendToWaha(string $phone, string $message): bool
    {
        try {
            $wahaUrl = config('services.waha.url', env('WAHA_API_URL'));
            $wahaApiKey = config('services.waha.api_key', env('WAHA_API_KEY'));
            $session = config('services.waha.session', env('WAHA_SESSION', 'default'));

            if (empty($wahaUrl)) {
                \Log::warning('WAHA API URL not configured, skipping direct reply');
                return false;
            }

            // Prepare the phone number with @c.us suffix
            $chatId = $phone . '@c.us';

            // WAHA API endpoint for sending text messages
            $endpoint = rtrim($wahaUrl, '/') . '/api/sendText';

            $payload = [
                'session' => $session,
                'chatId' => $chatId,
                'text' => $message,
            ];

            $headers = [
                'Content-Type' => 'application/json',
            ];

            // Add API key if configured
            if (!empty($wahaApiKey)) {
                $headers['X-Api-Key'] = $wahaApiKey;
            }

            \Log::info('Sending reply to WAHA', [
                'endpoint' => $endpoint,
                'chatId' => $chatId,
                'message_length' => strlen($message),
            ]);

            $response = \Illuminate\Support\Facades\Http::withHeaders($headers)
                ->timeout(10)
                ->post($endpoint, $payload);

            if ($response->successful()) {
                \Log::info('WAHA reply sent successfully', [
                    'phone' => $phone,
                    'response' => $response->json(),
                ]);
                return true;
            } else {
                \Log::error('WAHA reply failed', [
                    'phone' => $phone,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return false;
            }
        } catch (\Exception $e) {
            \Log::error('Exception sending WAHA reply', [
                'phone' => $phone,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }
}
