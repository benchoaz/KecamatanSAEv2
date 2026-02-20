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
        Schema::table('pelayanan_faqs', function (Blueprint $table) {
            $table->string('module')->default('pelayanan')->after('category');
            $table->integer('priority')->default(0)->after('module');
            $table->unsignedBigInteger('last_updated_by')->nullable()->after('priority');

            $table->foreign('last_updated_by')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pelayanan_faqs', function (Blueprint $table) {
            $table->dropForeign(['last_updated_by']);
            $table->dropColumn(['module', 'priority', 'last_updated_by']);
        });
    }
};