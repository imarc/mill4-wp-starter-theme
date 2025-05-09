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
        $this->addFilter('script_loader_tag', [$this, 'addTypeToMill4Scripts'], 10, 3);

        $this->addAction('wp_enqueue_scripts', function () {
            wp_enqueue_script('mill4', $this->assetResolver->resolve('resources/js/index.js'));
        });
    }

    public function hmrHeadHook()
    {
        echo '<script type="module" crossorigin src="' . static::VITE_HOST . '/@vite/client"></script>';
    }

    public function addTypeToMill4Scripts($tag, $handle, $src)
    {
        // if not your script, do nothing and return original $tag
        if ($handle !== 'mill4') {
            return $tag;
        }
        // change the script tag by adding type="module" and return it.
        $tag = '<script type="module" src="' . esc_url($src) . '"></script>';
        return $tag;
    }
}
