<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['kontrakan', 'laundry']);
            $table->unsignedBigInteger('item_id');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->unsignedTinyInteger('rating'); // 1-5
            $table->text('review')->nullable();
            $table->timestamps();
            
            $table->unique(['type', 'item_id', 'user_id']);
            $table->index(['type', 'item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};