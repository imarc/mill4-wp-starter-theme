<?php

namespace App\Commands;

use Imarc\Millyard\Attributes\RegistersCommand;
use Imarc\Millyard\Commands\Command;

#[RegistersCommand]
class TestCommand extends Command
{
    public string $name = 'test';

    public string $shortDescription = 'Test commands';

    /**
     * @subcommand hello
     */
    public function hello($args, $assoc_args)
    {
        $this->line('Hello, world!');
    }
}
