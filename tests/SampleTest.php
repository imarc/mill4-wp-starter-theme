<?php

namespace App\Tests;

/**
 * Sample test class to demonstrate the test setup with WordBless
 */
class SampleTest extends BaseTestCase
{
    /**
     * Test that the test environment is working
     */
    public function test_environment_setup()
    {
        $this->assertTrue(true, 'Test environment should be working');
        $this->assertTrue(function_exists('wp_insert_post'), 'WordPress functions should be available');
    }

    /**
     * Test WordPress functionality with WordBless
     */
    public function test_wordpress_functions_available()
    {
        // Test that core WordPress functions are available
        $this->assertTrue(function_exists('get_option'), 'get_option should be available');
        $this->assertTrue(function_exists('wp_insert_post'), 'wp_insert_post should be available');
        $this->assertTrue(function_exists('add_action'), 'add_action should be available');
        $this->assertTrue(function_exists('apply_filters'), 'apply_filters should be available');
    }

    /**
     * Test WordPress database operations (using in-memory database)
     */
    public function test_wordpress_database_operations()
    {
        // Test creating a post
        $post_id = $this->createTestPost([
            'post_title' => 'Test Post for Database',
            'post_content' => 'This is test content for database operations.',
        ]);

        $this->assertIsInt($post_id, 'Post creation should return an integer ID');
        $this->assertGreaterThan(0, $post_id, 'Post ID should be greater than 0');

        // Test retrieving the post
        $post = get_post($post_id);
        $this->assertNotNull($post, 'Post should be retrievable');
        $this->assertEquals('Test Post for Database', $post->post_title);
    }

    /**
     * Test WordPress user operations (using in-memory database)
     */
    public function test_wordpress_user_operations()
    {
        // Test creating a user
        $user_id = $this->createTestUser([
            'user_login' => 'testuser123',
            'user_email' => 'testuser123@example.com',
        ]);

        $this->assertIsInt($user_id, 'User creation should return an integer ID');
        $this->assertGreaterThan(0, $user_id, 'User ID should be greater than 0');

        // Test retrieving the user
        $user = get_user_by('id', $user_id);
        $this->assertNotFalse($user, 'User should be retrievable');
        $this->assertEquals('testuser123', $user->user_login);
    }

    /**
     * Test that we can test theme-specific functionality
     */
    public function test_theme_helpers_exist()
    {
        // Check if actual helper functions from the theme are loaded
        $this->assertTrue(function_exists('is_hmr'), 'is_hmr() helper should be available');
        $this->assertTrue(function_exists('response'), 'response() helper should be available');
        $this->assertTrue(function_exists('json_response'), 'json_response() helper should be available');

        // You can add more specific tests for your theme's functionality here
    }

    /**
     * Test WordPress options functionality
     */
    public function test_wordpress_options()
    {
        // Test setting and getting options
        $option_name = 'test_option_' . uniqid();
        $option_value = 'test_value_' . uniqid();

        $result = update_option($option_name, $option_value);
        $this->assertTrue($result, 'Option should be updated successfully');

        $retrieved_value = get_option($option_name);
        $this->assertEquals($option_value, $retrieved_value, 'Retrieved option should match set value');
    }
}
