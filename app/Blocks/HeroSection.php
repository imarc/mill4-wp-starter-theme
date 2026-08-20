<?php

namespace App\Blocks;

use Imarc\Millyard\Attributes\RegistersBlock;

#[RegistersBlock]
class HeroSection extends Block
{
    public const NAME = 'hero-section';
    public const TITLE = 'Hero Section';
    public const CATEGORY = 'section';
    public const ICON = 'cover-image';
    public const POST_TYPES = [];
}
