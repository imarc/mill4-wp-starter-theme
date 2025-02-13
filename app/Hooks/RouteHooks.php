<?php

namespace App\Hooks;

use App\Hooks\Contracts\HooksInterface;
use App\Services\Router;

class RouteHooks implements HooksInterface
{
    private $router;

    public function __construct(Router $router)
    {
        $this->router = $router;

        require __DIR__ . '/../routes.php';

        add_action('init', function () {
            $routes = $this->router->getRoutes();

            foreach ($routes as $method => $paths) {
                foreach ($paths as $path => $callback) {
                    $regex = preg_replace('/^\//', '', $path);
                    $regex = str_replace('/', '\/', $regex);
                    add_rewrite_rule("^$regex/?$", "index.php?custom_route=$path", 'top');
                }
            }
        });

        add_filter('query_vars', function ($vars) {
            $vars[] = 'custom_route';
            return $vars;
        });
    }

    public function initialize(): void
    {
        add_action('template_redirect', [$this, 'handleCustomRoutes']);
    }

    public function handleCustomRoutes()
    {
        $custom_route = get_query_var('custom_route');
        if ($custom_route) {
            $request_method = $_SERVER['REQUEST_METHOD'];
            $this->router->handleRequest($request_method, $custom_route);
        }
    }
}
