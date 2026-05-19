<?php
$host = '127.0.0.1';
$db = 'db_tugasakhir';
$user = 'root';
$pass = '';

$email = 'superadmin@gmail.com';
$plain = 'password';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare('SELECT email, password FROM admins WHERE email = ?');
    $stmt->execute([$email]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        echo "NOT_FOUND: $email\n";
        exit(0);
    }

    $hash = $row['password'];
    echo "EMAIL: {$row['email']}\n";
    echo "HASH_PREFIX: " . substr($hash, 0, 4) . "\n";
    echo "HASH_LEN: " . strlen($hash) . "\n";

    if (password_verify($plain, $hash)) {
        echo "VERIFY_OK\n";
    } else {
        echo "VERIFY_FAIL\n";
    }
} catch (Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
