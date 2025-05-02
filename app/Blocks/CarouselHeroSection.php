<?php

namespace App\Blocks;

use App\Attributes\RegistersBlock;

#[RegistersBlock]
class CarouselHeroSection extends Block
{
    public const NAME = 'carousel-hero-section';
    public const TITLE = 'Carousel Hero Section';
    public const CATEGORY = 'section';
    public const ICON = 'cover-image';
    public const POST_TYPES = [];
}
