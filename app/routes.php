<?php

use App\Services\Router;
use App\Http\Controllers;

$router = Router::getInstance();

$router->get('/api/csrf-token', Controllers\API\CsrfTokenAction::class);
