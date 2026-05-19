<?php
// Generate correct bcrypt hash for password "password"
$password = "password";
$hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

echo "Correct password hash for '{$password}':\n";
echo $hash . "\n\n";

// Verify it works
if (password_verify($password, $hash)) {
    echo "✅ Hash is valid and password_verify() works\n";
} else {
    echo "❌ Hash verification failed\n";
}
?>
