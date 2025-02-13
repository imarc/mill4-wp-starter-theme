<?php

namespace App\Http\Controllers;

use Symfony\Component\HttpFoundation\Request;

class FooController extends Controller
{
    public function __invoke(Request $request)
    {
        $this->render('foo.twig', ['message' => 'Hello from FooController!']);
    }
}
