<?php

namespace App\Http\Controllers;

use Imarc\Millyard\Http\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class FooController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        return json_response(['message' => 'Hello from FooController!']);
    }
}
