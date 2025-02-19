<?php

namespace App\Hooks;

use App\Hooks\Contracts\HooksInterface;
use App\PostTypes;

class PostTypeHooks implements HooksInterface
{
    public const POST_TYPES = [
        PostTypes\Movie::class,
    ];

    public function initialize(): void
    {
        add_action('init', [$this, 'registerPostTypes']);
    }

    public function registerPostTypes(): void
    {
        foreach (self::POST_TYPES as $postTypeClass) {
            $postType = new $postTypeClass();

            if (! method_exists($postType, 'register')) {
                throw new \RuntimeException(sprintf('Could not register class %s. register() does not exist', $postTypeClass));
            }

            $postType->register();

            do_action('mill4_post_type_registered', $postTypeClass);
        }
    }
}
