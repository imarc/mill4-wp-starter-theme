<?php

namespace App\Hooks;

use App\Commands\FooCommand;
use App\Commands\Registrar;
use App\Hooks\Contracts\HooksInterface;

class CommandHooks implements HooksInterface
{
    public function __construct(private Registrar $commands)
    {

    }

    public function initialize(): void
    {
        add_action('init', [$this, 'registerCommands']);
    }

    public function registerCommands(): void
    {
        $commands = [
            FooCommand::class,
        ];

        foreach ($commands as $command) {
            $this->commands->register($command);
        }
    }
}
