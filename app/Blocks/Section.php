<?php

namespace App\Blocks;

use App\Attributes\RegistersBlock;

#[RegistersBlock]
class Section extends Block
{
    public const NAME = 'section';
    public const TITLE = 'Section';
    public const CATEGORY = 'section';
    public const ICON = 'align-center';
    public const POST_TYPES = [];
    public const KEYWORDS = ['section', 'container'];

    public function getConfig(): array
    {
        return [
            'supports' => [
                'align' => false,
                'anchor' => true,
                'jsx' => true,
            ],
            'acf' => [
                'mode' => 'preview',
            ],
            'acf_block_version' => 2,
        ];
    }
}
