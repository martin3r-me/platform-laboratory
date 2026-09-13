<?php

namespace Platform\Laboratory;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Relations\Relation;
use Livewire\Livewire;
use Platform\Core\PlatformCore;
use Platform\Core\Routing\ModuleRouter;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class LaboratoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/laboratory.php', 'laboratory');
    }

    public function boot(): void
    {
        // Morph-Alias — erbrachte Leistungen (encounter Service) binden per 'lab_test'.
        Relation::morphMap([
            'lab_test' => \Platform\Laboratory\Models\LabTest::class,
        ]);

        if (
            config()->has('laboratory.routing') &&
            config()->has('laboratory.navigation') &&
            Schema::hasTable('modules')
        ) {
            PlatformCore::registerModule([
                'key'        => 'laboratory',
                'title'      => 'Labor',
                'group'      => 'catalog',
                'routing'    => config('laboratory.routing'),
                'guard'      => config('laboratory.guard'),
                'navigation' => config('laboratory.navigation'),
                'sidebar'    => config('laboratory.sidebar'),
            ]);
        }

        if (PlatformCore::getModule('laboratory')) {
            ModuleRouter::group('laboratory', function () {
                $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
            });
        }

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        $this->publishes([
            __DIR__ . '/../config/laboratory.php' => config_path('laboratory.php'),
        ], 'config');

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'laboratory');

        $this->registerLivewireComponents();
    }

    /**
     * Registriert alle Livewire-Komponenten automatisch.
     * src/Livewire/Index.php -> alias laboratory.index
     */
    protected function registerLivewireComponents(): void
    {
        $basePath = __DIR__ . '/Livewire';
        $baseNamespace = 'Platform\\Laboratory\\Livewire';
        $prefix = 'laboratory';

        if (!is_dir($basePath)) {
            return;
        }

        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($basePath));

        foreach ($iterator as $file) {
            if (!$file->isFile() || $file->getExtension() !== 'php') {
                continue;
            }

            $relativePath = str_replace($basePath . DIRECTORY_SEPARATOR, '', $file->getPathname());
            $classPath = str_replace(['/', '.php'], ['\\', ''], $relativePath);
            $class = $baseNamespace . '\\' . $classPath;

            if (!class_exists($class)) {
                continue;
            }

            $alias = $prefix . '.' . str_replace('\\', '.', strtolower($classPath));
            Livewire::component($alias, $class);
        }
    }
}
