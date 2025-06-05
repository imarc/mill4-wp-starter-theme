<?php

namespace App\Jobs;

use Imarc\Millyard\Attributes\RegistersJob;
use Imarc\Millyard\Jobs\Job;

#[RegistersJob]
class MyGreatJob extends Job
{
    public function handle(?string $foo = null): void
    {
        error_log('MyGreatJob ' . $foo);
        die('MyGreatJob ' . $foo);
    }
}
