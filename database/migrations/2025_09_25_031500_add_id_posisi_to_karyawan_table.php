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
        Schema::table('karyawan', function (Blueprint $table) {
            $table->unsignedBigInteger('id_posisi')->nullable()->after('id_departemen');
            $table->foreign('id_posisi')
                  ->references('id_posisi')
                  ->on('posisi')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('karyawan', function (Blueprint $table) {
            $table->dropForeign(['id_posisi']);
            $table->dropColumn('id_posisi');
        });
    }
};
