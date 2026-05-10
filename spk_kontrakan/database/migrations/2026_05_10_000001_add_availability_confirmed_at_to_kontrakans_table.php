 <?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kontrakans', function (Blueprint $table) {
            $table->timestamp('availability_confirmed_at')->nullable()->after('occupied_until');
        });
    }

    public function down(): void
    {
        Schema::table('kontrakans', function (Blueprint $table) {
            $table->dropColumn('availability_confirmed_at');
        });
    }
};
