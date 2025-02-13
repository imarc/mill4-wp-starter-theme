<?php

use App\Services\Router;
use App\Http\Controllers;
use App\Services\MyService;

$router = Router::getInstance();
$router->get('/foo', Controllers\FooController::class);
$router->get('/bar', function (MyService $service) {
    // Use the injected MyService instance
    $service->doSomething();
});
