<?php

namespace App\Providers;

use League\Container\ServiceProvider\AbstractServiceProvider;

class SiteServiceProvider extends AbstractServiceProvider
{
    public function provides(string $id): bool
    {
        $services = [
            'site',
        ];

        return in_array($id, $services, true);
    }

    public function register(): void
    {
        // $this->getContainer()->add('site', fn () => new \Timber\Site());
    }
}
