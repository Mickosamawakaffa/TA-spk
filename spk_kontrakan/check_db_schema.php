<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_tugasakhir";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "=== USER TABLE SCHEMA ===\n\n";
$result = $conn->query("DESCRIBE users");
while($row = $result->fetch_assoc()) {
    echo "- {$row['Field']} ({$row['Type']}) " . ($row['Null'] === 'NO' ? '[NOT NULL]' : '[NULLABLE]') . "\n";
}

$conn->close();
?>
