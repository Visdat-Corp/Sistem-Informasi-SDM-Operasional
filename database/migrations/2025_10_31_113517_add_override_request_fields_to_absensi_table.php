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
        Schema::table('absensi', function (Blueprint $table) {
            $table->boolean('override_request')->default(false)->after('is_lembur');
            $table->text('override_reason')->nullable()->after('override_request');
            $table->enum('override_status', ['pending', 'approved', 'rejected'])->default('pending')->after('override_reason');
            $table->timestamp('override_requested_at')->nullable()->after('override_status');
            $table->timestamp('override_responded_at')->nullable()->after('override_requested_at');
            $table->foreignId('override_responded_by')->nullable()->after('override_responded_at')->constrained('karyawan', 'id_karyawan')->onDelete('set null');
            $table->text('override_response_note')->nullable()->after('override_responded_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('absensi', function (Blueprint $table) {
            $table->dropForeign(['override_responded_by']);
            $table->dropColumn([
                'override_request',
                'override_reason',
                'override_status',
                'override_requested_at',
                'override_responded_at',
                'override_responded_by',
                'override_response_note'
            ]);
        });
    }
};
