<?php

namespace App\Hooks;

use App\Blocks;
use App\Hooks\Contracts\HooksInterface;

class BlockHooks implements HooksInterface
{
    public const BLOCKS = [
        Blocks\CallToActionSection::class,
        Blocks\CarouselHeroSection::class,
        Blocks\HeroSection::class,
        Blocks\TestimonialBlock::class,
        Blocks\TestimonialCarouselSection::class,
    ];

    public function initialize(): void
    {
        add_action('init', [$this, 'registerBlocks']);
    }

    public function registerBlocks()
    {
        foreach (self::BLOCKS as $blockClass) {
            $block = new $blockClass();

            if (! method_exists($block, 'register')) {
                throw new \RuntimeException(sprintf('Could not register class %s. register() does not exist', $blockClass));
            }

            $block->register();
            do_action('mill4_block_registered', $blockClass);
        }
    }
}
