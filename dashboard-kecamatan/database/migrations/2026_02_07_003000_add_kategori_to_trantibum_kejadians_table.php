<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('trantibum_kejadians', function (Blueprint $table) {
            $table->string('kategori')->default('Bencana Alam')->after('desa_id')
                ->comment('Bencana Alam, Kriminalitas, Ketertiban Umum, Lainnya');
        });
    }

    public function down(): void
    {
        Schema::table('trantibum_kejadians', function (Blueprint $table) {
            $table->dropColumn('kategori');
        });
    }
};
