<?php

namespace App\Blocks;

use Imarc\Millyard\Blocks\Block as MillyardBlock;

abstract class Block extends MillyardBlock
{
    protected function getConfig(): array
    {
        $version = self::blockApiVersion();

        $config = [
            'api_version' => $version,
            'acf_block_version' => $version,
        ];

        return $config;
    }

    protected static function blockApiVersion(): int
    {
        return version_compare(get_bloginfo('version'), 7.1, '>=')
            ? 3
            : 2;
    }
}
