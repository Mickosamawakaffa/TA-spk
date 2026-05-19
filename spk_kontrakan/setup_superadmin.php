<?php
// Direct database connection to create super admin
$host = '127.0.0.1';
$db = 'db_tugasakhir';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Ensure super admin exists in `admins` table. If not, try to copy from `users`, otherwise create.
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE email = ?");
    $stmt->execute(['superadmin@gmail.com']);
    $existingAdmin = $stmt->fetch();

    if ($existingAdmin) {
        echo "✅ Super Admin sudah ada di tabel admins:\n";
        echo "ID: {$existingAdmin['id']}\n";
        echo "Name: {$existingAdmin['name']}\n";
        echo "Email: {$existingAdmin['email']}\n";
        echo "Role: {$existingAdmin['role']}\n";
    } else {
        // Try to copy from users table if present
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute(['superadmin@gmail.com']);
        $fromUser = $stmt->fetch();

        if ($fromUser) {
            $insert = $pdo->prepare("INSERT INTO admins (name, email, password, role, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())");
            $insert->execute([$fromUser['name'], $fromUser['email'], $fromUser['password'], 'super_admin']);
            echo "✅ Super Admin berhasil disalin dari users ke admins.\n";
            echo "Email: {$fromUser['email']}\n";
        } else {
            // Create new super admin
            $password = password_hash('password', PASSWORD_BCRYPT, ['cost' => 12]);
            $stmt = $pdo->prepare("INSERT INTO admins (name, email, password, role, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())");
            $stmt->execute(['Super Admin', 'superadmin@gmail.com', $password, 'super_admin']);
            echo "✅ Super Admin baru berhasil dibuat di tabel admins!\n";
            echo "Email: superadmin@gmail.com\n";
            echo "Password: password\n";
        }
    }
    
    // Also show all users
    echo "\n=== Daftar User di Database ===\n";
    $stmt = $pdo->query("SELECT id, name, email, role FROM users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($users)) {
        echo "❌ Tidak ada user\n";
    } else {
        foreach ($users as $u) {
            echo "{$u['id']}. {$u['name']} ({$u['email']}) - {$u['role']}\n";
        }
    }
    
} catch (PDOException $e) {
    echo "❌ Database Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>
