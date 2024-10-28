<?php

namespace AnysiteDev\FrontendBuilder;

use Filament\Contracts\Plugin;
use Filament\Panel;

class FrontendBuilderPlugin implements Plugin
{
    const string ID = 'frontend-builder';

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }

    public function getId(): string
    {
        return static::ID;
    }

    public function register(Panel $panel): void
    {
        //
    }

    public function boot(Panel $panel): void
    {
        //
    }
}
