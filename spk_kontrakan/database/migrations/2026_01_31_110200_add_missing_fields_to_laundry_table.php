<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('laundry', function (Blueprint $table) {
            if (!Schema::hasColumn('laundry', 'no_whatsapp')) {
                $table->string('no_whatsapp')->nullable()->after('alamat');
            }
            if (!Schema::hasColumn('laundry', 'jam_buka')) {
                $table->time('jam_buka')->default('08:00')->after('fasilitas');
            }
            if (!Schema::hasColumn('laundry', 'jam_tutup')) {
                $table->time('jam_tutup')->default('20:00')->after('jam_buka');
            }
            if (!Schema::hasColumn('laundry', 'status')) {
                $table->enum('status', ['buka', 'tutup'])->default('buka')->after('jam_tutup');
            }
        });
    }

    public function down(): void
    {
        Schema::table('laundry', function (Blueprint $table) {
            $table->dropColumn(['no_whatsapp', 'jam_buka', 'jam_tutup', 'status']);
        });
    }
};
