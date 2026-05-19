<?php
// Reset Super Admin Account
// Hapus super admin lama dan buat yang baru

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_tugasakhir";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

// Data super admin baru
$email = "mikoadmin@gmail.com";
$plain_password = "12345678";
$hashed_password = password_hash($plain_password, PASSWORD_BCRYPT);
$name = "Super Admin";
$role = "super_admin";

try {
    // Start transaction
    $conn->begin_transaction();
    
    // 1. Hapus super admin lama
    $delete_sql = "DELETE FROM admins WHERE role = 'super_admin'";
    if (!$conn->query($delete_sql)) {
        throw new Exception("Error deleting old super admin: " . $conn->error);
    }
    echo "✓ Super admin lama berhasil dihapus<br>";
    
    // 2. Buat super admin baru
    $insert_sql = "INSERT INTO admins (name, email, password, role, email_verified_at, created_at, updated_at) 
                   VALUES (?, ?, ?, ?, NOW(), NOW(), NOW())";
    
    $stmt = $conn->prepare($insert_sql);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    $stmt->bind_param("ssss", $name, $email, $hashed_password, $role);
    
    if (!$stmt->execute()) {
        throw new Exception("Error creating new super admin: " . $stmt->error);
    }
    
    $conn->commit();
    echo "✓ Super admin baru berhasil dibuat<br><br>";
    
    echo "<strong>Detail Super Admin Baru:</strong><br>";
    echo "Email: <strong>" . htmlspecialchars($email) . "</strong><br>";
    echo "Password: <strong>" . htmlspecialchars($plain_password) . "</strong><br>";
    echo "Role: <strong>" . htmlspecialchars($role) . "</strong><br><br>";
    echo "✅ Proses selesai! Kamu sekarang bisa login dengan akun baru.";
    
    $stmt->close();
    
} catch (Exception $e) {
    $conn->rollback();
    echo "❌ Error: " . $e->getMessage();
}

$conn->close();
?>
