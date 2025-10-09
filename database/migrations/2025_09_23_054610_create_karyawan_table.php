<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('karyawan', function (Blueprint $table) {
            $table->id('id_karyawan');
            $table->foreignId('id_departemen')->constrained('departemen', 'id_departemen')->onDelete('cascade');
            $table->foreignId('id_posisi')->nullable()->constrained('posisi', 'id_posisi')->onDelete('set null');
            $table->string('nama_karyawan');
            $table->string('username_karyawan')->unique();
            $table->string('email_karyawan')->unique();
            $table->string('password_karyawan');
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('karyawan');
    }
};
