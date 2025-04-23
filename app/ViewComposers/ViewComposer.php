<?php

namespace App\ViewComposers;

abstract class ViewComposer
{
    public array $views = [];

    abstract public function with(array $context): array;
}
