<?php

namespace App\Taxonomies;

use App\PostTypes\Movie;

class Genre extends Taxonomy
{
    public const SLUG = 'genre';

    public string $pluralLabel = 'Genres';

    public string $singularLabel = 'Genre';

    protected array $postTypes = [
        Movie::SLUG,
    ];

    protected bool $registersTopLevelMenuItem = false;

    // protected ?string $menuItemName = 'Genres';

    // protected ?string $menuItemIcon = 'dashicons-admin-generic';

    // protected ?int $menuItemPosition = 6;
}
