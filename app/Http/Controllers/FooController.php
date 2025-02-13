<?php

namespace App\Http\Controllers;

use App\Services\MyService;

class FooController extends Controller
{
    public function __invoke(MyService $service)
    {
        $this->render('foo.twig', ['message' => 'Hello from FooController!']);
    }
}
