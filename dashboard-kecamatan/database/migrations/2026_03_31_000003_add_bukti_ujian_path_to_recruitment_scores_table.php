<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Guard: only run if the recruitment system table exists
        // This migration belongs to the perangkat desa recruitment module
        // and must not interfere with core kecamatan workflow
        if (!Schema::hasTable('recruitment_scores')) {
            return;
        }

        Schema::table('recruitment_scores', function (Blueprint $table) {
            if (!Schema::hasColumn('recruitment_scores', 'bukti_ujian_path')) {
                $table->string('bukti_ujian_path')->nullable()->after('catatan_penilai');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('recruitment_scores', function (Blueprint $table) {
            $table->dropColumn('bukti_ujian_path');
        });
    }
};
