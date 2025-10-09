<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posisi', function (Blueprint $table) {
            $table->id('id_posisi');
            $table->string('nama_posisi');
            $table->foreignId('id_departemen')->constrained('departemen', 'id_departemen')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posisi');
    }
};
