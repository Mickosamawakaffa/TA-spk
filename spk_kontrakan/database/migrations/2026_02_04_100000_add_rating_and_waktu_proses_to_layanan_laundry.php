<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('layanan_laundry', function (Blueprint $table) {
            // Tambah field rating dan waktu_proses untuk konsistensi dengan kriteria laundry
            if (!Schema::hasColumn('layanan_laundry', 'rating')) {
                $table->float('rating')->nullable()->default(0)->after('status');
            }
            if (!Schema::hasColumn('layanan_laundry', 'waktu_proses')) {
                $table->integer('waktu_proses')->nullable()->comment('Waktu proses dalam jam')->after('rating');
            }
        });
    }

    public function down(): void
    {
        Schema::table('layanan_laundry', function (Blueprint $table) {
            if (Schema::hasColumn('layanan_laundry', 'rating')) {
                $table->dropColumn('rating');
            }
            if (Schema::hasColumn('layanan_laundry', 'waktu_proses')) {
                $table->dropColumn('waktu_proses');
            }
        });
    }
};
