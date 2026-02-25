<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== RESET PASSWORD USER ===\n\n";

// Update password untuk semua user menjadi 'password123'
$newPassword = 'password123';
$hashedPassword = \Illuminate\Support\Facades\Hash::make($newPassword);

\App\Models\User::query()->update(['password' => $hashedPassword]);

echo "Password semua user berhasil direset!\n\n";
echo "Gunakan kredensial berikut untuk login:\n\n";

$users = \App\Models\User::select('id', 'name', 'email')->get();
foreach ($users as $user) {
    echo "Email: {$user->email}\n";
    echo "Password: {$newPassword}\n";
    echo "---\n";
}

echo "\n=== SELESAI ===\n";
