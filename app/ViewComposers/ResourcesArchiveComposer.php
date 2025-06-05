<?php

namespace App\ViewComposers;

use Imarc\Millyard\Attributes\RegistersViewComposer;
use Imarc\Millyard\Views\Composer;

#[RegistersViewComposer]
class ResourcesArchiveComposer extends Composer
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
