<?php

namespace App\Hooks;

use App\Blocks;

class BlockHooks
{
    public function initialize(): void
    {
        add_action('init', [$this, 'registerBlocks']);
    }

    public function registerBlocks()
    {
        $registrar = new Blocks\Registrar();

        $blocks = [
            Blocks\GenericCtaBlock::class,
        ];

        foreach ($blocks as $block) {
            $registrar->register($block);
        }
    }
}
