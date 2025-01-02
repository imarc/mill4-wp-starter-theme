<?php

namespace App\Assets;

use App\Assets\Contracts\AssetResolver;

class HotModuleResolver implements AssetResolver
{
    private string $host = 'http://localhost:5173';

    public function __construct()
    {

    }

    protected function getClient()
    {
        return $this->host . '/@vite/client';
    }

    public function resolve(string $asset): ?string
    {
        return $this->host . '/' . $asset;
    }
}
