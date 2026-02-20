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
        Schema::create('berita', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->string('judul');
            $blueprint->string('slug')->unique();
            $blueprint->text('ringkasan')->nullable();
            $blueprint->longText('konten');
            $blueprint->string('kategori')->index();
            $blueprint->string('thumbnail')->nullable();

            // Status publikasi (draft/published)
            $blueprint->enum('status', ['draft', 'published'])->default('draft')->index();

            // Statistik
            $blueprint->unsignedBigInteger('view_count')->default(0);

            // Relasi & Audit
            $blueprint->foreignId('author_id')
                ->constrained('users')
                ->onDelete('cascade');

            // Timestamps
            $blueprint->timestamp('published_at')->nullable();
            $blueprint->timestamps();
            $blueprint->softDeletes(); // Audit-friendly: bisa direstore jika salah hapus

            // Indexing for performance
            $blueprint->index(['status', 'published_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('berita');
    }
};
