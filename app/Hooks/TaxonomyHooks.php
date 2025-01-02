<?php

namespace App\Hooks;

use App\Hooks\Contracts\HooksInterface;
use App\Taxonomies;

class TaxonomyHooks implements HooksInterface
{
    public function __construct(private Taxonomies\Registrar $taxonomies)
    {

    }

    public function initialize(): void
    {
        add_action('init', [$this, 'registerTaxonomies']);
    }

    public function registerTaxonomies(): void
    {
        $taxonomies = [
            Taxonomies\Genre::class,
        ];

        foreach ($taxonomies as $postType) {
            $this->taxonomies->register($postType);
        }
    }
}
