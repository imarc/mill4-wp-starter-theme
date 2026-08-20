<?php

namespace App\Blocks;

use Imarc\Millyard\Attributes\RegistersBlock;

#[RegistersBlock]
class TestimonialBlock extends Block
{
    public const NAME = 'testimonial-block';
    public const TITLE = 'Testimonial Block';
    public const CATEGORY = 'block';
    public const ICON = 'admin-comments';
    public const POST_TYPES = [];
}
