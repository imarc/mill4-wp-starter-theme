<?php

namespace App\Hooks;

use App\Assets\Manifest;
use App\Hooks\Concerns\RegistersHooks;
use App\Hooks\Contracts\HooksInterface;

/**
 * AssetHooks handles the loading and management of theme assets in both development (HMR) and production environments.
 *
 * This class is responsible for:
 * - Loading Vite client in HMR mode
 * - Managing script and stylesheet loading in production
 * - Handling asset paths and manifest resolution
 * - Ensuring proper module type attributes for scripts
 */
class AssetHooks implements HooksInterface
{
    use RegistersHooks;

    private Manifest $manifest;

    private const VITE_HOST = 'http://localhost:5173';

    private const MANIFEST_PATH = 'dist/.vite/manifest.json';

    private const DIST_PATH = 'dist';

    /**
     * Initialize the AssetHooks with a new Manifest instance.
     */
    public function __construct()
    {
        $this->manifest = new Manifest(get_theme_file_path(self::MANIFEST_PATH));
    }

    /**
     * Initialize asset loading based on environment.
     * In HMR mode, loads Vite client and entry point.
     * In production, loads bundled assets from manifest.
     */
    public function initialize(): void
    {
        if (is_hmr()) {
            $this->addAction('wp_head', [$this, 'hmrHeadHook']);
        } else {
            $this->addAction('wp_head', function () {
                $this->loadScriptsForEntryPoint('resources/js/index.js');
            });
        }
    }

    /**
     * Loads necessary scripts for HMR development mode.
     * Includes Vite client and main entry point.
     */
    public function hmrHeadHook()
    {
        echo '<script type="module" crossorigin src="' . static::VITE_HOST . '/@vite/client"></script>';
        echo '<script type="module" crossorigin src="' . static::VITE_HOST . '/resources/js/index.js"></script>';
    }

    /**
     * Loads all assets for a given entry point.
     * This includes the main script, stylesheets, and imported modules.
     */
    public function loadScriptsForEntryPoint(string $script): void
    {
        $this->loadFileForEntryPoint($script);
        $this->loadStylesheetsForEntryPoint($script);
        $this->loadImportsForEntryPoint($script);
    }

    /**
     * Loads the main script file for an entry point in the
     * manifest.
     */
    private function loadFileForEntryPoint(string $entryPoint): void
    {
        $file = $this->manifest->getFileForEntryPoint($entryPoint);

        if (! $file) {
            return;
        }

        echo $this->buildScriptTag($file);
    }

    /**
     * Loads all stylesheets associated with an entry point in the
     * manifest.
     */
    private function loadStylesheetsForEntryPoint(string $entryPoint): void
    {
        $stylesheets = $this->manifest->getStylesheetsForEntryPoint($entryPoint);

        foreach ($stylesheets as $css) {
            echo $this->buildStylesheetTag($css);
        }
    }

    /**
     * Loads all imported modules for an entry point in the
     * manifest.
     */
    private function loadImportsForEntryPoint(string $entryPoint): void
    {
        $imports = $this->manifest->getImportsForEntryPoint($entryPoint);

        foreach ($imports as $import) {
            echo $this->buildScriptTag($import);
        }
    }

    /**
     * Builds a script tag with module type and crossorigin attributes.
     */
    private function buildScriptTag(string $file): string
    {
        return '<script type="module" crossorigin src="' . $this->getWebDistPath($file) . '"></script>';
    }

    /**
     * Builds a stylesheet link tag.
     */
    private function buildStylesheetTag(string $file): string
    {
        return '<link rel="stylesheet" href="' . $this->getWebDistPath($file) . '">';
    }

    /**
     * Gets the web-accessible URL for an asset in the dist directory.
     */
    private function getWebDistPath(?string $assetPath = null): string
    {
        $path = $assetPath ? self::DIST_PATH . '/' . $assetPath : self::DIST_PATH;

        return get_theme_file_uri($path);
    }
}
