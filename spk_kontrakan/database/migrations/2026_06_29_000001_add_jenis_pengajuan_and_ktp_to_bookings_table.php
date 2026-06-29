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
        Schema::table('bookings', function (Blueprint $table) {
            // Jenis pengajuan: survei atau sewa
            if (!Schema::hasColumn('bookings', 'jenis_pengajuan')) {
                $table->string('jenis_pengajuan')->default('sewa')->after('notes');
            }
            // Tanggal dan jam survei (hanya untuk jenis_pengajuan = survei)
            if (!Schema::hasColumn('bookings', 'tanggal_survei')) {
                $table->date('tanggal_survei')->nullable()->after('jenis_pengajuan');
            }
            if (!Schema::hasColumn('bookings', 'jam_survei')) {
                $table->string('jam_survei')->nullable()->after('tanggal_survei');
            }
            // Foto KTP (untuk pengajuan sewa)
            if (!Schema::hasColumn('bookings', 'ktp_photo')) {
                $table->string('ktp_photo')->nullable()->after('jam_survei');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['jenis_pengajuan', 'tanggal_survei', 'jam_survei', 'ktp_photo']);
        });
    }
};
