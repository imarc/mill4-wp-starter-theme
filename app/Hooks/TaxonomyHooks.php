<?php

namespace App\Hooks;

use App\Hooks\Contracts\HooksInterface;
use App\Taxonomies;

class TaxonomyHooks implements HooksInterface
{
    public const TAXONOMIES = [
        Taxonomies\Genre::class,
    ];

    public function initialize(): void
    {
        add_action('init', [$this, 'registerTaxonomies']);
    }

    public function registerTaxonomies(): void
    {
        foreach (self::TAXONOMIES as $taxonomyClass) {
            $taxonomy = new $taxonomyClass();

            if (! method_exists($taxonomy, 'register')) {
                throw new \RuntimeException(sprintf('Could not register class %s. register() does not exist', $taxonomyClass));
            }

            $taxonomy->register();

            do_action('mill4_taxonomy_registered', $taxonomyClass);
        }
    }
}
