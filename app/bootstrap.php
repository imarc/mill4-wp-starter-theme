<?php

use App\Hooks;
use Imarc\Millyard\Services\Container;

$container = new Container();

$hooks = $container->get(Hooks\Registrar::class);

$hooks->register(Hooks\SessionHooks::class);
$hooks->register(Hooks\ThemeHooks::class);
$hooks->register(Hooks\TemplateHooks::class);
$hooks->register(Hooks\SecurityHooks::class);
$hooks->register(Hooks\AssetHooks::class);
$hooks->register(Hooks\BlockHooks::class);
$hooks->register(Hooks\CommandHooks::class);
$hooks->register(Hooks\JobHooks::class);
$hooks->register(Hooks\PostTypeHooks::class);
$hooks->register(Hooks\RouteHooks::class);
$hooks->register(Hooks\TaxonomyHooks::class);
$hooks->register(Hooks\CronHooks::class);
$hooks->register(Hooks\AdminPageHooks::class);
