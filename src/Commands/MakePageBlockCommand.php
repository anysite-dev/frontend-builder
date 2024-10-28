<?php

namespace AnysiteDev\FrontendBuilder\Commands;

use Filament\Support\Commands\Concerns\CanManipulateFiles;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

use function Laravel\Prompts\text;

class MakePageBlockCommand extends Command
{
    use CanManipulateFiles;

    protected $signature = 'make:frontend-block {name?} {--F|force}';

    protected $description = 'Create a new frontend block';

    public function handle(): int
    {
        $block = (string) Str::of($this->argument('name') ?? text(
            label: 'What is the block name?',
            placeholder: 'E.g. HeroBlock or Hero/Basic',
            required: true,
        ))
            ->trim('/')
            ->trim('\\')
            ->trim(' ')
            ->replace('/', '\\');

        $blockClass = (string) Str::of($block)->afterLast('\\');

        $blockNamespace = Str::of($block)->contains('\\') ?
            (string) Str::of($block)->beforeLast('\\') :
            '';

        $label = Str::of($block)
            ->beforeLast('Block')
            ->explode('\\')
            ->map(fn ($segment) => Str::studly($segment))
            ->implode(': ');

        $shortName = Str::of($block)
            ->beforeLast('Block')
            ->explode('\\')
            ->map(fn ($segment) => Str::kebab($segment))
            ->implode('.');

        $view = Str::of($block)
            ->beforeLast('Block')
            ->prepend('components\\blocks\\')
            ->explode('\\')
            ->map(fn ($segment) => Str::kebab($segment))
            ->implode('.');

        $path = app_path(
            (string) Str::of($block)
                ->prepend('Filament\\Frontend\\Blocks\\')
                ->replace('\\', '/')
                ->append('.php'),
        );

        $viewPath = resource_path(
            (string) Str::of($view)
                ->replace('.', '/')
                ->prepend('views/')
                ->append('.blade.php'),
        );

        $files = [$path, $viewPath];

        if (! $this->option('force') && $this->checkForCollision($files)) {
            return static::INVALID;
        }

        $this->copyStubToApp('Block', $path, [
            'label' => $label,
            'class' => $blockClass,
            'namespace' => 'App\\Filament\\Frontend\\Blocks'.($blockNamespace !== '' ? "\\{$blockNamespace}" : ''),
            'shortName' => $shortName,
            'componentPath' => "components.blocks.$shortName",
        ]);

        $this->copyStubToApp('BlockView', $viewPath);

        $this->info("Successfully created {$block}!");

        return static::SUCCESS;
    }
}
