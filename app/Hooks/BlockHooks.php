<?php

namespace App\Hooks;

use App\Attributes\RegistersBlock;
use App\Hooks\Concerns\RegistersHooks;
use App\Hooks\Contracts\HooksInterface;
use ReflectionClass;

class BlockHooks implements HooksInterface
{
    use RegistersHooks;

    public function initialize(): void
    {
        $this->addAction('init', [$this, 'registerBlocks']);
    }

    public function registerBlocks()
    {
        $blockClasses = $this->discoverBlocks();

        foreach ($blockClasses as $blockClass) {
            $block = new $blockClass();

            if (! method_exists($block, 'register')) {
                throw new \RuntimeException(sprintf('Could not register class %s. register() does not exist', $blockClass));
            }

            $block->register();
            do_action('mill4_block_registered', $blockClass);
        }
    }

    private function discoverBlocks(): array
    {
        $blockClasses = [];
        $namespace = 'App\\Blocks\\';
        $directory = get_template_directory() . '/app/Blocks';

        foreach (glob($directory . '/*.php') as $file) {
            $className = $namespace . basename($file, '.php');

            if (!class_exists($className)) {
                continue;
            }

            $reflection = new ReflectionClass($className);

            if ($reflection->isAbstract() || !$reflection->isSubclassOf('App\\Blocks\\Block')) {
                continue;
            }

            if (!empty($reflection->getAttributes(RegistersBlock::class))) {
                $blockClasses[] = $className;
            }
        }

        return $blockClasses;
    }
}
