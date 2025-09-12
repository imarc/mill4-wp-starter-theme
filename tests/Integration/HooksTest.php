<?php

namespace App\Tests\Integration;

use App\Tests\BaseTestCase;

/**
 * Integration tests for WordPress hooks and actions
 */
class HooksTest extends BaseTestCase
{
    /**
     * Test that WordPress hook system works
     */
    public function test_wordpress_hooks_system()
    {
        // Test that we can add and execute actions
        $test_value = '';

        add_action('test_action', function () use (&$test_value) {
            $test_value = 'action_executed';
        });

        do_action('test_action');

        $this->assertEquals('action_executed', $test_value, 'Action should be executed');
    }

    /**
     * Test WordPress filter integration
     */
    public function test_wordpress_filters()
    {
        // Test that we can add and apply filters
        add_filter('test_filter', function ($value) {
            return $value . '_filtered';
        });

        $result = apply_filters('test_filter', 'original');

        $this->assertEquals('original_filtered', $result, 'Filter should modify the value');
    }

    /**
     * Test that custom post types can be registered
     */
    public function test_custom_post_type_registration()
    {
        // Test registering a custom post type
        $result = register_post_type('test_post_type', [
            'public' => true,
            'label' => 'Test Posts'
        ]);

        $this->assertInstanceOf('WP_Post_Type', $result, 'Post type should be registered successfully');

        // Test that the post type exists
        $this->assertTrue(post_type_exists('test_post_type'), 'Post type should exist after registration');
    }

    /**
     * Test theme-specific functionality integration
     */
    public function test_theme_integration()
    {
        // Test that theme helpers work with WordPress
        $this->assertTrue(function_exists('is_hmr'), 'Theme helper should be available');

        // Test that we can create the container (as the theme does in bootstrap.php)
        $container = new \Imarc\Millyard\Services\Container();
        $this->assertInstanceOf('Imarc\Millyard\Services\Container', $container);

        // You can add more specific integration tests here
        // For example, testing that your theme hooks properly integrate with WordPress
    }
}
