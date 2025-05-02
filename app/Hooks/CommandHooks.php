<?php

namespace App\Hooks;

use App\Attributes\RegistersCommand;
use App\Commands\Mill4Command;
use App\Hooks\Concerns\DiscoversClasses;
use App\Hooks\Concerns\RegistersHooks;
use App\Hooks\Contracts\HooksInterface;

class CommandHooks implements HooksInterface
{
    use DiscoversClasses;
    use RegistersHooks;

    public function initialize(): void
    {
        $this->addAction('init', [$this, 'registerCommands']);
    }

    public function registerCommands(): void
    {
        $classes = $this->discoverClassesForAttribute(RegistersCommand::class, 'Commands');

        foreach ($classes as $commandClass) {
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
