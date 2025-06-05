<?php

namespace App\Hooks;

use Imarc\Millyard\Attributes\RegistersBlock;
use Imarc\Millyard\Concerns\DiscoversClasses;
use Imarc\Millyard\Concerns\RegistersHooks;
use Imarc\Millyard\Contracts\HooksInterface;

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
