<?php

namespace App\Hooks;

use App\Attributes\RegistersBlock;
use App\Hooks\Concerns\DiscoversClasses;
use App\Hooks\Concerns\RegistersHooks;
use App\Hooks\Contracts\HooksInterface;

class BlockHooks implements HooksInterface
{
    use DiscoversClasses;
    use RegistersHooks;

    public function initialize(): void
    {
        $this->addAction('init', [$this, 'registerBlocks']);
    }

    public function registerBlocks()
    {
        $blockClasses = $this->discoverClassesForAttribute(RegistersBlock::class, 'Blocks');

        foreach ($blockClasses as $blockClass) {
            $block = new $blockClass();

            if (! method_exists($block, 'register')) {
                throw new \RuntimeException(sprintf('Could not register class %s. register() does not exist', $blockClass));
            }

            $block->register();
            do_action('mill4_block_registered', $blockClass);
        }
    }
}
