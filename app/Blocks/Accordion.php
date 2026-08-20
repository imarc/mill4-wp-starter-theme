<?php

namespace App\Blocks;

use Imarc\Millyard\Attributes\RegistersBlock;

#[RegistersBlock]
class Accordion extends Block
{
    public const NAME = 'accordion';
    public const TITLE = 'Accordion';
    public const CATEGORY = 'section';
    public const ICON = 'menu-alt3';
    public const POST_TYPES = [];
    public const KEYWORDS = ['accordion', 'section'];
}
