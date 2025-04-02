<?php

namespace App\Hooks;

use App\Assets\Contracts\AssetResolver;
use App\Hooks\Concerns\RegistersHooks;
use App\Hooks\Contracts\HooksInterface;

class AssetHooks implements HooksInterface
{
    use RegistersHooks;

    private const VITE_HOST = 'http://localhost:5173';

    public function __construct(private AssetResolver $assetResolver)
    {
    }

    public function initialize(): void
    {
        if (is_hmr()) {
            add_action('wp_head', [$this, 'hmrHeadHook']);
        }

        $this->addAction('wp_enqueue_scripts', function () {
            wp_enqueue_script('mill4', $this->assetResolver->resolve('resources/scripts/scripts.js'));
            wp_enqueue_style('mill4', $this->assetResolver->resolve('resources/styles/styles.scss'));
        });
    }

    public function hmrHeadHook()
    {
        echo '<script type="module" crossorigin src="' . static::VITE_HOST . '/@vite/client"></script>';
    }
}
