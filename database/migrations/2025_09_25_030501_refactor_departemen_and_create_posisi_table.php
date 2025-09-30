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
        // Drop nama_posisi column from departemen table
        Schema::table('departemen', function (Blueprint $table) {
            $table->dropColumn('nama_posisi');
        });

        // Create posisi table
        Schema::create('posisi', function (Blueprint $table) {
            $table->bigIncrements('id_posisi');
            $table->string('nama_posisi');
            $table->unsignedBigInteger('id_departemen');
            $table->timestamps();

            $table->foreign('id_departemen')->references('id_departemen')->on('departemen')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop posisi table
        Schema::dropIfExists('posisi');

        // Add back nama_posisi column to departemen table
        Schema::table('departemen', function (Blueprint $table) {
            $table->string('nama_posisi')->after('nama_departemen');
        });
    }
};
