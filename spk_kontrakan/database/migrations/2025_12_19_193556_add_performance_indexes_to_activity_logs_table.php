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
        Schema::table('activity_logs', function (Blueprint $table) {
            // Composite index for action + created_at (for filtering by action type and date)
            $table->index(['action', 'created_at'], 'idx_action_created_at');
            
            // Index for IP address (for security monitoring)
            $table->index('ip_address', 'idx_ip_address');
            
            // Composite index for model queries
            $table->index(['model_type', 'model_id'], 'idx_model_type_id');
            
            // Index for user activity queries
            $table->index(['user_id', 'action'], 'idx_user_action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropIndex('idx_action_created_at');
            $table->dropIndex('idx_ip_address');
            $table->dropIndex('idx_model_type_id');
            $table->dropIndex('idx_user_action');
        });
    }
};
