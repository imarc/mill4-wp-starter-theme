<?php

namespace App\Jobs;

class MyGreatJob extends Job
{
    public function handle(?string $foo = null): void
    {
        die('MyGreatJob ' . $foo);
    }
}
