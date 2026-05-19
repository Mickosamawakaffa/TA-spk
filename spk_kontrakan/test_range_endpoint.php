<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Http\Controllers\Api\KontrakanController;

$controller = new KontrakanController();
$response = $controller->getRange();
echo $response->getContent();
