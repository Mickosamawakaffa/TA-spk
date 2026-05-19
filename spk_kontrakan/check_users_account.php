<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/bootstrap/app.php';

use App\Models\User;
use App\Models\Admin;

echo "=== Checking Users Table ===\n";
$users = User::all(['id', 'name', 'email', 'created_at']);
if ($users->isEmpty()) {
    echo "❌ No users found\n";
} else {
    echo "✅ Users found:\n";
    foreach ($users as $user) {
        echo "ID: {$user->id} | Name: {$user->name} | Email: {$user->email} | Created: {$user->created_at}\n";
    }
}

echo "\n=== Checking Admins Table ===\n";
try {
    $admins = Admin::all(['id', 'name', 'email', 'created_at']);
    if ($admins->isEmpty()) {
        echo "❌ No admins found\n";
    } else {
        echo "✅ Admins found:\n";
        foreach ($admins as $admin) {
            echo "ID: {$admin->id} | Name: {$admin->name} | Email: {$admin->email} | Created: {$admin->created_at}\n";
        }
    }
} catch (\Exception $e) {
    echo "⚠️ Admin model error: " . $e->getMessage() . "\n";
}
