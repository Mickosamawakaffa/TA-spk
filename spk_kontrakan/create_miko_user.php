<?php
require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Http\Kernel')->handle(
    $request = Illuminate\Http\Request::capture()
);

use App\Models\User;
use Illuminate\Support\Facades\Hash;

try {
    // Check if user already exists
    $existingUser = User::where('email', 'miko@gmail.com')->first();
    
    if ($existingUser) {
        echo "User miko@gmail.com sudah ada di database\n";
        echo "ID: " . $existingUser->id . "\n";
        echo "Name: " . $existingUser->name . "\n";
        exit;
    }

    // Create new user
    $user = User::create([
        'name' => 'Miko',
        'email' => 'miko@gmail.com',
        'password' => Hash::make('password123'),
        'phone' => '08123456789',
        'role' => 'user',
    ]);

    echo "✓ User berhasil dibuat!\n";
    echo "Email: miko@gmail.com\n";
    echo "Password: password123\n";
    echo "ID: " . $user->id . "\n";
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
