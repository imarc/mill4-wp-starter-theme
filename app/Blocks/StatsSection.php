<?php

namespace App\Blocks;

use Imarc\Millyard\Attributes\RegistersBlock;

#[RegistersBlock]
class StatsSection extends Block
{
    public const NAME = 'stats-section';
    public const TITLE = 'Stats Section';
    public const CATEGORY = 'section';
    public const ICON = 'screenoptions';
    public const POST_TYPES = [];
    public const KEYWORDS = ['stats', 'section'];
}
