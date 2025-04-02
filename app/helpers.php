<?php

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

function is_hmr(): bool
{
    // make sure we're in development and a .hot file exists in the theme folder.
    return wp_get_environment_type() === 'development' && file_exists(get_theme_file_path('.hot'));
}

function response(string $content, int $status = 200, array $headers = []): Response
{
    $response = new Response($content, $status, $headers);

    return $response->send();
}

function json_response(array $data, int $status = 200, array $headers = []): JsonResponse
{
    return new JsonResponse($data, $status, $headers);
}

function csrf_token_key(): string
{
    $key = 'ajax_nonce';

    if (config('sessions.enabled')) {
        $sessionId = session_id();
        $key .= '_' . $sessionId;
    }

    return $key;
}

function csrf_token(): string
{
    $key = csrf_token_key();

    return wp_create_nonce($key);
}

function config(string $key, $default = null)
{
    $key = explode('.', $key);
    $config = require get_theme_file_path('app/config.php');

    foreach ($key as $k) {
        $config = $config[$k] ?? $default;
    }

    return $config;
}

function env(string $key, $default = null)
{
    $value = $_ENV[$key] ?? $default;

    if ($value === 'true') {
        return true;
    }

    if ($value === 'false') {
        return false;
    }

    return $value;
}
