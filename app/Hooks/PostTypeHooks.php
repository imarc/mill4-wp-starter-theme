<?php

namespace App\Hooks;

use App\Attributes\RegistersPostType;
use App\Hooks\Concerns\RegistersHooks;
use App\Hooks\Concerns\DiscoversClasses;
use App\Hooks\Contracts\HooksInterface;

class PostTypeHooks implements HooksInterface
{
    use DiscoversClasses;
    use RegistersHooks;

    public function initialize(): void
    {
        $this->addAction('init', [$this, 'registerPostTypes']);
    }

    public function registerPostTypes(): void
    {
        $classes = $this->discoverClassesForAttribute(RegistersPostType::class, 'PostTypes');

        foreach ($classes as $postTypeClass) {
            $postType = new $postTypeClass();

            if (! method_exists($postType, 'register')) {
                throw new \RuntimeException(sprintf('Could not register class %s. register() does not exist', $postTypeClass));
            }

            $postType->register();

            do_action('mill4_post_type_registered', $postTypeClass);
        }
    }
}
