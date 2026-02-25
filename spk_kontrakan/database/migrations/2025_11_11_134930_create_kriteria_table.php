<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kriteria', function (Blueprint $table) {
            $table->id();
            $table->enum('tipe_bisnis', ['kontrakan', 'laundry']); // DITAMBAHKAN
            $table->string('nama_kriteria');
            $table->float('bobot');
            $table->enum('tipe', ['Benefit', 'Cost']);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kriteria');
    }
};