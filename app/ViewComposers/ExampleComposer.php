<?php

namespace App\ViewComposers;

class ExampleComposer extends ViewComposer
{
    public array $views = [
        'index.twig',
    ];

    public function with(array $data): array
    {
        // Add your custom context data here
        $data['example_data'] = [
            'message' => 'This data was added by the ExampleComposer',
            'timestamp' => time(),
        ];

        return $data;
    }
}
