<?php

namespace App\Blocks;

use Imarc\Millyard\Attributes\RegistersBlock;
use Imarc\Millyard\Blocks\Block;

#[RegistersBlock]
class LogoCloudSection extends Block
{
    public const NAME = 'logo-cloud-section';
    public const TITLE = 'Logo Cloud Section';
    public const CATEGORY = 'section';
    public const ICON = 'cloud';
    public const POST_TYPES = [];
}
