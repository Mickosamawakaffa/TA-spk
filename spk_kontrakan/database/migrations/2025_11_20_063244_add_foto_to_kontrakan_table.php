<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('kontrakans', function (Blueprint $table) {
            // ↑ Ubah 'kontrakan' jadi 'kontrakans'
            $table->string('foto')->nullable();
        });
    }

    public function down()
    {
        Schema::table('kontrakans', function (Blueprint $table) {
            // ↑ Ubah 'kontrakan' jadi 'kontrakans'
            $table->dropColumn('foto');
        });
    }
};