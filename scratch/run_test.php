<?php

require __DIR__ . '/../bootstrap/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$request = Illuminate\Http\Request::create('/herramientas/ubicaciones', 'GET');
$response = $kernel->handle($request);

echo "STATUS: " . $response->getStatusCode() . "\n";
if ($response->getStatusCode() === 500) {
    echo $response->getContent();
}
