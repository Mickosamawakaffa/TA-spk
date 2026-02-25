<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('galeri', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['kontrakan', 'laundry']);
            $table->unsignedBigInteger('item_id');
            $table->string('foto');
            $table->unsignedSmallInteger('urutan')->default(0);
            $table->boolean('is_primary')->default(false); // Foto utama untuk thumbnail
            $table->string('caption')->nullable(); // Keterangan foto (opsional)
            $table->timestamps();
            
            $table->index(['type', 'item_id', 'urutan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('galeri');
    }
};