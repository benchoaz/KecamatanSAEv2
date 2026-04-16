<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Add multi-provider WhatsApp fields to waha_n8n_settings.
     * WAHA columns already exist — we only ADD new columns here.
     */
    public function up(): void
    {
        Schema::table('waha_n8n_settings', function (Blueprint $table) {
            // ── Active provider selector ─────────────────────────────────────
            $table->string('active_provider')
                ->default('waha')
                ->after('n8n_webhook_url')
                ->comment('Active WA provider: waha | fonnte | ultramsg | generic_http');

            // ── Fonnte ───────────────────────────────────────────────────────
            $table->string('fonnte_token')->nullable()
                ->after('active_provider')
                ->comment('Fonnte API token');

            $table->string('fonnte_device')->nullable()
                ->after('fonnte_token')
                ->comment('Fonnte device ID (optional)');

            // ── UltraMsg ─────────────────────────────────────────────────────
            $table->string('ultramsg_instance_id')->nullable()
                ->after('fonnte_device')
                ->comment('UltraMsg instance ID');

            $table->string('ultramsg_token')->nullable()
                ->after('ultramsg_instance_id')
                ->comment('UltraMsg API token');

            // ── Generic HTTP ─────────────────────────────────────────────────
            $table->string('generic_http_url')->nullable()
                ->after('ultramsg_token')
                ->comment('Generic provider: full POST endpoint URL');

            $table->json('generic_http_headers')->nullable()
                ->after('generic_http_url')
                ->comment('Generic provider: auth/custom headers as JSON object');

            $table->string('generic_http_phone_field')->nullable()->default('target')
                ->after('generic_http_headers')
                ->comment('Generic provider: request body field name for phone number');

            $table->string('generic_http_message_field')->nullable()->default('message')
                ->after('generic_http_phone_field')
                ->comment('Generic provider: request body field name for message text');

            $table->json('generic_http_extra_body')->nullable()
                ->after('generic_http_message_field')
                ->comment('Generic provider: additional body fields as JSON object');
        });
    }

    public function down(): void
    {
        Schema::table('waha_n8n_settings', function (Blueprint $table) {
            $table->dropColumn([
                'active_provider',
                'fonnte_token',
                'fonnte_device',
                'ultramsg_instance_id',
                'ultramsg_token',
                'generic_http_url',
                'generic_http_headers',
                'generic_http_phone_field',
                'generic_http_message_field',
                'generic_http_extra_body',
            ]);
        });
    }
};
