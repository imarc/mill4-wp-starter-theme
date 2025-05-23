<?php

namespace App\ViewComposers;

use App\Attributes\RegistersViewComposer;

#[RegistersViewComposer]
class FooterComposer extends ViewComposer
{
    public array $views = [
        'footer.twig',
    ];

    public function withContext(): array
    {
        $social = get_field('social_media', 'option');

        return [
            'social_links' => $social['links'] ?? [],
        ];
    }
}
