<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update nama_kriteria "luas" menjadi "jumlah_kamar" untuk tipe_bisnis kontrakan
        // Case-insensitive update untuk menangani "Luas", "luas", "LUAS", dll
        DB::table('kriteria')
            ->where('tipe_bisnis', 'kontrakan')
            ->where(function($query) {
                $query->where('nama_kriteria', 'LIKE', '%luas%')
                      ->orWhere('nama_kriteria', 'LIKE', '%Luas%')
                      ->orWhere('nama_kriteria', 'LIKE', '%LUAS%');
            })
            ->update([
                'nama_kriteria' => DB::raw("REPLACE(REPLACE(REPLACE(nama_kriteria, 'luas', 'jumlah_kamar'), 'Luas', 'Jumlah Kamar'), 'LUAS', 'JUMLAH KAMAR')")
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse: Update kembali "jumlah_kamar" menjadi "luas"
        DB::table('kriteria')
            ->where('tipe_bisnis', 'kontrakan')
            ->where(function($query) {
                $query->where('nama_kriteria', 'LIKE', '%jumlah_kamar%')
                      ->orWhere('nama_kriteria', 'LIKE', '%Jumlah Kamar%')
                      ->orWhere('nama_kriteria', 'LIKE', '%JUMLAH KAMAR%');
            })
            ->update([
                'nama_kriteria' => DB::raw("REPLACE(REPLACE(REPLACE(nama_kriteria, 'jumlah_kamar', 'luas'), 'Jumlah Kamar', 'Luas'), 'JUMLAH KAMAR', 'LUAS')")
            ]);
    }
};
