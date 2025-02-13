<?php

namespace App\Commands;

class FooCommand extends Command
{
    protected string $name = 'foo';

    protected string $shortDescription = 'Print a message to the console';

    public function __invoke($args, $assoc_args)
    {
        $this->line('Hello from FooCommand!');
    }
}
