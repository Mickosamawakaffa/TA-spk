<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Change enum back to express, reguler, kilat
        Schema::table('layanan_laundry', function (Blueprint $table) {
            // First update any existing values to valid ones
            DB::table('layanan_laundry')->update([
                'jenis_layanan' => DB::raw("CASE 
                    WHEN jenis_layanan = 'kiloan' THEN 'reguler'
                    WHEN jenis_layanan = 'satuan' THEN 'express'
                    ELSE 'reguler'
                END")
            ]);
            
            // Change column type (MySQL)
            $table->enum('jenis_layanan', ['express', 'reguler', 'kilat'])->change();
        });
    }

    public function down(): void
    {
        Schema::table('layanan_laundry', function (Blueprint $table) {
            // Revert back to kiloan, satuan
            DB::table('layanan_laundry')->update([
                'jenis_layanan' => DB::raw("CASE 
                    WHEN jenis_layanan = 'reguler' THEN 'kiloan'
                    WHEN jenis_layanan = 'express' THEN 'satuan'
                    ELSE 'kiloan'
                END")
            ]);
            
            $table->enum('jenis_layanan', ['kiloan', 'satuan'])->change();
        });
    }
};
