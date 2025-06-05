<?php

namespace App\Hooks;

use Imarc\Millyard\Attributes\RegistersTaxonomy;
use Imarc\Millyard\Concerns\DiscoversClasses;
use Imarc\Millyard\Concerns\RegistersHooks;
use Imarc\Millyard\Contracts\HooksInterface;

class TaxonomyHooks implements HooksInterface
{
    use DiscoversClasses;
    use RegistersHooks;

    public function initialize(): void
    {
        $this->addAction('init', [$this, 'registerTaxonomies']);
    }

    public function registerTaxonomies(): void
    {
        $classes = $this->discoverClassesForAttribute(RegistersTaxonomy::class, 'Taxonomies');

        foreach ($classes as $taxonomyClass) {
            $taxonomy = new $taxonomyClass();

            if (! method_exists($taxonomy, 'register')) {
                throw new \RuntimeException(sprintf('Could not register class %s. register() does not exist', $taxonomyClass));
            }

            $taxonomy->register();

            do_action('mill4_taxonomy_registered', $taxonomyClass);
        }
    }
}
