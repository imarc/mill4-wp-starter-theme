<?php

use App\Hooks;
use App\Services\Container;

$container = new Container();

$hooks = $container->get(Hooks\Registrar::class);
$hooks->register(Hooks\ThemeHooks::class);
$hooks->register(Hooks\BlockHooks::class);
$hooks->register(Hooks\SecurityHooks::class);
