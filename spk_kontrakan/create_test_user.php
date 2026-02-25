<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;

// Create test user
User::updateOrCreate(
    ['email' => 'test@example.com'],
    [
        'name' => 'Test User',
        'password' => bcrypt('password123'),
        'role' => 'user',
        'phone' => '08123456789',
    ]
);

echo "✅ User berhasil dibuat/updated!\n";
echo "📧 Email: test@example.com\n";
echo "🔑 Password: password123\n";
echo "👤 Role: user\n";
