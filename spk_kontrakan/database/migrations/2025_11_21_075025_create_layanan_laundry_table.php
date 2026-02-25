<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('layanan_laundry', function (Blueprint $table) {
            $table->id();
            $table->foreignId('laundry_id')->constrained('laundry')->onDelete('cascade');
            $table->enum('jenis_layanan', ['express', 'reguler', 'kilat']);
            $table->integer('harga');
            $table->integer('kecepatan');
            $table->enum('satuan_kecepatan', ['jam', 'hari']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('layanan_laundry');
    }
};