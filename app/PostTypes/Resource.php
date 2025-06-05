<?php

/**********************************************/
/* This is an example of a custom post type.  */
/* Feel free to delete it!                    */
/**********************************************/

namespace App\PostTypes;

use Imarc\Millyard\Attributes\RegistersPostType;
use Imarc\Millyard\PostTypes\PostType;

#[RegistersPostType]
class Resource extends PostType
{
    public const SLUG = 'resources';

    public string $singularLabel = 'Resource';

    public string $pluralLabel = 'Resources';

    public string $path = 'resources';

    public array $args = [
        'menu_icon' => 'dashicons-format-aside',
    ];
}
