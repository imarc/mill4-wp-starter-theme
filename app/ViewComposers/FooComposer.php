<?php

namespace App\ViewComposers;

class FooComposer extends ViewComposer
{
    public array $views = [
        'index.twig',
    ];

    public function with(array $context): array
    {
        $context['myfoo'] = 'barrrrrr';

        return $context;
    }

}
