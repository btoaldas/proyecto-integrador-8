<?php

define('LARAVEL_START', microtime(true));

// Check if application is in maintenance mode
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request
$app = require_once __DIR__.'/../bootstrap/app.php';

$response = $app->handle(
    $request = Illuminate\Http\Request::capture()
);

$response->send();

$app->terminate($request, $response);