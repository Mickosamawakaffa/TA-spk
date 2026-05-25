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
            // Tambah kolom admin_id sebagai foreign key ke admins table
            $table->foreignId('admin_id')
                ->nullable()
                ->after('id')
                ->constrained('admins')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kontrakans', function (Blueprint $table) {
            $table->dropForeignKey(['admin_id']);
            $table->dropColumn('admin_id');
        });
    }
};
