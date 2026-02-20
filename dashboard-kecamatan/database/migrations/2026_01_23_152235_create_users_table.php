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
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // Using id as internal primary key
            $table->string('nama_lengkap', 150);
            $table->string('username')->unique()->index();
            $table->string('password');

            // Access Controls
            $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');
            $table->foreignId('desa_id')->nullable()->constrained('desa')->onDelete('set null');

            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->timestamp('last_login')->nullable();
            $table->string('email')->nullable()->unique(); // Optional, usage of username is primary

            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
