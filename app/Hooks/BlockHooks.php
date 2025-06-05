<?php

namespace App\Hooks;

use Imarc\Millyard\Blocks\Registrar;
use Imarc\Millyard\Concerns\RegistersHooks;
use Imarc\Millyard\Contracts\HooksInterface;

class BlockHooks implements HooksInterface
{
    use RegistersHooks;

    public function __construct(private Registrar $registrar)
    {
    }

    public function initialize(): void
    {
        $this->addAction('init', [$this, 'registerBlocks']);
    }

    public function registerBlocks()
    {
        $this->registrar->registerBlocks();
    }
}
