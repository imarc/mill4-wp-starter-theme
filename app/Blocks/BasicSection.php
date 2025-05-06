<?php

namespace App\Blocks;

use App\Attributes\RegistersBlock;

#[RegistersBlock]
class BasicSection extends Block
{
    public const NAME = 'basic-section';
    public const TITLE = 'Basic Section';
    public const CATEGORY = 'section';
    public const ICON = 'align-center';
    public const POST_TYPES = [];
    public const KEYWORDS = ['section'];

    private array $columnWidthMap = [
        '1/3' => '2/3',
        '1/2' => '1/2',
        '2/3' => '1/3',
    ];

    private array $widthClassMap = [
        '1/3' => 'one-third',
        '1/2' => 'half',
        '2/3' => 'two-thirds',
    ];

    public function withContext(): array
    {
        $block = $this->context['block'] ?? [];

        if ($block['layout'] === '2-column') {
            $block['secondary_content']['width'] = $this->columnWidthMap[$block['primary_content']['width'] ?? '1/2'];
        }

        // Convert width values to CSS classes
        if (isset($block['primary_content']['width'])) {
            $block['primary_content']['width'] = $this->widthClassMap[$block['primary_content']['width']];
        }
        if (isset($block['secondary_content']['width'])) {
            $block['secondary_content']['width'] = $this->widthClassMap[$block['secondary_content']['width']];
        }

        return [
            'block' => $block,
        ];
    }
}
