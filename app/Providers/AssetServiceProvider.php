<?php

namespace App\Providers;

use App\Assets;
use App\Assets\Contracts\AssetResolver;
use League\Container\ServiceProvider\AbstractServiceProvider;

class AssetServiceProvider extends AbstractServiceProvider
{
    public function provides(string $id): bool
    {
        $services = [
            AssetResolver::class,
        ];

        return in_array($id, $services, true);
    }

    public function register(): void
    {
        $this->getContainer()->add(AssetResolver::class, function () {
            if (is_hmr()) {
                return new Assets\HotModuleResolver();
            }

            return new Assets\BuildResolver();
        });
    }
}
