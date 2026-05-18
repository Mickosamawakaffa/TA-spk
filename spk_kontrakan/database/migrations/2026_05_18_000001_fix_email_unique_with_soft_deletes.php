<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Force delete soft-deleted users to clean up
        DB::table('users')->whereNotNull('deleted_at')->delete();
    }

    public function down(): void
    {
        // Cannot restore deleted records
    }
};
