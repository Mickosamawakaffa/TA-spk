<?php

echo "=== TESTING REGISTER WITH bagas@gmail.com ===\n\n";

$url = "http://10.192.233.99:8000/api/register";
$data = [
    'name' => 'Bagas',
    'email' => 'bagas@gmail.com',
    'password' => 'password123',
    'password_confirmation' => 'password123',
    'phone' => '08123456789'
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json',
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);
$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Status: $status_code\n";
echo "Response:\n";
echo json_encode(json_decode($response), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";

?>
