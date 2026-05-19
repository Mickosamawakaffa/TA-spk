<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_tugasakhir";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "=== LATEST USER IN DATABASE ===\n\n";

$result = $conn->query("SELECT * FROM users ORDER BY id DESC LIMIT 1");
if($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo "User ID: " . $row['id'] . "\n";
    echo "Name: " . $row['name'] . "\n";
    echo "Email: " . $row['email'] . "\n";
    echo "Phone: " . $row['phone'] . "\n";
    echo "Role: " . ($row['role'] ?? 'NULL') . "\n";
    echo "Created: " . $row['created_at'] . "\n";
}

$conn->close();
?>
