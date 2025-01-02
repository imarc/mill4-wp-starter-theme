<?php

namespace App\Assets;

use App\Assets\Contracts\AssetResolver;

class BuildResolver implements AssetResolver
{
    private string $manifestPath = 'dist/.vite/manifest.json';

    private string $distPath = 'dist';

    private array $manifest = [];

    public function __construct()
    {
        $this->loadManifest();
    }

    private function getFullManifestPath(): string
    {
        return get_theme_file_path($this->manifestPath);
    }

    private function getWebDistPath(?string $assetPath = null): string
    {
        $path = $assetPath ? $this->distPath . '/' . $assetPath : $this->distPath;

        return get_theme_file_uri($path);
    }

    public function loadManifest(): void
    {
        $fullManifestPath = $this->getFullManifestPath();

        if (! file_exists($fullManifestPath)) {
            wp_die('Manifest file does not exist. Run <code>npm run build</code> in your theme root!');
        }

        $manifest = file_get_contents($fullManifestPath);

        $this->manifest = json_decode($manifest, true);
    }

    public function resolve(string $asset): ?string
    {
        if (! ($this->manifest[$asset]['file'] ?? false)) {
            die('Asset not found in manifest. Run <code>npm run build</code> in your theme root!');
        }


        return $this->getWebDistPath($this->manifest[$asset]['file']);
    }
}
