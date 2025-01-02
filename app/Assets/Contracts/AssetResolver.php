<?php

namespace App\Assets\Contracts;

interface AssetResolver
{
    public function resolve(string $asset): ?string;
}
