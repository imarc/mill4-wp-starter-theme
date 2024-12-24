<?php

/**********************************************/
/* This is an example of a custom post type.  */
/* Feel free to delete it!                    */
/**********************************************/

namespace App\PostTypes;

class Movie extends PostType
{
    public const SLUG = 'movie';

    public string $singularLabel = 'Movie';

    public string $pluralLabel = 'Movies';
}
