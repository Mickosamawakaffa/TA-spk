<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if super admin already exists
        $existingSuperAdmin = User::where('email', 'superadmin@gmail.com')->first();
        
        if ($existingSuperAdmin) {
            // Update existing super admin
            $existingSuperAdmin->update([
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'role' => 'super_admin',
            ]);
            
            $this->command->info('✅ Super Admin updated successfully!');
        } else {
            // Create new super admin
            User::create([
                'name' => 'Super Admin',
                'email' => 'superadmin@gmail.com',
                'password' => Hash::make('password'),
                'role' => 'super_admin',
                'email_verified_at' => now(),
            ]);
            
            $this->command->info('✅ Super Admin created successfully!');
        }
        
        $this->command->info('📧 Email: superadmin@gmail.com');
        $this->command->info('🔑 Password: password');
        $this->command->line('');
        
        // Also create a regular admin for testing
        $existingAdmin = User::where('email', 'admin@gmail.com')->first();
        
        if (!$existingAdmin) {
            User::create([
                'name' => 'Admin',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]);
            
            $this->command->info('✅ Regular Admin created successfully!');
            $this->command->info('📧 Email: admin@gmail.com');
            $this->command->info('🔑 Password: password');
        }
    }
}
