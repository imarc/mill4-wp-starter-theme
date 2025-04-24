<?php

namespace App\ViewComposers;

class ExampleComposer extends ViewComposer
{
    public array $views = [
        'blocks/hero-section.twig',
    ];

    public function withContext(): array
    {
        // Add your custom context data here
        $data['phone_number'] = '123-123-1234';

        return $data;
    }
}
