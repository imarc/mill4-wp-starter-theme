<?php

namespace App\Hooks;

use Imarc\Millyard\Commands\MillyardCommand;
use Imarc\Millyard\Commands\Registrar;
use Imarc\Millyard\Concerns\RegistersHooks;
use Imarc\Millyard\Contracts\HooksInterface;

class CommandHooks implements HooksInterface
{
    use RegistersHooks;

    public function __construct(private Registrar $registrar)
    {
    }

    public function initialize(): void
    {
        $this->addAction('init', [$this, 'registerCommands']);
    }

    public function registerCommands(): void
    {
        $this->registrar->registerCommand(MillyardCommand::class);
        $this->registrar->registerCommands();
    }
}
