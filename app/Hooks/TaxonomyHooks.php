<?php

namespace App\Hooks;

use Imarc\Millyard\Concerns\RegistersHooks;
use Imarc\Millyard\Contracts\HooksInterface;
use Imarc\Millyard\Taxonomies\Registrar;

class TaxonomyHooks implements HooksInterface
{
    use RegistersHooks;

    public function __construct(private Registrar $registrar)
    {
    }

    public function initialize(): void
    {
        $this->addAction('init', [$this, 'registerTaxonomies']);
    }

    public function registerTaxonomies(): void
    {
        $this->registrar->registerTaxonomies();
    }
}
