<?php

namespace App\Jobs;

class MyGreatJob extends Job
{
    public function handle(?string $foo = null): void
    {
        error_log('MyGreatJob ' . $foo);
        die('MyGreatJob ' . $foo);
    }
}
