<?php

use App\Services\Router;
use App\Http\Controllers;

$router = Router::getInstance();
$router->get('/foo', Controllers\FooController::class);
