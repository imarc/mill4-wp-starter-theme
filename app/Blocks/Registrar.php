<?php

namespace App\Blocks;

class Registrar
{
    public function register(string $blockClass): void
    {
        $block = new $blockClass();

        if (! method_exists($block, 'register')) {
            throw new \RuntimeException(sprintf('Could not register class %s. register() does not exist', $blockClass));
        }

        $block->register();
    }
}
