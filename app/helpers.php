<?php

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Check if the current environment is development and a .hot file exists in the theme folder.
 *
 * @return bool True if the environment is development and the .hot file exists, false otherwise.
 */
function is_hmr(): bool
{
    return wp_get_environment_type() === 'development'
        && file_exists(get_theme_file_path('.hot'));
}

/**
 * Send a response using the Symfony Response class.
 *
 * @param string $content The content of the response.
 * @param int $status The status code of the response.
 * @param array $headers The headers of the response.
 */
function response(string $content, int $status = 200, array $headers = []): Response
{
    $response = new Response($content, $status, $headers);

    return $response->send();
}

/**
 * Send a JSON response using the Symfony JsonResponse class.
 *
 * @param array $data The data to send in the response.
 * @param int $status The status code of the response.
 * @param array $headers The headers of the response.
 */
function json_response(array $data, int $status = 200, array $headers = []): JsonResponse
{
    return new JsonResponse($data, $status, $headers);
}

/**
 * Get the CSRF token key.
 *
 * @return string The CSRF token key.
 */
function csrf_token_key(): string
{
    $key = 'ajax_nonce';

    if (config('sessions.enabled')) {
        $sessionId = session_id();
        $key .= '_' . $sessionId;
    }

    return $key;
}

/**
 * Get the CSRF token.
 *
 * @return string The CSRF token.
 */
function csrf_token(): string
{
    $key = csrf_token_key();

    return wp_create_nonce($key);
}

/**
 * Get a configuration value.
 *
 * @param string $key The key to get the configuration value for.
 * @param mixed $default The default value to return if the key is not found.
 * @return mixed The configuration value.
 */
function config(string $key, $default = null)
{
    $key = explode('.', $key);
    $config = require get_theme_file_path('app/config.php');

    foreach ($key as $k) {
        $config = $config[$k] ?? $default;
    }

    return $config;
}

/**
 * Get an environment variable.
 *
 * @param string $key The key to get the environment variable for.
 * @param mixed $default The default value to return if the key is not found.
 * @return mixed The environment variable value.
 */
function env(string $key, $default = null)
{
    $value = $_ENV[$key] ?? $default;

    if ($value === 'true') {
        return true;
    }

    if ($value === 'false') {
        return false;
    }

    if (is_numeric($value)) {
        if (str_contains($value, '.')) {
            return (float) $value;
        }

        return (int) $value;
    }

    return $value;
}

/**
 * Log the execution time of a function.
 *
 * @param string $name The name of the function to log.
 * @param callable $callback The function to log the execution time of.
 * @return mixed The result of the function.
 */
function function_timer(string $name, callable $callback)
{
    $start = microtime(true);
    $result = $callback();
    $end = microtime(true);

    error_log(sprintf('%s took %s seconds to execute', $name, $end - $start));

    return $result;
}
