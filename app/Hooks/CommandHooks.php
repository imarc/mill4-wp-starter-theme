<?php

namespace App\Hooks;

use App\Commands\Mill4Command;
use App\Hooks\Contracts\HooksInterface;

class CommandHooks implements HooksInterface
{
    public const COMMANDS = [
        Mill4Command::class,
    ];

    public function initialize(): void
    {
        add_action('init', [$this, 'registerCommands']);
    }

    public function registerCommands(): void
    {
        foreach (self::COMMANDS as $commandClass) {
            $command = new $commandClass();

            if (! (defined('WP_CLI') && constant('WP_CLI'))) {
                return;
            }

            \WP_CLI::add_command($command->name, $command, [
                'shortdesc' => $command->shortDescription,
                'longdesc' => $command->longDescription,
                'when' => $command->when,
            ]);

            do_action('mill4_command_registered', $commandClass);
        }
    }
}
