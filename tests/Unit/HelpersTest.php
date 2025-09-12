<?php

namespace App\Tests\Unit;

use App\Tests\BaseTestCase;

/**
 * Unit tests for theme helper functions
 */
class HelpersTest extends BaseTestCase
{
    /**
     * Test that helper functions are properly loaded
     */
    public function test_helper_functions_exist()
    {
        // Test that actual helper functions from the theme are available
        $this->assertTrue(function_exists('is_hmr'), 'is_hmr() helper should exist');
        $this->assertTrue(function_exists('response'), 'response() helper should exist');
        $this->assertTrue(function_exists('json_response'), 'json_response() helper should exist');
        $this->assertTrue(function_exists('config'), 'config() helper should exist');
        $this->assertTrue(function_exists('env'), 'env() helper should exist');
        $this->assertTrue(function_exists('cache'), 'cache() helper should exist');
    }

    /**
     * Test that we can create container instances when needed
     */
    public function test_container_can_be_instantiated()
    {
        // Test that we can create container instances directly (as the theme does)
        $container = new \Imarc\Millyard\Services\Container();

        $this->assertNotNull($container, 'Container should be instantiable');
        $this->assertInstanceOf('Imarc\Millyard\Services\Container', $container);
    }

    /**
     * Test WordPress functions work in helper context
     */
    public function test_wordpress_functions_in_helpers()
    {
        // Test that WordPress functions are available for use in helpers
        $this->assertTrue(function_exists('get_option'), 'get_option should be available');
        $this->assertTrue(function_exists('wp_cache_get'), 'wp_cache_get should be available');
        $this->assertTrue(function_exists('wp_cache_set'), 'wp_cache_set should be available');

        // Test actual WordPress functionality
        $test_option = 'test_option_' . uniqid();
        update_option($test_option, 'test_value');
        $this->assertEquals('test_value', get_option($test_option));
    }
}
