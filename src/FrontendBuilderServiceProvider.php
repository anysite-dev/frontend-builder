<?php

namespace AnysiteDev\FrontendBuilder;

use AnysiteDev\FrontendBuilder\Blocks\Block;
use AnysiteDev\FrontendBuilder\Facades\FrontendBuilder;
use AnysiteDev\FrontendBuilder\Layouts\Layout;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use ReflectionClass;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Symfony\Component\Finder\SplFileInfo;

class FrontendBuilderServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package->name(FrontendBuilderManager::ID)
            ->hasViews()
            ->hasTranslations()
            ->hasConfigFile()
            ->hasCommands([
                Commands\MakeLayoutCommand::class,
                Commands\MakePageBlockCommand::class,
            ]);
    }

    public function packageRegistered(): void
    {
        parent::packageRegistered();

        $this->app->singleton('frontend-builder', function () {
            return new FrontendBuilderManager;
        });
    }

    public function bootingPackage(): void
    {
        $this->app->afterResolving('translation.loader', function () {
            $this->registerComponentsFromDirectory(
                Block::class,
                config('frontend-builder.blocks.register'),
                config('frontend-builder.blocks.path'),
                config('frontend-builder.blocks.namespace')
            );
        });

        $this->registerComponentsFromDirectory(
            Layout::class,
            config('frontend-builder.layouts.register'),
            config('frontend-builder.layouts.path'),
            config('frontend-builder.layouts.namespace')
        );
    }

    protected function registerComponentsFromDirectory(string $baseClass, array $register, ?string $directory, ?string $namespace): void
    {
        if (blank($directory) || blank($namespace)) {
            return;
        }

        $filesystem = app(Filesystem::class);

        if ((! $filesystem->exists($directory)) && (! Str::of($directory)->contains('*'))) {
            return;
        }

        $namespace = Str::of($namespace);

        $register = array_merge(
            $register,
            collect($filesystem->allFiles($directory))
                ->map(function (SplFileInfo $file) use ($namespace): string {
                    $variableNamespace = $namespace->contains('*') ? str_ireplace(
                        ['\\'.$namespace->before('*'), $namespace->after('*')],
                        ['', ''],
                        Str::of($file->getPath())
                            ->after(base_path())
                            ->replace(['/'], ['\\']),
                    ) : null;

                    return (string) $namespace
                        ->append('\\', $file->getRelativePathname())
                        ->replace('*', $variableNamespace)
                        ->replace(['/', '.php'], ['\\', '']);
                })
                ->filter(fn (string $class): bool => is_subclass_of($class, $baseClass) && (! (new ReflectionClass($class))->isAbstract()))
                ->each(fn (string $class) => FrontendBuilder::registerComponent($class, $baseClass))
                ->all(),
        );
    }
}
