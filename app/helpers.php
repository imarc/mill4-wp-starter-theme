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
    $response = new JsonResponse($data, $status, $headers);

    return $response->send();
}
