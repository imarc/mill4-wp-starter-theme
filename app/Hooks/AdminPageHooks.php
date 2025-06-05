<?php

namespace App\Hooks;

use Imarc\Millyard\Attributes\RegistersAdminPage;
use Imarc\Millyard\Contracts\HooksInterface;
use Imarc\Millyard\Concerns\DiscoversClasses;
use Imarc\Millyard\Concerns\RegistersHooks;

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
