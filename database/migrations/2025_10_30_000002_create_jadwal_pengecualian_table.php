<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwal_pengecualian', function (Blueprint $table) {
            $table->id('id_jadwal_pengecualian');
            $table->date('tanggal');
            $table->string('nama_hari_libur');
            $table->text('keterangan')->nullable();
            $table->enum('jenis', ['libur_nasional', 'cuti_bersama', 'lainnya'])->default('libur_nasional');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_pengecualian');
    }
};
