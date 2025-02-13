<?php

namespace App\Commands;

class Registrar
{
    public function register(string $commandClass): void
    {
        $command = new $commandClass();

        if (! method_exists($command, 'register')) {
            throw new \RuntimeException(sprintf('Could not register class %s. register() does not exist', $commandClass));
        }

        $command->register();
    }
}
