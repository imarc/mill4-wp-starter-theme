<?php

namespace App\Blocks;

use App\Attributes\RegistersBlock;

#[RegistersBlock]
class FeatureCards extends Block
{
    public const NAME = 'feature-cards';
    public const TITLE = 'Feature Cards';
    public const CATEGORY = 'section';
    public const ICON = 'screenoptions';
    public const POST_TYPES = [];
    public const KEYWORDS = ['cards', 'section'];
}
