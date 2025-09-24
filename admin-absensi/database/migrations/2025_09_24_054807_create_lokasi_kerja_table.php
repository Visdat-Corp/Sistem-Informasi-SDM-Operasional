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
        Schema::create('lokasi_kerja', function (Blueprint $table) {
            $table->bigIncrements('id_lokasi'); // Primary Key
            $table->string('lokasi_kerja');     // Nama lokasi kerja
            $table->timestamps();              // created_at & updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lokasi_kerja');
    }
};
