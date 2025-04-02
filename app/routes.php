<?php

use App\Services\Router;
use App\Http\Controllers;
use App\Services\MyService;

$router = Router::getInstance();

$router->get('/foo', Controllers\FooController::class);
$router->get('/bar', function (MyService $service) {
    $service->doSomething();
});

$router->get('/api/csrf-token', Controllers\API\CsrfTokenAction::class);
