<?php

namespace AnysiteDev\FrontendBuilder;

use AnysiteDev\FrontendBuilder\Blocks\Block;
use AnysiteDev\FrontendBuilder\Layouts\Layout;
use Exception;
use Illuminate\Support\Collection;
use InvalidArgumentException;

class FrontendBuilderManager
{
    const ID = 'frontend-builder';

    protected Collection $blocks;

    protected Collection $layouts;

    public function __construct()
    {
        $blocks = collect([]);
        $layouts = collect([]);

        $this->blocks = $blocks;
        $this->layouts = $layouts;
    }

    public function registerComponent(string $class, string $baseClass): void
    {
        match ($baseClass) {
            Layout::class => static::registerLayout($class),
            Block::class => static::registerBlock($class),
            default => throw new Exception('Invalid class type'),
        };
    }

    public function registerLayout(string $layout): void
    {
        if (! is_subclass_of($layout, Layout::class)) {
            throw new InvalidArgumentException("{$layout} must extend ".Layout::class);
        }

        $this->layouts->put($layout::getName(), $layout);
    }

    public function registerBlock(string $block): void
    {
        if (! is_subclass_of($block, Block::class)) {
            throw new InvalidArgumentException("{$block} must extend ".Block::class);
        }

        $this->blocks->put($block::getName(), $block);
    }

    public function getLayoutFromName(string $layoutName): ?string
    {
        return $this->layouts->get($layoutName);
    }

    public function getBlockFromName(string $name): ?string
    {
        return $this->blocks->get($name);
    }

    public function getLayouts(): array
    {
        return $this->layouts->map(fn ($layout) => $layout::getLabel())->toArray();
    }

    public function getDefaultLayoutName(): ?string
    {
        return $this->layouts->keys()->first();
    }

    public function getBlocks(): array
    {
        return $this->blocks->map(fn ($block) => $block::getBlockSchema())->toArray();
    }

    public function getBlocksRaw(): array
    {
        return $this->blocks->toArray();
    }
}
