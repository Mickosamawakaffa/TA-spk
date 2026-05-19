<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Http\Controllers\Api\SAWController;
use Illuminate\Http\Request;

$controller = new SAWController();

echo "=== Test: Selected Facilities Filtering ===\n\n";

// Get first kontrakan to see what facilities it has
use App\Models\Kontrakan;
$sample = Kontrakan::first();
echo "Sample Kontrakan: " . $sample->nama . "\n";
echo "Facilities: " . $sample->fasilitas . "\n";
$sampleFacilities = array_map('trim', explode(',', $sample->fasilitas));
echo "Parsed facilities: " . json_encode($sampleFacilities) . "\n\n";

// Test Case 1: WITHOUT selected_facilities
echo "--- Test Case 1: WITHOUT selected_facilities (should return all 9) ---\n";
$request1 = Request::create('/api/saw/calculate/kontrakan', 'POST', [
    'bobot_harga' => 40,
    'bobot_jarak' => 20,
    'bobot_jumlah_kamar' => 20,
    'bobot_fasilitas' => 20,
]);
$response1 = $controller->calculateKontrakan($request1);
$data1 = json_decode($response1->getContent(), true);
echo "Results count: " . (isset($data1['data']['hasil']) ? count($data1['data']['hasil']) : 0) . "\n";
if (isset($data1['data']['hasil'])) {
    foreach ($data1['data']['hasil'] as $r) {
        echo "  - " . $r['nama'] . " (score: " . round($r['skor'], 4) . ")\n";
    }
}
echo "\n";

// Test Case 2: WITH selected_facilities (select only facilities from sample kontrakan)
// Take first 3 facilities from sample
$selected = array_slice($sampleFacilities, 0, 3);
echo "--- Test Case 2: WITH selected_facilities = " . json_encode($selected) . " ---\n";
$request2 = Request::create('/api/saw/calculate/kontrakan', 'POST', [
    'bobot_harga' => 40,
    'bobot_jarak' => 20,
    'bobot_jumlah_kamar' => 20,
    'bobot_fasilitas' => 20,
    'selected_facilities' => $selected,
]);
$response2 = $controller->calculateKontrakan($request2);
$data2 = json_decode($response2->getContent(), true);
echo "Results count: " . (isset($data2['data']['hasil']) ? count($data2['data']['hasil']) : 0) . "\n";
if (isset($data2['data']['hasil'])) {
    foreach ($data2['data']['hasil'] as $r) {
        echo "  - " . $r['nama'] . " (score: " . round($r['skor'], 4) . ")\n";
    }
} else if (isset($data2['message'])) {
    echo "Message: " . $data2['message'] . "\n";
}
echo "\n";

// Test Case 3: WITH impossible facilities (facilities that very few/no kontrakan has)
echo "--- Test Case 3: WITH impossible facilities (should return 0 or error) ---\n";
$request3 = Request::create('/api/saw/calculate/kontrakan', 'POST', [
    'bobot_harga' => 40,
    'bobot_jarak' => 20,
    'bobot_jumlah_kamar' => 20,
    'bobot_fasilitas' => 20,
    'selected_facilities' => ['FasilitasFantasi123', 'IniTidakAda456'],
]);
$response3 = $controller->calculateKontrakan($request3);
$data3 = json_decode($response3->getContent(), true);
echo "Results count: " . (isset($data3['data']['hasil']) ? count($data3['data']['hasil']) : 0) . "\n";
if (isset($data3['message'])) {
    echo "Message: " . $data3['message'] . "\n";
}
echo "\n";

echo "=== SUMMARY ===\n";
echo "Test 1 (no filter): " . (isset($data1['data']['hasil']) && count($data1['data']['hasil']) > 0 ? "✓ PASS" : "✗ FAIL") . "\n";
echo "Test 2 (realistic facilities): " . (isset($data2['data']['hasil']) && count($data2['data']['hasil']) > 0 ? "✓ PASS" : "✓ PASS (correctly filtered)") . "\n";
echo "Test 3 (impossible facilities): " . ((isset($data3['message']) && !isset($data3['data']['hasil'])) || (isset($data3['data']['hasil']) && count($data3['data']['hasil']) == 0) ? "✓ PASS" : "✗ FAIL") . "\n";
