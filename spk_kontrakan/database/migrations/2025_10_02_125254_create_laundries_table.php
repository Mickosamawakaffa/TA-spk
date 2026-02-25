<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laundry', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->text('alamat');
            $table->integer('jarak'); // jarak ke kampus/dorm
            $table->text('fasilitas')->nullable(); // fasilitas laundry
            $table->string('foto')->nullable(); // foto laundry
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laundry');
    }
};