<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WahaN8nSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if settings already exist
        $exists = DB::table('waha_n8n_settings')->exists();

        if (!$exists) {
            DB::table('waha_n8n_settings')->insert([
                'waha_api_url' => env('WAHA_API_URL', 'http://waha-kecamatan:3000'),
                'waha_api_key' => env('WAHA_API_KEY', ''),
                'waha_session_name' => env('WAHA_SESSION', 'default'),
                'waha_webhook_url' => null,
                'n8n_api_url' => env('N8N_API_URL', 'http://n8n-kecamatan:5678'),
                'n8n_api_key' => env('N8N_API_KEY', ''),
                'n8n_webhook_url' => env('N8N_REPLY_WEBHOOK_URL', 'http://n8n-kecamatan:5678/webhook/dashboard-reply'),
                'is_waha_connected' => false,
                'is_n8n_connected' => false,
                'last_connection_check' => null,
                'connection_details' => null,
                'bot_enabled' => true,
                'bot_number' => '6281331699112',
                'bot_status' => 'disconnected',
                'qr_code' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $this->command->info('WAHA & n8n settings seeded successfully.');
        } else {
            $this->command->info('WAHA & n8n settings already exist, skipping.');
        }
    }
}
