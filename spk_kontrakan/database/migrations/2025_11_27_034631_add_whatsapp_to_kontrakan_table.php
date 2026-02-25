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
        Schema::table('kontrakans', function (Blueprint $table) {
            // Tambah kolom nomor WhatsApp pemilik
            $table->string('no_whatsapp', 20)->nullable()->after('alamat');
            
            // Opsional: Tambah nama pemilik juga (jika belum ada)
            // $table->string('nama_pemilik', 100)->nullable()->after('nama');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kontrakans', function (Blueprint $table) {
            $table->dropColumn('no_whatsapp');
            
            // Jika tambah nama_pemilik, uncomment baris ini:
            // $table->dropColumn('nama_pemilik');
        });
    }
};