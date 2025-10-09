<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('absensi', function (Blueprint $table) {
            $table->id('id_absensi');
            $table->foreignId('id_karyawan')->constrained('karyawan', 'id_karyawan')->onDelete('cascade');
            $table->date('tanggal_absen');
            $table->time('jam_masuk')->nullable();
            $table->time('jam_keluar')->nullable();
            $table->string('lokasi_absen_masuk')->nullable();
            $table->string('lokasi_absen_keluar')->nullable();
            $table->string('foto_masuk')->nullable();
            $table->string('foto_keluar')->nullable();
            $table->text('keterangan')->nullable();
            $table->enum('status', ['hadir', 'terlambat', 'lembur'])->nullable();
            $table->integer('menit_keterlambatan')->nullable();
            $table->boolean('is_lembur')->default(false);
            $table->foreignId('id_jamKerja')->nullable()->constrained('jam_kerja', 'id_jamKerja')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absensi');
    }
};
