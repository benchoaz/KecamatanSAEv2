<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('berita', function (Blueprint $table) {
            $table->foreignId('desa_id')->nullable()->constrained('desa')->onDelete('cascade');
            $table->enum('scope', ['kecamatan', 'desa'])->default('kecamatan');
            $table->enum('source_type', ['internal', 'scraped'])->default('internal');
            $table->string('external_url', 500)->nullable();
            $table->string('external_source', 100)->nullable();
            $table->string('external_id', 100)->nullable()->unique(); // To prevent duplicate scraping
            $table->string('clickbait_headline', 200)->nullable();
            $table->tinyInteger('priority_level')->default(3); // 1 = Breaking, 5 = Normal
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('berita', function (Blueprint $table) {
            $table->dropForeign(['desa_id']);
            $table->dropColumn([
                'desa_id',
                'scope',
                'source_type',
                'external_url',
                'external_source',
                'external_id',
                'clickbait_headline',
                'priority_level'
            ]);
        });
    }
};
