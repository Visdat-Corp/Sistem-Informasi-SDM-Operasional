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
        Schema::create('absensi', function (Blueprint $table) {
            $table->bigIncrements('id_absensi'); // Primary Key

            // Relasi ke karyawan
            $table->unsignedBigInteger('id_karyawan');
            $table->date('tanggal_absen');
            $table->time('jam_masuk')->nullable();
            $table->time('jam_keluar')->nullable();

            $table->string('lokasi_absen_masuk')->nullable();
            $table->string('lokasi_absen_keluar')->nullable();

            $table->string('foto_masuk')->nullable(); // bisa simpan path foto
            $table->string('foto_keluar')->nullable();

            $table->enum('mode', ['WFO', 'WFH', 'Lainnya'])->default('WFO');
            $table->text('keterangan')->nullable();

            // Relasi ke jam kerja
            $table->unsignedBigInteger('id_jamKerja')->nullable();

            $table->timestamps();

            // Foreign Key Constraints
            $table->foreign('id_karyawan')
                  ->references('id_karyawan')
                  ->on('karyawan')
                  ->onDelete('cascade');

            $table->foreign('id_jamKerja')
                  ->references('id_jamKerja')
                  ->on('jam_kerja')
                  ->onDelete('set null'); // kalau jam kerja dihapus, absensi tetap ada tapi null
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensi');
    }
};
