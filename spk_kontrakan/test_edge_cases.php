<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Http\Controllers\Api\SAWController;
use Illuminate\Http\Request;

$controller = new SAWController();

// Test Case 1: Cheapest budget (harga highest weight) + some facilities
echo "=== Test Case 1: Budget termurah (bobot: 70, 10, 0, 20) ===\n";
$request1 = Request::create('/api/saw/calculate/kontrakan', 'POST', [
    'bobot_harga' => 70,
    'bobot_jarak' => 10,
    'bobot_jumlah_kamar' => 0,
    'bobot_fasilitas' => 20,
]);
$response1 = $controller->calculateKontrakan($request1);
$data1 = json_decode($response1->getContent(), true);
echo "Success: " . ($data1['success'] ? 'YES' : 'NO') . "\n";
echo "Results: " . (isset($data1['data']['hasil']) ? count($data1['data']['hasil']) : 0) . " items\n\n";

// Test Case 2: Balanced preference
echo "=== Test Case 2: Balanced (bobot: 40, 20, 20, 20) ===\n";
$request2 = Request::create('/api/saw/calculate/kontrakan', 'POST', [
    'bobot_harga' => 40,
    'bobot_jarak' => 20,
    'bobot_jumlah_kamar' => 20,
    'bobot_fasilitas' => 20,
]);
$response2 = $controller->calculateKontrakan($request2);
$data2 = json_decode($response2->getContent(), true);
echo "Success: " . ($data2['success'] ? 'YES' : 'NO') . "\n";
echo "Results: " . (isset($data2['data']['hasil']) ? count($data2['data']['hasil']) : 0) . " items\n";
if (isset($data2['data']['hasil'][0])) {
    echo "Top result: " . $data2['data']['hasil'][0]['nama'] . " (score: " . round($data2['data']['hasil'][0]['skor'], 4) . ")\n";
}
echo "\n";

// Test Case 3: Facilities important
echo "=== Test Case 3: Facilities focused (bobot: 30, 20, 10, 40) ===\n";
$request3 = Request::create('/api/saw/calculate/kontrakan', 'POST', [
    'bobot_harga' => 30,
    'bobot_jarak' => 20,
    'bobot_jumlah_kamar' => 10,
    'bobot_fasilitas' => 40,
]);
$response3 = $controller->calculateKontrakan($request3);
$data3 = json_decode($response3->getContent(), true);
echo "Success: " . ($data3['success'] ? 'YES' : 'NO') . "\n";
echo "Results: " . (isset($data3['data']['hasil']) ? count($data3['data']['hasil']) : 0) . " items\n\n";

// Summary
echo "=== SUMMARY ===\n";
echo "All test cases should return > 0 results.\n";
echo "Case 1 (budget focused): " . (isset($data1['data']['hasil']) && count($data1['data']['hasil']) > 0 ? "✓ PASS" : "✗ FAIL") . "\n";
echo "Case 2 (balanced): " . (isset($data2['data']['hasil']) && count($data2['data']['hasil']) > 0 ? "✓ PASS" : "✗ FAIL") . "\n";
echo "Case 3 (facilities): " . (isset($data3['data']['hasil']) && count($data3['data']['hasil']) > 0 ? "✓ PASS" : "✗ FAIL") . "\n";
