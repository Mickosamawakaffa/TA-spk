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
        // Tambah kolom koordinat ke tabel laundry
        Schema::table('laundry', function (Blueprint $table) {
            $table->decimal('latitude', 10, 8)->nullable()->after('alamat');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
        });

        // Tambah kolom koordinat ke tabel kontrakans
        Schema::table('kontrakans', function (Blueprint $table) {
            $table->decimal('latitude', 10, 8)->nullable()->after('alamat');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Hapus kolom jika rollback
        Schema::table('laundry', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude']);
        });

        Schema::table('kontrakans', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude']);
        });
    }
};