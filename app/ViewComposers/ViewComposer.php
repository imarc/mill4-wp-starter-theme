<?php

namespace App\ViewComposers;

abstract class ViewComposer
{
    public array $views = [];

    abstract public function withContext(): array;
}
