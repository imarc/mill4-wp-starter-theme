<?php

namespace App\Commands;

abstract class Command
{
    protected string $name;

    protected string $shortDescription = '';

    protected string $longDescription = '';

    protected array $synopsis = [];

    protected string $when = 'after_wp_load';

    public function register(): void
    {
        if (! (defined('WP_CLI') && constant('WP_CLI'))) {
            return;
        }

        \WP_CLI::add_command($this->name, $this, [
            'shortdesc' => $this->shortDescription,
            'longdesc' => $this->longDescription,
            'synopsis' => $this->synopsis,
            'when' => $this->when,
        ]);
    }

    protected function line($message = '')
    {
        \WP_CLI::line($message);
    }

    protected function success($message = '')
    {
        \WP_CLI::success($message);
    }

    protected function error($message, $exit = true)
    {
        \WP_CLI::error($message, $exit);
    }

    protected function warning($message = '')
    {
        \WP_CLI::warning($message);
    }

    protected function log($message = '')
    {
        \WP_CLI::log($message);
    }

    protected function confirm($question, $assoc_args)
    {
        return \WP_CLI::confirm($question, $assoc_args);
    }

    abstract public function __invoke($args, $assoc_args);
}
