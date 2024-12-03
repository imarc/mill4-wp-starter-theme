<?php

use App\Hooks;
use App\Providers;
use App\Services\Container;

$container = new Container();
$container->addServiceProvider(new Providers\SiteServiceProvider());

$hooks = $container->get(Hooks\Registrar::class);
$hooks->register(Hooks\ThemeHooks::class);
$hooks->register(Hooks\BlockHooks::class);
$hooks->register(Hooks\SecurityHooks::class);
