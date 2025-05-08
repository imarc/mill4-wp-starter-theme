<?php

namespace App\ViewComposers;

use App\Attributes\RegistersViewComposer;
use App\ViewComposers\ViewComposer;

#[RegistersViewComposer]
class ResourcesArchiveComposer extends ViewComposer
{
    public array $views = [
        'archive-resources.twig',
    ];

    public function withContext(): array
    {
        $resource_types = get_terms([
            'taxonomy' => 'resource_type',
        ]);
        $current_filters = $_GET['resource_type'] ?? [];

        return [
            'resource_types' => $resource_types,
            'current_filters' => $current_filters,
        ];
    }
}
