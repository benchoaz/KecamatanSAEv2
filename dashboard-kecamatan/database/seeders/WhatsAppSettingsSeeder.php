<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ModuleSetting;

class WhatsAppSettingsSeeder extends Seeder
{
    /**
     * Seed WhatsApp module settings
     */
    public function run(): void
    {
        $settings = [
            [
                'module' => 'whatsapp',
                'key' => 'whatsapp_maintenance_mode',
                'value' => '0', // 0 = active, 1 = maintenance
                'description' => 'WhatsApp bot maintenance mode (1 = enabled, 0 = disabled)',
                'type' => 'boolean',
            ],
            [
                'module' => 'whatsapp',
                'key' => 'whatsapp_welcome_message',
                'value' => 'Selamat datang di layanan WhatsApp Kecamatan Besuk. Ketik MENU untuk melihat layanan yang tersedia.',
                'description' => 'Welcome message for new users',
                'type' => 'text',
            ],
            [
                'module' => 'whatsapp',
                'key' => 'whatsapp_maintenance_message',
                'value' => 'Sistem sedang dalam pemeliharaan. Silakan coba beberapa saat lagi.',
                'description' => 'Message shown during maintenance mode',
                'type' => 'text',
            ],
            [
                'module' => 'whatsapp',
                'key' => 'whatsapp_session_timeout',
                'value' => '30', // minutes
                'description' => 'Session timeout in minutes',
                'type' => 'number',
            ],
            [
                'module' => 'whatsapp',
                'key' => 'whatsapp_bot_number',
                'value' => '6281331699112',
                'description' => 'Official WhatsApp bot number for Kecamatan Besuk',
                'type' => 'text',
            ],
            [
                'module' => 'whatsapp',
                'key' => 'whatsapp_max_message_length',
                'value' => '4096',
                'description' => 'Maximum message length for WhatsApp responses',
                'type' => 'number',
            ],
            [
                'module' => 'whatsapp',
                'key' => 'whatsapp_auto_reply_delay',
                'value' => '1',
                'description' => 'Delay in seconds for auto-reply messages',
                'type' => 'number',
            ]
        ];

        foreach ($settings as $setting) {
            ModuleSetting::updateOrCreate(
                ['module' => $setting['module'], 'key' => $setting['key']],
                $setting
            );
        }

        $this->command->info('WhatsApp settings seeded successfully!');
    }
}
