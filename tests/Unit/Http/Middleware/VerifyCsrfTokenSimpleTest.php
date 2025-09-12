<?php

namespace App\Tests\Unit\Http\Middleware;

use App\Http\Middleware\VerifyCsrfToken;
use App\Tests\BaseTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Simple unit tests for VerifyCsrfToken middleware structure
 * Avoids config loading issues by testing only safe methods
 */
class VerifyCsrfTokenSimpleTest extends BaseTestCase
{
    private VerifyCsrfToken $middleware;

    protected function setUp(): void
    {
        parent::setUp();
        $this->middleware = new VerifyCsrfToken();
    }

    /**
     * Test that the middleware implements the correct interface
     */
    public function test_implements_middleware_interface()
    {
        $this->assertInstanceOf('Imarc\Millyard\Contracts\MiddlewareInterface', $this->middleware);
    }

    /**
     * Test that the middleware has the handle method
     */
    public function test_has_handle_method()
    {
        $this->assertTrue(method_exists($this->middleware, 'handle'));
        $this->assertTrue(is_callable([$this->middleware, 'handle']));
    }

    /**
     * Test GET requests pass through without CSRF verification
     */
    public function test_get_requests_pass_through()
    {
        $request = Request::create('/test', 'GET');
        $next = function ($request) {
            return new Response('Success', 200);
        };

        $response = $this->middleware->handle($request, $next);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Success', $response->getContent());
    }

    /**
     * Test HEAD requests pass through without CSRF verification
     */
    public function test_head_requests_pass_through()
    {
        $request = Request::create('/test', 'HEAD');
        $next = function ($request) {
            return new Response('Success', 200);
        };

        $response = $this->middleware->handle($request, $next);

        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * Test OPTIONS requests pass through without CSRF verification
     */
    public function test_options_requests_pass_through()
    {
        $request = Request::create('/test', 'OPTIONS');
        $next = function ($request) {
            return new Response('Success', 200);
        };

        $response = $this->middleware->handle($request, $next);

        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * Test POST request without CSRF token returns 401
     */
    public function test_post_without_csrf_token_returns_401()
    {
        $request = Request::create('/test', 'POST');
        $next = function ($request) {
            return new Response('Success', 200);
        };

        $response = $this->middleware->handle($request, $next);

        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals('Unauthorized', $response->getContent());
    }

    /**
     * Test PUT request without CSRF token returns 401
     */
    public function test_put_without_csrf_token_returns_401()
    {
        $request = Request::create('/test', 'PUT');
        $next = function ($request) {
            return new Response('Success', 200);
        };

        $response = $this->middleware->handle($request, $next);

        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals('Unauthorized', $response->getContent());
    }

    /**
     * Test DELETE request without CSRF token returns 401
     */
    public function test_delete_without_csrf_token_returns_401()
    {
        $request = Request::create('/test', 'DELETE');
        $next = function ($request) {
            return new Response('Success', 200);
        };

        $response = $this->middleware->handle($request, $next);

        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals('Unauthorized', $response->getContent());
    }

    /**
     * Test that the middleware properly handles the next closure
     */
    public function test_calls_next_closure_for_safe_requests()
    {
        $request = Request::create('/test', 'GET');
        $nextCalled = false;

        $next = function ($request) use (&$nextCalled) {
            $nextCalled = true;
            return new Response('Next called', 200);
        };

        $response = $this->middleware->handle($request, $next);

        $this->assertTrue($nextCalled, 'Next closure should be called for GET requests');
        $this->assertEquals('Next called', $response->getContent());
    }
}
