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
        Schema::table('bookings', function (Blueprint $table) {
            // Owner verification fields
            $table->enum('owner_verification_status', ['pending', 'verified', 'rejected'])->default('pending')->after('status');
            $table->timestamp('owner_verified_at')->nullable()->after('owner_verification_status');
            $table->text('owner_verification_note')->nullable()->after('owner_verified_at');
            
            // Notify owner field
            $table->timestamp('owner_notified_at')->nullable()->after('owner_verification_note');
            
            // Index untuk query
            $table->index(['kontrakan_id', 'owner_verification_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex(['kontrakan_id', 'owner_verification_status']);
            $table->dropColumn([
                'owner_verification_status',
                'owner_verified_at',
                'owner_verification_note',
                'owner_notified_at',
            ]);
        });
    }
};
