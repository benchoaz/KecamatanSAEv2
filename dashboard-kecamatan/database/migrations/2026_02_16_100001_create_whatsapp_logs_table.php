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
        Schema::create('whatsapp_logs', function (Blueprint $table) {
            $table->id();
            $table->string('phone')->comment('User phone number');
            $table->string('intent')->nullable()->comment('Detected intent (status, umkm, jasa, loker, pengaduan, toggle, menu)');
            $table->text('message')->comment('Incoming message from user');
            $table->text('response')->nullable()->comment('Bot response sent back');
            $table->boolean('success')->default(true)->comment('Whether request was handled successfully');
            $table->timestamps();

            $table->index('phone');
            $table->index('intent');
            $table->index('success');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_logs');
    }
};
