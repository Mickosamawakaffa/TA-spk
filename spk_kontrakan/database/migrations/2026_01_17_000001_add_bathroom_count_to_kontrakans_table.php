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
            $table->integer('bathroom_count')->default(1)->after('jumlah_kamar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kontrakans', function (Blueprint $table) {
            $table->dropColumn('bathroom_count');
        });
    }
};
