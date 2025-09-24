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
        Schema::create('jam_kerja', function (Blueprint $table) {
            $table->bigIncrements('id_jamKerja'); // Primary Key

            $table->time('jam_masuk_normal');       // Jam masuk
            $table->time('jam_keluar_normal');      // Jam keluar
            $table->integer('toleransi_keterlambatan')->default(0); // dalam menit
            $table->time('jam_lembur')->nullable(); // bisa kosong kalau tidak ada lembur
            $table->integer('total_jam')->nullable(); // total jam kerja (opsional)

            $table->timestamps(); // created_at & updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jam_kerja');
    }
};
