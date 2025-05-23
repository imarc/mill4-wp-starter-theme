<?php

namespace App\ViewComposers;

abstract class ViewComposer
{
    public array $views = [];

    protected array $contextData = [];

    abstract public function withContext(): array;

    public function getContextData(): array
    {
        return $this->contextData;
    }

    public function setContextData(array $data): void
    {
        $this->contextData = $data;
    }
}
