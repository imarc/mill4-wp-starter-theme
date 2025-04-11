<?php

namespace App\Hooks;

use App\AdminPages\LogViewer;
use App\Hooks\Contracts\HooksInterface;
use App\Hooks\Concerns\RegistersHooks;

class AdminPageHooks implements HooksInterface
{
    use RegistersHooks;

    public const ADMIN_PAGES = [
        LogViewer::class,
    ];

    public function initialize(): void
    {
        $this->registerAdminPages();
    }

    private function registerAdminPages(): void
    {
        $this->addAction('admin_menu', function () {
            foreach (self::ADMIN_PAGES as $adminPage) {
                $adminPage = new $adminPage();
                $adminPage->register();
            }
        });
    }
}
