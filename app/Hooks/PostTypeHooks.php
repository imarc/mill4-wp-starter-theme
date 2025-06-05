<?php

namespace App\Hooks;

use Imarc\Millyard\Attributes\RegistersPostType;
use Imarc\Millyard\Concerns\DiscoversClasses;
use Imarc\Millyard\Concerns\RegistersHooks;
use Imarc\Millyard\Contracts\HooksInterface;

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
