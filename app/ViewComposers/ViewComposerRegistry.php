<?php

namespace App\ViewComposers;

use App\Services\Container;

class ViewComposerRegistry
{
    private array $composers = [];

    public function __construct(private Container $container)
    {
    }

    public function registerComposer(string $composerClass): void
    {
        if (! is_subclass_of($composerClass, ViewComposer::class)) {
            throw new \InvalidArgumentException(sprintf(
                'Composer class %s must extend %s',
                $composerClass,
                ViewComposer::class
            ));
        }

        $composer = $this->container->get($composerClass);

        foreach ($composer->views as $view) {
            $this->composers[$view] = $composerClass;
        }
    }

    public function getComposers(): array
    {
        return $this->composers;
    }

    public function hasComposers(): bool
    {
        return ! empty($this->composers);
    }

    /**
     * Filter the data for composers. This is registered as a
     * timber/render/data filter.
     */
    public function filterDataForComposers(array $data, string $template): array
    {
        foreach ($this->composers as $view => $composerClass) {
            if ($view === $template) {
                $composer = $this->container->get($composerClass);
                $data = [...$data, ...$composer->with($data)];
            }
        }

        return $data;
    }
}
