<?php

namespace App\Taxonomies;

use App\Attributes\RegistersTaxonomy;
use App\PostTypes\Resource;

#[RegistersTaxonomy]
class ResourceType extends Taxonomy
{
    public const SLUG = 'resource_type';

    public string $pluralLabel = 'Resource Types';

    public string $singularLabel = 'Resource Type';

    protected array $postTypes = [
        Resource::SLUG,
    ];

    protected bool $registersTopLevelMenuItem = false;

    // protected ?string $menuItemName = 'Resource Types';

    // protected ?string $menuItemIcon = 'dashicons-admin-generic';

    // protected ?int $menuItemPosition = 6;
}
