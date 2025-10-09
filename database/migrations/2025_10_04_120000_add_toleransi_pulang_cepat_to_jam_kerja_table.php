<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jam_kerja', function (Blueprint $table) {
            $table->integer('toleransi_pulang_cepat')->default(0)->after('toleransi_keterlambatan');
        });
    }

    public function down(): void
    {
        Schema::table('jam_kerja', function (Blueprint $table) {
            $table->dropColumn('toleransi_pulang_cepat');
        });
    }
};
