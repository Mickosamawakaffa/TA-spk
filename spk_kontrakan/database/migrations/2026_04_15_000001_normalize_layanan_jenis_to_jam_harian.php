<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Normalize legacy layanan types to new canonical values:
     * - harian: regular/day package
     * - jam: fast/hour package
     */
    public function up(): void
    {
        DB::table('layanan_laundry')
            ->whereIn('jenis_layanan', ['reguler', 'kiloan'])
            ->update(['jenis_layanan' => 'harian']);

        DB::table('layanan_laundry')
            ->whereIn('jenis_layanan', ['express', 'kilat', 'satuan'])
            ->update(['jenis_layanan' => 'jam']);
    }

    /**
     * Revert to closest legacy labels for compatibility.
     */
    public function down(): void
    {
        DB::table('layanan_laundry')
            ->where('jenis_layanan', 'harian')
            ->update(['jenis_layanan' => 'reguler']);

        DB::table('layanan_laundry')
            ->where('jenis_layanan', 'jam')
            ->update(['jenis_layanan' => 'express']);
    }
};
