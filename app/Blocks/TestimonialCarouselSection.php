<?php

namespace App\Blocks;

use App\Attributes\RegistersBlock;

#[RegistersBlock]
class TestimonialCarouselSection extends Block
{
    public const NAME = 'testimonial-carousel-section';
    public const TITLE = 'Testimonial Carousel Section';
    public const CATEGORY = 'section';
    public const ICON = 'admin-comments';
    public const POST_TYPES = [];
}
