<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add a marker column to admins to allow safe rollback
        if (Schema::hasTable('admins') && ! Schema::hasColumn('admins', 'migrated_from_users')) {
            Schema::table('admins', function (Blueprint $table) {
                $table->boolean('migrated_from_users')->default(false)->after('role');
            });
        }

        if (! Schema::hasTable('admins')) {
            // Nothing we can do if admins table doesn't exist
            return;
        }

        if (! Schema::hasTable('users')) {
            return;
        }

        // Determine source users to copy
        $sourceUsers = collect();

        if (Schema::hasColumn('users', 'role')) {
            $sourceUsers = DB::table('users')
                ->whereIn('role', ['super_admin', 'admin'])
                ->get();
        }

        // Always also try to copy a common superadmin email if present
        $maybe = DB::table('users')->where('email', 'superadmin@gmail.com')->get();
        if ($maybe->isNotEmpty()) {
            $sourceUsers = $sourceUsers->merge($maybe)->unique('email');
        }

        foreach ($sourceUsers as $u) {
            $exists = DB::table('admins')->where('email', $u->email)->exists();
            if (! $exists) {
                DB::table('admins')->insert([
                    'name' => $u->name,
                    'email' => $u->email,
                    'password' => $u->password,
                    'role' => property_exists($u, 'role') ? $u->role : 'admin',
                    'remember_token' => $u->remember_token ?? null,
                    'created_at' => $u->created_at ?? now(),
                    'updated_at' => $u->updated_at ?? now(),
                    'migrated_from_users' => true,
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('admins')) {
            return;
        }

        if (Schema::hasColumn('admins', 'migrated_from_users')) {
            DB::table('admins')->where('migrated_from_users', true)->delete();

            Schema::table('admins', function (Blueprint $table) {
                $table->dropColumn('migrated_from_users');
            });
        }
    }
};
