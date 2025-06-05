<?php

namespace App\Blocks;

use Imarc\Millyard\Attributes\RegistersBlock;
use Imarc\Millyard\Blocks\Block;

#[RegistersBlock]
class CallToActionSection extends Block
{
    public const NAME = 'call-to-action-section';
    public const TITLE = 'Call to Action Section';
    public const CATEGORY = 'section';
    public const ICON = 'button';
    public const POST_TYPES = [];
    public const KEYWORDS = ['cta', 'call to action', 'section'];
}
