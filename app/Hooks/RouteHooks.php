<?php

namespace App\Hooks;

use Imarc\Millyard\Contracts\HooksInterface;
use Imarc\Millyard\Hooks\AbstractRouteHooks;

class RouteHooks extends AbstractRouteHooks implements HooksInterface
{
    protected function loadRoutes(): void
    {
        require __DIR__ . '/../routes.php';
    }
}
