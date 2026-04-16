<?php

namespace App\Services\WhatsApp;

use App\Models\WahaN8nSetting;

/**
 * Generates n8n workflow JSON dynamically based on the active WhatsApp provider.
 * The exported JSON can be imported directly into n8n.
 */
class N8nWorkflowGenerator
{
    protected WahaN8nSetting $settings;
    protected string $dashboardUrl;

    public function __construct(WahaN8nSetting $settings)
    {
        $this->settings     = $settings;
        $this->dashboardUrl = rtrim(config('app.url', 'http://localhost'), '/');
    }

    /**
     * Generate the complete n8n workflow array.
     */
    public function generate(): array
    {
        $provider = $this->settings->active_provider ?? 'waha';
        $sendNode = $this->buildSendNode($provider);

        return [
            'name'        => '🤖 Kecamatan SAE WhatsApp Bot (' . strtoupper($provider) . ')',
            'nodes'       => $this->buildNodes($sendNode),
            'connections' => $this->buildConnections(),
            'settings'    => ['saveManualExecutions' => true],
            'tags'        => [['name' => 'whatsapp'], ['name' => 'kecamatan']],
        ];
    }

    /**
     * Generate and return as a JSON string.
     */
    public function toJson(): string
    {
        return json_encode($this->generate(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Private: build the send-message node based on provider
    // ──────────────────────────────────────────────────────────────────────────

    private function buildSendNode(string $provider): array
    {
        return match ($provider) {
            'fonnte'       => $this->fonnteNode(),
            'ultramsg'     => $this->ultraMsgNode(),
            'generic_http' => $this->genericHttpNode(),
            default        => $this->wahaNode(),
        };
    }

    // ── WAHA ──────────────────────────────────────────────────────────────────
    private function wahaNode(): array
    {
        $wahaUrl = rtrim($this->settings->waha_api_url ?? 'http://waha:3000', '/');
        $apiKey  = $this->settings->waha_api_key ?? '';
        $session = $this->settings->waha_session_name ?? 'default';

        return [
            'parameters' => [
                'method'           => 'POST',
                'url'              => "{$wahaUrl}/api/sendText",
                'sendBody'         => true,
                'sendHeaders'      => (bool) $apiKey,
                'headerParameters' => $apiKey ? [
                    'parameters' => [['name' => 'X-Api-Key', 'value' => $apiKey]],
                ] : [],
                'bodyParameters' => [
                    'parameters' => [
                        ['name' => 'chatId',  'value' => '={{ $node["PrepareData"].json.chatId }}'],
                        ['name' => 'text',    'value' => '={{ $node["PrepareData"].json.replyText }}'],
                        ['name' => 'session', 'value' => $session],
                    ],
                ],
            ],
            'id'          => 'send-message',
            'name'        => 'SendMessage',
            'type'        => 'n8n-nodes-base.httpRequest',
            'typeVersion' => 4.1,
            'position'    => [1400, 300],
        ];
    }

    // ── Fonnte ────────────────────────────────────────────────────────────────
    private function fonnteNode(): array
    {
        $token = $this->settings->fonnte_token ?? '';

        return [
            'parameters' => [
                'method'           => 'POST',
                'url'              => 'https://api.fonnte.com/send',
                'sendBody'         => true,
                'sendHeaders'      => true,
                'headerParameters' => [
                    'parameters' => [['name' => 'Authorization', 'value' => $token]],
                ],
                'bodyParameters' => [
                    'parameters' => [
                        ['name' => 'target',      'value' => '={{ $node["PrepareData"].json.chatId }}'],
                        ['name' => 'message',     'value' => '={{ $node["PrepareData"].json.replyText }}'],
                        ['name' => 'countryCode', 'value' => '62'],
                    ],
                ],
            ],
            'id'          => 'send-message',
            'name'        => 'SendMessage',
            'type'        => 'n8n-nodes-base.httpRequest',
            'typeVersion' => 4.1,
            'position'    => [1400, 300],
        ];
    }

    // ── UltraMsg ──────────────────────────────────────────────────────────────
    private function ultraMsgNode(): array
    {
        $instanceId = $this->settings->ultramsg_instance_id ?? 'YOUR_INSTANCE';
        $token      = $this->settings->ultramsg_token ?? '';

        return [
            'parameters' => [
                'method'      => 'POST',
                'url'         => "https://api.ultramsg.com/{$instanceId}/messages/chat",
                'sendBody'    => true,
                'bodyParameters' => [
                    'parameters' => [
                        ['name' => 'token',   'value' => $token],
                        ['name' => 'to',      'value' => '={{ $node["PrepareData"].json.chatId }}'],
                        ['name' => 'body',    'value' => '={{ $node["PrepareData"].json.replyText }}'],
                        ['name' => 'msgtype', 'value' => 'text'],
                    ],
                ],
            ],
            'id'          => 'send-message',
            'name'        => 'SendMessage',
            'type'        => 'n8n-nodes-base.httpRequest',
            'typeVersion' => 4.1,
            'position'    => [1400, 300],
        ];
    }

    // ── Generic HTTP ──────────────────────────────────────────────────────────
    private function genericHttpNode(): array
    {
        $url          = $this->settings->generic_http_url ?? '';
        $phoneField   = $this->settings->generic_http_phone_field ?? 'target';
        $msgField     = $this->settings->generic_http_message_field ?? 'message';
        $headers      = $this->settings->generic_http_headers ?? [];
        $extra        = $this->settings->generic_http_extra_body ?? [];

        $headerParams = [];
        foreach ($headers as $key => $value) {
            $headerParams[] = ['name' => $key, 'value' => $value];
        }

        $bodyParams = [
            ['name' => $phoneField, 'value' => '={{ $node["PrepareData"].json.chatId }}'],
            ['name' => $msgField,   'value' => '={{ $node["PrepareData"].json.replyText }}'],
        ];
        foreach ($extra as $key => $value) {
            $bodyParams[] = ['name' => $key, 'value' => $value];
        }

        return [
            'parameters' => [
                'method'           => 'POST',
                'url'              => $url,
                'sendBody'         => true,
                'sendHeaders'      => ! empty($headerParams),
                'headerParameters' => ! empty($headerParams) ? ['parameters' => $headerParams] : [],
                'bodyParameters'   => ['parameters' => $bodyParams],
            ],
            'id'          => 'send-message',
            'name'        => 'SendMessage',
            'type'        => 'n8n-nodes-base.httpRequest',
            'typeVersion' => 4.1,
            'position'    => [1400, 300],
        ];
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Workflow skeleton (same for all providers, only the SendMessage node differs)
    // ──────────────────────────────────────────────────────────────────────────

    private function buildNodes(array $sendNode): array
    {
        $webhookUrl  = $this->settings->n8n_webhook_url ?? 'http://localhost:5678/webhook/whatsapp';
        $dashUrl     = $this->dashboardUrl;

        return [
            // ── 1. Webhook Trigger ─────────────────────────────────────────────
            [
                'parameters'  => [
                    'path'           => 'whatsapp-primary',
                    'responseMode'   => 'responseNode',
                    'responseData'   => '',
                    'options'        => [],
                ],
                'id'          => 'webhook-trigger',
                'name'        => 'WebhookTrigger',
                'type'        => 'n8n-nodes-base.webhook',
                'typeVersion' => 1,
                'position'    => [200, 300],
                'webhookId'   => 'whatsapp-primary',
            ],

            // ── 2. Filter broadcast & non-message events ───────────────────────
            [
                'parameters' => [
                    'conditions' => [
                        'string' => [
                            ['value1' => '={{ $json.body.event }}',              'operation' => 'equals',     'value2' => 'message'],
                            ['value1' => '={{ $json.body.payload.from ?? "" }}', 'operation' => 'notContains','value2' => 'status@broadcast'],
                        ],
                    ],
                    'combineOperation' => 'all',
                ],
                'id'          => 'filter-valid',
                'name'        => 'FilterValid',
                'type'        => 'n8n-nodes-base.if',
                'typeVersion' => 1,
                'position'    => [400, 300],
            ],

            // ── 3. Respond to webhook immediately (avoid timeout) ──────────────
            [
                'parameters'  => [
                    'respondWith'    => 'json',
                    'responseBody'   => '{"ok":true}',
                    'options'        => [],
                ],
                'id'          => 'respond-ok',
                'name'        => 'RespondOK',
                'type'        => 'n8n-nodes-base.respondToWebhook',
                'typeVersion' => 1,
                'position'    => [600, 500],
            ],

            // ── 4. Extract chatId & message text ──────────────────────────────
            [
                'parameters' => [
                    'values' => [
                        'string' => [
                            ['name' => 'chatId', 'value' => '={{ $json.body.payload.from }}'],
                            ['name' => 'msg',    'value' => '={{ ($json.body.payload.body ?? "").trim() }}'],
                        ],
                    ],
                    'options' => [],
                ],
                'id'          => 'prepare-data',
                'name'        => 'PrepareData',
                'type'        => 'n8n-nodes-base.set',
                'typeVersion' => 2,
                'position'    => [600, 300],
            ],

            // ── 5. Route message to Dashboard API ─────────────────────────────
            [
                'parameters' => [
                    'method'      => 'POST',
                    'url'         => "{$dashUrl}/api/whatsapp/handle",
                    'sendBody'    => true,
                    'bodyParameters' => [
                        'parameters' => [
                            ['name' => 'chatId',  'value' => '={{ $node["PrepareData"].json.chatId }}'],
                            ['name' => 'message', 'value' => '={{ $node["PrepareData"].json.msg }}'],
                        ],
                    ],
                ],
                'id'          => 'dashboard-api',
                'name'        => 'DashboardAPI',
                'type'        => 'n8n-nodes-base.httpRequest',
                'typeVersion' => 4.1,
                'position'    => [800, 300],
            ],

            // ── 6. Check if dashboard wants a reply ───────────────────────────
            [
                'parameters' => [
                    'conditions' => [
                        'boolean' => [
                            ['value1' => '={{ $json.should_reply }}', 'value2' => true],
                        ],
                    ],
                ],
                'id'          => 'check-reply',
                'name'        => 'ShouldReply',
                'type'        => 'n8n-nodes-base.if',
                'typeVersion' => 1,
                'position'    => [1000, 300],
            ],

            // ── 7. Prepare reply text ─────────────────────────────────────────
            [
                'parameters' => [
                    'values' => [
                        'string' => [
                            ['name' => 'chatId',    'value' => '={{ $node["PrepareData"].json.chatId }}'],
                            ['name' => 'replyText', 'value' => '={{ $json.reply }}'],
                        ],
                    ],
                    'options' => [],
                ],
                'id'          => 'prepare-reply',
                'name'        => 'PrepareReply',
                'type'        => 'n8n-nodes-base.set',
                'typeVersion' => 2,
                'position'    => [1200, 300],
            ],

            // ── 8. SEND MESSAGE (provider-specific node) ──────────────────────
            $sendNode,
        ];
    }

    private function buildConnections(): array
    {
        return [
            'WebhookTrigger' => ['main' => [[
                ['node' => 'FilterValid', 'type' => 'main', 'index' => 0],
            ]]],

            'FilterValid' => ['main' => [
                // True branch → process
                [['node' => 'PrepareData', 'type' => 'main', 'index' => 0]],
                // False branch → respond immediately
                [['node' => 'RespondOK',   'type' => 'main', 'index' => 0]],
            ]],

            'PrepareData' => ['main' => [[
                ['node' => 'RespondOK',    'type' => 'main', 'index' => 0],
                ['node' => 'DashboardAPI', 'type' => 'main', 'index' => 0],
            ]]],

            'DashboardAPI' => ['main' => [[
                ['node' => 'ShouldReply', 'type' => 'main', 'index' => 0],
            ]]],

            'ShouldReply' => ['main' => [
                // True → build reply
                [['node' => 'PrepareReply', 'type' => 'main', 'index' => 0]],
                // False → stop
            ]],

            'PrepareReply' => ['main' => [[
                ['node' => 'SendMessage', 'type' => 'main', 'index' => 0],
            ]]],
        ];
    }
}
