<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('layanan_laundry', function (Blueprint $table) {
            // Drop old columns
            $table->dropColumn(['jenis_layanan', 'kecepatan', 'satuan_kecepatan']);
        });
        
        Schema::table('layanan_laundry', function (Blueprint $table) {
            // Add new columns
            $table->enum('jenis_layanan', ['kiloan', 'satuan'])->default('kiloan')->after('laundry_id');
            $table->string('nama_paket')->after('jenis_layanan');
            $table->integer('estimasi_selesai')->comment('dalam jam')->after('harga');
            $table->text('deskripsi')->nullable()->after('estimasi_selesai');
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif')->after('deskripsi');
        });
    }

    public function down(): void
    {
        Schema::table('layanan_laundry', function (Blueprint $table) {
            $table->dropColumn(['jenis_layanan', 'nama_paket', 'estimasi_selesai', 'deskripsi', 'status']);
        });
        
        Schema::table('layanan_laundry', function (Blueprint $table) {
            $table->enum('jenis_layanan', ['express', 'reguler', 'kilat']);
            $table->integer('kecepatan');
            $table->enum('satuan_kecepatan', ['jam', 'hari']);
        });
    }
};
