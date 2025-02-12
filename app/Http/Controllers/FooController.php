<?php

namespace App\Http\Controllers;

class FooController extends Controller
{
    public function __invoke()
    {
        $this->render('foo.twig', ['message' => 'Hello from FooController!']);

        exit();
    }
}
