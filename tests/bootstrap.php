<?php

/**
 * PHPUnit bootstrap file for Mill4 theme tests
 *
 * This bootstrap uses WordBless to provide a WordPress environment
 * without requiring a real database, ensuring test isolation.
 */

// Load Composer autoloader
require_once dirname(__DIR__) . '/vendor/autoload.php';

// Set up test-specific database constants BEFORE WordBless loads
// This ensures we don't accidentally connect to the real database
define('DB_NAME', 'test_database_' . uniqid());
define('DB_USER', 'test_user');
define('DB_PASSWORD', 'test_password');
define('DB_HOST', 'localhost');
define('DB_CHARSET', 'utf8');
define('DB_COLLATE', '');

// Initialize WordBless - this provides WordPress functionality without database
// This creates an isolated, in-memory WordPress environment for each test run
\WorDBless\Load::load();

// Set up test-specific WordPress constants
define('WP_TESTS_DOMAIN', 'test.example.org');
define('WP_TESTS_EMAIL', 'admin@test.example.org');
define('WP_TESTS_TITLE', 'Test Blog');

// Load theme helpers safely (skip bootstrap to avoid config loading issues in test environment)
if (file_exists(dirname(__DIR__) . '/app/helpers.php')) {
    require_once dirname(__DIR__) . '/app/helpers.php';
}

// For tests, we'll manually initialize what we need instead of running the full bootstrap
