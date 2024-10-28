<?php

namespace AnysiteDev\FrontendBuilder\Forms\Components;

use AnysiteDev\FrontendBuilder\Facades\FrontendBuilder;
use Filament\Forms\Components\Select;

class LayoutSelect extends Select
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('frontend-builder::layout-select.label'))
            ->options(FrontendBuilder::getLayouts())
            ->default(fn () => FrontendBuilder::getDefaultLayoutName())
            ->required();
    }

    public static function make(string $name = 'layout'): static
    {
        return parent::make($name);
    }
}
