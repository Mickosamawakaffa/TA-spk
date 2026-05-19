<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $email = 'superadmin@gmail.com';

        $exists = DB::table('admins')->where('email', $email)->exists();
        if ($exists) {
            $this->command->info('Superadmin already exists in admins table.');
            return;
        }

        // Try copy from users
        if (DB::table('users')->where('email', $email)->exists()) {
            $u = DB::table('users')->where('email', $email)->first();
            DB::table('admins')->insert([
                'name' => $u->name,
                'email' => $u->email,
                'password' => $u->password,
                'role' => 'super_admin',
                'remember_token' => $u->remember_token ?? null,
                'created_at' => $u->created_at ?? now(),
                'updated_at' => $u->updated_at ?? now(),
            ]);
            $this->command->info('Superadmin copied from users to admins.');
            return;
        }

        // Create default superadmin
        $password = Hash::make('password');
        DB::table('admins')->insert([
            'name' => 'Super Admin',
            'email' => $email,
            'password' => $password,
            'role' => 'super_admin',
            'remember_token' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info('Default superadmin created: '.$email.' / password');
    }
}
<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat super admin
        Admin::create([
            'name' => 'Super Admin',
            'email' => 'super@admin.com',
            'password' => Hash::make('password123'),
            'role' => 'super_admin'
        ]);

        // Buat admin biasa
        Admin::create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('password123'),
            'role' => 'admin'
        ]);
    }
}
