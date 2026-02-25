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
        Schema::create('kontrakans', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('alamat');
            $table->integer('harga');       // harga kontrakan
            $table->integer('jarak');       // jarak ke kampus (meter/km)
            $table->string('fasilitas')->nullable();
            $table->integer('luas');        // luas bangunan
            $table->timestamps();           // created_at & updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kontrakans');
    }
};
