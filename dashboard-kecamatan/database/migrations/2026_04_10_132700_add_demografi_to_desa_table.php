<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('desa', function (Blueprint $table) {
            $table->unsignedInteger('jumlah_penduduk')->default(0)->after('status');
            $table->unsignedInteger('jumlah_laki_laki')->default(0)->after('jumlah_penduduk');
            $table->unsignedInteger('jumlah_perempuan')->default(0)->after('jumlah_laki_laki');
            $table->unsignedInteger('jumlah_kk')->default(0)->after('jumlah_perempuan');
            $table->decimal('luas_wilayah', 8, 2)->default(0)->after('jumlah_kk')->comment('Luas dalam hektar');
            $table->unsignedInteger('jumlah_rt')->default(0)->after('luas_wilayah');
            $table->unsignedInteger('jumlah_rw')->default(0)->after('jumlah_rt');
        });
    }

    public function down(): void
    {
        Schema::table('desa', function (Blueprint $table) {
            $table->dropColumn([
                'jumlah_penduduk', 'jumlah_laki_laki', 'jumlah_perempuan',
                'jumlah_kk', 'luas_wilayah', 'jumlah_rt', 'jumlah_rw'
            ]);
        });
    }
};
