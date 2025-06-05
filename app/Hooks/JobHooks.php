<?php

namespace App\Hooks;

use Imarc\Millyard\Concerns\RegistersHooks;
use Imarc\Millyard\Contracts\HooksInterface;
use Imarc\Millyard\Jobs\Registrar;
use Imarc\Millyard\Services\Container;

class JobHooks implements HooksInterface
{
    use RegistersHooks;

    public function __construct(
        private Container $container,
        private Registrar $registrar
    ) {
    }

    public function initialize(): void
    {
        $this->addAction('init', [$this, 'registerJobs']);
    }

    public function registerJobs()
    {
        $this->registrar->registerJobs();
    }
}
