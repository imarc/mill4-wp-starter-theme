<?php

namespace App\Hooks;

use App\Attributes\RegistersAdminPage;
use App\Hooks\Contracts\HooksInterface;
use App\Hooks\Concerns\DiscoversClasses;
use App\Hooks\Concerns\RegistersHooks;

class AdminPageHooks implements HooksInterface
{
    use DiscoversClasses;
    use RegistersHooks;

    public function initialize(): void
    {
        $this->registerAdminPages();
    }

    private function registerAdminPages(): void
    {
        $adminPageClasses = $this->discoverClassesForAttribute(RegistersAdminPage::class, 'AdminPages');

        $this->addAction('admin_menu', function () use ($adminPageClasses) {
            foreach ($adminPageClasses as $adminPage) {
                $adminPage = new $adminPage();
                $adminPage->register();
            }
        });
    }
}
