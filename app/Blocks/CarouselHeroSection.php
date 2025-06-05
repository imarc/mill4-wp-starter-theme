<?php

namespace App\Blocks;

use Imarc\Millyard\Attributes\RegistersBlock;
use Imarc\Millyard\Blocks\Block;

#[RegistersBlock]
class CarouselHeroSection extends Block
{
    public const NAME = 'carousel-hero-section';
    public const TITLE = 'Carousel Hero Section';
    public const CATEGORY = 'section';
    public const ICON = 'cover-image';
    public const POST_TYPES = [];
}
