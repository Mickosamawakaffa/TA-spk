<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('kontrakans', function (Blueprint $table) {
            $table->string('status')->default('available')->after('foto')->comment('available, booked, occupied, maintenance');
            $table->date('occupied_until')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('kontrakans', function (Blueprint $table) {
            $table->dropColumn(['status', 'occupied_until']);
        });
    }
};
