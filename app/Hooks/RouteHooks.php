<?php

namespace App\Hooks;

use App\Hooks\Concerns\RegistersHooks;
use App\Hooks\Contracts\HooksInterface;
use App\Services\Router;

class RouteHooks implements HooksInterface
{
    use RegistersHooks;

    private $router;

    public function __construct(Router $router)
    {
        $this->router = $router;

        require __DIR__ . '/../routes.php';

        $this->addAction('init', function () {
            $routes = $this->router->getRoutes();

            foreach ($routes as $method => $paths) {
                foreach ($paths as $path => $callback) {
                    // Convert route pattern to regex for WordPress rewrite rules
                    $regex = preg_replace('/\{([^}]+)\}/', '([^/]+)', $path);
                    $regex = ltrim($regex, '/');
                    $regex = str_replace('/', '\/', $regex);
                    // Just pass a flag to indicate this is a custom route
                    // handleCustomRoutes() will take it from there.
                    add_rewrite_rule("^$regex/?$", "index.php?custom_route=1", 'top');
                }
            }
        });

        $this->addFilter('query_vars', function ($vars) {
            $vars[] = 'custom_route';
            return $vars;
        });

        // Flush rewrite rules when theme is activated
        $this->addAction('after_switch_theme', [$this, 'flushRewriteRules']);
    }

    public function initialize(): void
    {
        $this->addAction('template_redirect', [$this, 'handleCustomRoutes']);
    }

    public function handleCustomRoutes()
    {
        $custom_route = get_query_var('custom_route');
        if (!$custom_route) {
            return;
        }

        $request_method = $_SERVER['REQUEST_METHOD'];
        $request_uri = $_SERVER['REQUEST_URI'] ?? '';

        // Parse the URL path, ensuring we get a clean path
        $path = parse_url($request_uri, PHP_URL_PATH);
        if ($path === null) {
            return;
        }

        // Ensure the path starts with a forward slash
        $path = '/' . ltrim($path, '/');

        try {
            $this->router->handleRequest($request_method, $path);
        } catch (\Exception $e) {
            error_log('Route handling error: ' . $e->getMessage());
        }
    }

    public function flushRewriteRules(): void
    {
        flush_rewrite_rules();
    }
}
