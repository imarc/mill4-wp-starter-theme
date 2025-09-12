<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;

/**
 * Base test case for all Mill4 theme tests
 *
 * This class provides a foundation for testing with WordBless providing
 * WordPress functionality without database requirements.
 */
abstract class BaseTestCase extends TestCase
{
    /**
     * Set up the test environment before each test
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Add any common test setup here
        $this->setUpWordPressFunctions();
    }

    /**
     * Clean up after each test
     */
    protected function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * Set up common WordPress functions that might be used across tests
     *
     * WordBless provides a complete WordPress environment, so most functions
     * are available without mocking.
     */
    protected function setUpWordPressFunctions(): void
    {
        // WordBless provides WordPress functionality out of the box
        // Add any specific test setup here if needed
    }

    /**
     * Helper method to create a test user
     *
     * @param array $user_data User data array
     * @return int|\WP_Error User ID on success, WP_Error on failure
     */
    protected function createTestUser(array $user_data = []): int|\WP_Error
    {
        $defaults = [
            'user_login' => 'testuser_' . uniqid(),
            'user_email' => 'test@example.com',
            'user_pass' => 'password123',
            'role' => 'subscriber',
        ];

        $user_data = array_merge($defaults, $user_data);

        return wp_insert_user($user_data);
    }

    /**
     * Helper method to create a test post
     *
     * @param array $post_data Post data array
     * @return int|\WP_Error Post ID on success, WP_Error on failure
     */
    protected function createTestPost(array $post_data = []): int|\WP_Error
    {
        $defaults = [
            'post_title' => 'Test Post ' . uniqid(),
            'post_content' => 'This is test content.',
            'post_status' => 'publish',
            'post_type' => 'post',
        ];

        $post_data = array_merge($defaults, $post_data);

        return wp_insert_post($post_data);
    }
}
