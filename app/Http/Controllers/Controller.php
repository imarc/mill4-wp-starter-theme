<?php

namespace App\Http\Controllers;

use Timber\Timber;

class Controller
{
    protected function render($template, $data = [])
    {
        Timber::render($template, $data);
        exit;
    }
}
