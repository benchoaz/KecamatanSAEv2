<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    /*
    |--------------------------------------------------------------------------
    | WAHA WhatsApp API Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for WAHA (WhatsApp HTTP API) for sending messages directly
    | from the Dashboard without going through n8n.
    |
    */
    'waha' => [
        'url' => env('WAHA_API_URL'),
        'api_key' => env('WAHA_API_KEY'),
        'session' => env('WAHA_SESSION', 'default'),
    ],

    /*
    |--------------------------------------------------------------------------
    | n8n Webhook Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for n8n webhooks for WhatsApp integration.
    |
    */
    'n8n' => [
        'reply_webhook_url' => env('N8N_REPLY_WEBHOOK_URL'),
    ],

];
