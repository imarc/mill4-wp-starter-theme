<?php

namespace App\ViewComposers;

use Imarc\Millyard\Attributes\RegistersViewComposer;
use Imarc\Millyard\Views\Composer;

#[RegistersViewComposer]
class FooterComposer extends Composer
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
