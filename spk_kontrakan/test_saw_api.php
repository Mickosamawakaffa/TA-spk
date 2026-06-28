<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$request = \Illuminate\Http\Request::create('/api/saw/calculate/kontrakan', 'POST', [
    'bobot_harga' => 25,
    'bobot_jarak' => 25,
    'bobot_jumlah_kamar' => 25,
    'bobot_fasilitas' => 25,
]);

$controller = new \App\Http\Controllers\Api\SAWController();
$response = $controller->calculateKontrakan($request);

echo "Status Code: " . $response->getStatusCode() . "\n";
echo "Content: \n";
$data = json_decode($response->getContent(), true);
echo json_encode($data, JSON_PRETTY_PRINT);
