<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jam_kerja', function (Blueprint $table) {
            $table->id('id_jamKerja');
            $table->time('jam_masuk_normal');
            $table->time('jam_keluar_normal');
            $table->integer('toleransi_keterlambatan')->default(0);
            $table->time('jam_lembur')->nullable();
            $table->integer('total_jam')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jam_kerja');
    }
};
