<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('karyawan', function (Blueprint $table) {
            $table->foreignId('id_lokasi_kerja')->nullable()->after('id_posisi')
                ->constrained('lokasi_kerja', 'id_lokasi')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('karyawan', function (Blueprint $table) {
            $table->dropForeign(['id_lokasi_kerja']);
            $table->dropColumn('id_lokasi_kerja');
        });
    }
};
