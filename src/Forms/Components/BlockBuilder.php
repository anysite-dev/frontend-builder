<?php

namespace AnysiteDev\FrontendBuilder\Forms\Components;

use AnysiteDev\FrontendBuilder\Facades\FrontendBuilder;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Builder;
use Filament\Support\Enums\MaxWidth;

class BlockBuilder extends Builder
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->hiddenLabel();

        $this->blockPreviews();

        $this->collapsible();

        $this->cloneable();

        $this->blockPickerColumns(3);

        $this->blockIcons();

        $this->blockPickerWidth(MaxWidth::FiveExtraLarge);

        $this->blocks(FrontendBuilder::getBlocks());

        $this->mutateDehydratedStateUsing(static function (?array $state): array {
            if (! is_array($state)) {
                return array_values([]);
            }

            $registerBlockNames = array_keys(FrontendBuilder::getBlocksRaw());

            return collect($state)
                ->filter(fn (array $block) => in_array($block['type'], $registerBlockNames, true))
                ->values()
                ->toArray();
        });

        $this->deleteAction(
            fn (Action $action) => $action->requiresConfirmation(),
        );

        $this->editAction(
            fn (Action $action) => $action->modalSubmitActionLabel('Update'),
        );
    }

    public static function make(string $name = 'blocks'): static
    {
        return parent::make($name);
    }
}
