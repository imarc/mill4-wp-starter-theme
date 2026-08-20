<?php

namespace App\Blocks;

use Imarc\Millyard\Attributes\RegistersBlock;

#[RegistersBlock]
class Section extends Block
{
    public const NAME = 'section';
    public const TITLE = 'Section';
    public const CATEGORY = 'section';
    public const ICON = 'align-center';
    public const POST_TYPES = [];
    public const KEYWORDS = ['section', 'container'];

    protected function getConfig(): array
    {
        return array_merge(parent::getConfig(), [
            'supports' => [
                'align' => false,
                'anchor' => true,
                'jsx' => true,
            ],
            'acf' => [
                'mode' => 'preview',
            ],
        ]);
    }
}
