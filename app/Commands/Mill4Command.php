<?php

namespace App\Commands;

class Mill4Command extends Command
{
    public string $name = 'mill4';

    public string $shortDescription = 'Mill4 commands';

    /**
     * @subcommand flush-rewrite-rules
     */
    public function flushRewriteRules($args, $assoc_args)
    {
        $this->line('Flushing rewrite rules...');

        flush_rewrite_rules();

        $this->line('Rewrite rules flushed!');
    }
}
