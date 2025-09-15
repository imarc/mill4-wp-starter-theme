<?php

namespace App\Hooks;

use Imarc\Millyard\AdminPages\Registrar;
use Imarc\Millyard\Concerns\RegistersHooks;
use Imarc\Millyard\Contracts\HooksInterface;

class AdminPageHooks implements HooksInterface
{
    use RegistersHooks;

    public function __construct(private Registrar $registrar)
    {
    }

    public function initialize(): void
    {
        $this->addAction('init', [$this, 'registerAdminPages']);
    }

    public function registerAdminPages(): void
    {
        $this->registrar->registerAdminPages();
    }
}
