<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('umkm_locals', function (Blueprint $table) {
            $table->decimal('price', 15, 2)->nullable()->after('product');
            $table->decimal('original_price', 15, 2)->nullable()->after('price');
            $table->text('description')->nullable()->after('original_price');
            $table->boolean('is_featured')->default(false)->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('umkm_locals', function (Blueprint $table) {
            $table->dropColumn(['price', 'original_price', 'description', 'is_featured']);
        });
    }
};
