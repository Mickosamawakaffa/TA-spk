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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kontrakan_id')->constrained('kontrakans')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            // Periode sewa
            $table->date('start_date');
            $table->date('end_date');
            
            // Status booking: pending, confirmed, checked_in, completed, cancelled
            $table->string('status')->default('pending');
            
            // Info pembayaran
            $table->decimal('amount', 12, 2)->nullable();
            $table->string('payment_status')->default('unpaid'); // unpaid, paid, refunded
            $table->string('payment_method')->nullable();
            $table->timestamp('paid_at')->nullable();
            
            // Info tambahan
            $table->text('notes')->nullable();
            $table->string('tenant_name')->nullable(); // Nama penyewa (jika beda dari user)
            $table->string('tenant_phone')->nullable();
            
            // Tracking
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('checked_in_at')->nullable();
            $table->timestamp('checked_out_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            
            $table->timestamps();
            
            // Indexes untuk query performa
            $table->index(['kontrakan_id', 'status']);
            $table->index(['kontrakan_id', 'start_date', 'end_date']);
            $table->index(['user_id', 'status']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
