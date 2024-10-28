<?php

namespace AnysiteDev\FrontendBuilder\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static void registerComponent(string $class, string $baseClass)
 * @method static void registerLayout(string $layout)
 * @method static void registerPageBlock(string $pageBlock)
 * @method static ?string getLayoutFromName(string $layoutName)
 * @method static ?string getPageBlockFromName(string $name)
 * @method static array getLayouts()
 * @method static string getDefaultLayoutName()
 * @method static array getBlocks()
 * @method static array getBlocksRaw()
 *
 * @see \AnysiteDev\FrontendBuilder\FrontendBuilderManager
 */
class FrontendBuilder extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'frontend-builder';
    }
}
