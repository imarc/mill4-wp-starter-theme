<?php

namespace App\Hooks;

use App\PostTypes;

class PostTypeHooks
{
    public function __construct(private PostTypes\Registrar $postTypes)
    {

    }

    public function initialize(): void
    {
        add_action('init', [$this, 'registerPostTypes']);
    }

    public function registerPostTypes(): void
    {
        $postTypes = [
            PostTypes\Movie::class,
        ];

        foreach ($postTypes as $postType) {
            $this->postTypes->register($postType);
        }
    }
}
