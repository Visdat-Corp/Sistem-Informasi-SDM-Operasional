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
        Schema::create('karyawan', function (Blueprint $table) {
            $table->bigIncrements('id_karyawan'); // Primary Key
            $table->unsignedBigInteger('id_departemen'); // Foreign Key
            $table->string('nama_karyawan');
            $table->string('username_karyawan')->unique();
            $table->string('password_karyawan');
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->timestamps();

            // Relasi ke tabel departemen
            $table->foreign('id_departemen')
                  ->references('id_departemen')
                  ->on('departemen')
                  ->onDelete('cascade'); // kalau departemen dihapus, karyawan juga ikut terhapus
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('karyawan');
    }
};
