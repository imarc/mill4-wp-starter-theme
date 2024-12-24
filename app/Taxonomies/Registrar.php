<?php

namespace App\Taxonomies;

class Registrar
{
    public function register(string $taxonomyClass): void
    {
        $taxonomy = new $taxonomyClass();

        if (! method_exists($taxonomy, 'register')) {
            throw new \RuntimeException(sprintf('Could not register class %s. register() does not exist', $taxonomyClass));
        }

        $taxonomy->register();
    }
}
