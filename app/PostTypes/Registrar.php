<?php

namespace App\PostTypes;

class Registrar
{
    public function register(string $postTypeClass): void
    {
        $postType = new $postTypeClass();

        if (! method_exists($postType, 'register')) {
            throw new \RuntimeException(sprintf('Could not register class %s. register() does not exist', $postTypeClass));
        }

        $postType->register();
    }
}
