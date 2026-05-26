<?php

use App\Http\Controllers\DashboardController;
use App\Services\AIService;
use Illuminate\Support\Facades\Facade;

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "--- Testing Dashboard Logic ---\n";
    $service = new AIService();
    $controller = new DashboardController();
    
    echo "1. Testing AI Service insights...\n";
    $insights = $service->getInsights();
    echo "AI Insights generated successfully.\n";
    
    echo "2. Testing Dashboard Controller index...\n";
    $response = $controller->index($service);
    echo "Dashboard Controller response generated successfully.\n";
    
    echo "3. Testing View Rendering...\n";
    $html = $response->render();
    echo "Dashboard View rendered successfully. Length: " . strlen($html) . "\n";
    
    echo "--- SUCCESS ---\n";
} catch (\Exception $e) {
    echo "--- FAILURE ---\n";
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
}
