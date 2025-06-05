<?php

namespace App\Http\Controllers\API;

use Imarc\Millyard\Http\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class CsrfTokenAction extends Controller
{
    public function __invoke(): JsonResponse
    {
        return json_response([
            'csrf_token' => csrf_token(),
        ]);
    }
}
