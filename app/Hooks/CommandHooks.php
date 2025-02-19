<?php

namespace App\Hooks;

use App\Commands\FooCommand;
use App\Hooks\Contracts\HooksInterface;

class CommandHooks implements HooksInterface
{
    public const COMMANDS = [
        FooCommand::class,
    ];

    public function initialize(): void
    {
        add_action('init', [$this, 'registerCommands']);
    }

    public function registerCommands(): void
    {
        foreach (self::COMMANDS as $commandClass) {
            $command = new $commandClass();

            if (! method_exists($command, 'register')) {
                throw new \RuntimeException(sprintf('Could not register class %s. register() does not exist', $commandClass));
            }

            $command->register();

            do_action('mill4_command_registered', $commandClass);
        }
    }
}
