<?php
// Simple HTTP test tanpa Laravel ORM
$url = 'http://localhost:8000/api/saw/calculate/kontrakan';
$data = [
    'bobot_harga' => 50,
    'bobot_jarak' => 20,
    'bobot_jumlah_kamar' => 15,
    'bobot_fasilitas' => 15
];

echo "Testing koneksi ke: $url\n";
echo "dengan data: " . json_encode($data) . "\n\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "HTTP Status: $httpCode\n";
if ($error) {
    echo "Error: $error\n";
} else {
    echo "Response:\n";
    echo $response ? json_encode(json_decode($response), JSON_PRETTY_PRINT) : "(empty)";
    echo "\n";
}
