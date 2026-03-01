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

        $startTime = microtime(true);

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
            \Log::info('Bot Routing Path', [
                'phone' => $phone,
                'state' => $session->state,
                'is_active' => $session->isActive(),
                'message' => $message
            ]);

            if ($session->isActive()) {
                $response = $this->stateHandler->handle($session, $message);
            } else {
                $response = $this->intentHandler->handle($phone, $message);
            }

            $duration = round((microtime(true) - $startTime) * 1000, 2);

            $replyPreview = 'NULL';
            if (isset($response['reply'])) {
                $replyText = is_array($response['reply']) ? json_encode($response['reply']) : $response['reply'];
                $replyPreview = is_string($replyText) ? substr($replyText, 0, 50) . '...' : 'Not a string';
            }

            \Log::info('Bot Handler Response', [
                'intent' => $response['intent'] ?? 'N/A',
                'state_update' => $response['state_update'] ?? 'N/A',
                'duration_ms' => $duration . 'ms',
                'reply_preview' => $replyPreview
            ]);

            // =====================================================
            // CRITICAL FIX: Save state_update to database
            // The handlers return state_update but it wasn't being saved!
            // =====================================================
            if (isset($response['state_update'])) {
                if ($response['state_update'] === null) {
                    // Clear session if state_update is null
                    $session->clear();
                    \Log::info('Session cleared as per state_update');
                } else {
                    // Update session state
                    $session->updateState($response['state_update']);
                    \Log::info('Session state updated to: ' . $response['state_update']);
                }
            }

            // =====================================================
            // FAILSAFE MODE: Ensure reply is never empty
            // =====================================================
            if (empty($response['reply'])) {
                \Log::warning('Handler returned empty reply, using fallback', [
                    'phone' => $phone,
                    'intent' => $response['intent'] ?? 'unknown',
                ]);
                $response['reply'] = '🙏 Sistem sedang memproses. Silakan coba kembali beberapa saat lagi.';
            }

            // Log interaction - convert array reply to string
            $replyForLog = '';
            if (isset($response['reply'])) {
                $replyForLog = is_array($response['reply']) ? json_encode($response['reply']) : (string) $response['reply'];
            }

            WhatsappLog::logInteraction(
                $phone,
                $message,
                $response['intent'] ?? null,
                $replyForLog,
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
