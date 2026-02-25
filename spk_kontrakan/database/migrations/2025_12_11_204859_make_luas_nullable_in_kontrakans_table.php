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
        // Mengubah kolom luas menjadi nullable dengan default 0
        DB::statement('ALTER TABLE `kontrakans` MODIFY `luas` INTEGER NULL DEFAULT 0');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kembalikan ke NOT NULL (pastikan semua record punya nilai)
        DB::statement('UPDATE `kontrakans` SET `luas` = 0 WHERE `luas` IS NULL');
        DB::statement('ALTER TABLE `kontrakans` MODIFY `luas` INTEGER NOT NULL');
    }
};
