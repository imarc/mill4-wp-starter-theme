<?php

namespace App\Hooks;

use Imarc\Millyard\Concerns\RegistersHooks;
use Imarc\Millyard\Contracts\HooksInterface;
use Imarc\Millyard\PostTypes\Registrar;

class PostTypeHooks implements HooksInterface
{
    use RegistersHooks;

    public function __construct(private Registrar $registrar)
    {
    }

    public function initialize(): void
    {
        $this->addAction('init', [$this, 'registerPostTypes']);
    }

    public function registerPostTypes(): void
    {
        $this->registrar->registerPostTypes();
    }
}
