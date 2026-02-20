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
        Schema::table('public_services', function (Blueprint $table) {
            // Completion tracking
            $table->enum('completion_type', ['digital', 'physical'])->nullable()->after('status');
            $table->string('result_file_path')->nullable()->after('completion_type'); // PDF hasil
            $table->timestamp('ready_at')->nullable()->after('result_file_path'); // Kapan siap diambil
            $table->string('pickup_person')->nullable()->after('ready_at'); // Nama petugas
            $table->text('pickup_notes')->nullable()->after('pickup_person'); // Catatan pengambilan
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('public_services', function (Blueprint $table) {
            $table->dropColumn([
                'completion_type',
                'result_file_path',
                'ready_at',
                'pickup_person',
                'pickup_notes'
            ]);
        });
    }
};
