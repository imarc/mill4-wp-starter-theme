<?php

use App\Services\Router;
use App\Http\Controllers;
use App\Http\Middleware\VerifyCsrfToken;

$router = Router::getInstance();
$router->setDefaultMiddleware([
    VerifyCsrfToken::class,
]);
$router->get('/api/csrf-token', Controllers\API\CsrfTokenAction::class);
