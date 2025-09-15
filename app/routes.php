<?php

use App\Http\Controllers;
use App\Http\Middleware\VerifyCsrfToken;
use Imarc\Millyard\Routing\Router;

$router = Router::getInstance();
$router->setDefaultMiddleware([
    VerifyCsrfToken::class,
]);
// $router->get('/api/foo', FooController::class);
$router->get('/api/csrf-token', Controllers\API\CsrfTokenAction::class);
