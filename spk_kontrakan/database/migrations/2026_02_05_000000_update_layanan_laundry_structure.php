<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('layanan_laundry', function (Blueprint $table) {
            // Modify jenis_layanan to varchar
            if (Schema::hasColumn('layanan_laundry', 'jenis_layanan')) {
                try {
                    $table->string('jenis_layanan', 50)->change();
                } catch (\Exception $e) {
                    // Jika error, skip
                }
            }

            // Add missing columns if they don't exist
            if (!Schema::hasColumn('layanan_laundry', 'nama_paket')) {
                $table->string('nama_paket')->nullable()->after('jenis_layanan');
            }
            if (!Schema::hasColumn('layanan_laundry', 'estimasi_selesai')) {
                $table->integer('estimasi_selesai')->nullable()->after('harga');
            }
            if (!Schema::hasColumn('layanan_laundry', 'deskripsi')) {
                $table->text('deskripsi')->nullable()->after('estimasi_selesai');
            }
            if (!Schema::hasColumn('layanan_laundry', 'status')) {
                $table->string('status', 50)->default('aktif')->after('deskripsi');
            }
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
            $columns = [
                'nama_paket',
                'estimasi_selesai',
                'deskripsi',
                'status',
                'rating',
                'waktu_proses'
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('layanan_laundry', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
