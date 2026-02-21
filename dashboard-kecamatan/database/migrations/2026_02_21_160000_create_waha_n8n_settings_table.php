<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('waha_n8n_settings', function (Blueprint $table) {
            $table->id();

            // WAHA Configuration
            $table->string('waha_api_url')->nullable()->comment('WAHA API URL (e.g., http://localhost:3000)');
            $table->string('waha_api_key')->nullable()->comment('WAHA API Key for authentication');
            $table->string('waha_session_name')->default('default')->comment('WAHA session name');
            $table->string('waha_webhook_url')->nullable()->comment('Webhook URL for WAHA events');

            // n8n Configuration
            $table->string('n8n_api_url')->nullable()->comment('n8n API URL (e.g., http://localhost:5678)');
            $table->string('n8n_api_key')->nullable()->comment('n8n API Key for authentication');
            $table->string('n8n_webhook_url')->nullable()->comment('n8n webhook URL for WhatsApp bot');

            // Status & Monitoring
            $table->boolean('is_waha_connected')->default(false)->comment('WAHA connection status');
            $table->boolean('is_n8n_connected')->default(false)->comment('n8n connection status');
            $table->timestamp('last_connection_check')->nullable()->comment('Last time connection was checked');
            $table->json('connection_details')->nullable()->comment('Detailed connection info');

            // Bot Configuration
            $table->boolean('bot_enabled')->default(true)->comment('Enable/disable WhatsApp bot');
            $table->string('bot_number')->nullable()->comment('WhatsApp bot number');
            $table->string('bot_status')->default('disconnected')->comment('Bot status: connected, disconnected, qr_required');
            $table->text('qr_code')->nullable()->comment('QR code for WhatsApp connection');

            $table->timestamps();
        });

        // Insert default settings
        DB::table('waha_n8n_settings')->insert([
            'waha_api_url' => env('WAHA_API_URL', 'http://localhost:3000'),
            'waha_api_key' => env('WAHA_API_KEY', ''),
            'waha_session_name' => 'default',
            'n8n_api_url' => env('N8N_API_URL', 'http://localhost:5678'),
            'n8n_api_key' => env('N8N_API_KEY', ''),
            'bot_enabled' => true,
            'bot_status' => 'disconnected',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('waha_n8n_settings');
    }
};
