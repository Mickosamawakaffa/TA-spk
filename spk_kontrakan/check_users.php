<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== USERS IN DATABASE ===\n\n";

$users = \App\Models\User::select('id', 'name', 'email')->get();

if ($users->isEmpty()) {
    echo "Tidak ada user di database!\n\n";
    echo "Membuat user test...\n";
    
    $user = \App\Models\User::create([
        'name' => 'Test User',
        'email' => 'test@gmail.com',
        'password' => \Illuminate\Support\Facades\Hash::make('password123'),
    ]);
    
    echo "User berhasil dibuat:\n";
    echo "Email: test@gmail.com\n";
    echo "Password: password123\n";
} else {
    echo "Total users: " . $users->count() . "\n\n";
    foreach ($users as $user) {
        echo "ID: {$user->id} | Name: {$user->name} | Email: {$user->email}\n";
    }
}

echo "\n=== SELESAI ===\n";
