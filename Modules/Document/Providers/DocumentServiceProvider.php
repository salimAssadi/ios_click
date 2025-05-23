<?php

namespace Modules\Document\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;
use App\Providers\BaseModuleServiceProvider;
use Livewire\Livewire;
use Modules\Document\Http\Livewire\CreateDocument;
use Illuminate\Database\Eloquent\Relations\Relation;

/**
 * Class DocumentServiceProvider
 *
 * @author  Anuj Jaha Er <eranujjaha@gmail.com>
 */
class DocumentServiceProvider extends BaseModuleServiceProvider
{
    /**
     * @var string $moduleName
     */
    protected $moduleName = 'Document';

    /**
     * @var string $moduleNameLower
     */
    protected $moduleNameLower = 'document';

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->registerAssets();
        $this->registerLivewireComponents();
        $this->registerComponents();
        $this->loadMigrationsFrom(module_path($this->moduleName, 'Database/Migrations'));
       
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(RouteServiceProvider::class);
        
        // Register the commands
        
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            module_path($this->moduleName, 'Config/config.php') => config_path($this->moduleNameLower . '.php'),
        ], 'config');
        $this->mergeConfigFrom(
            module_path($this->moduleName, 'Config/config.php'), $this->moduleNameLower
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/' . $this->moduleNameLower);

        $sourcePath = module_path($this->moduleName, 'Resources/views');

        $this->publishes([
            $sourcePath => $viewPath
        ], ['views', $this->moduleNameLower . '-module-views']);

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), $this->moduleNameLower);
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/' . $this->moduleNameLower);

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $this->moduleNameLower);
        } else {
            $this->loadTranslationsFrom(module_path($this->moduleName, 'Resources/lang'), $this->moduleNameLower);
        }
    }

    /**
     * Register assets.
     *
     * @return void
     */
    protected function registerAssets()
    {
        $this->publishes([
            module_path($this->moduleName, 'Resources/assets') => public_path('modules/' . $this->moduleNameLower),
        ], ['assets', $this->moduleNameLower . '-module-assets']);
    }

    /**
     * Register Livewire components.
     *
     * @return void
     */
    protected function registerLivewireComponents()
    {
        Livewire::component('document::create-document', CreateDocument::class);
    }

    /**
     * Register Blade components.
     *
     * @return void
     */
    protected function registerComponents()
    {
        // Register the documents-table component
        $this->loadViewComponentsAs('document', [
            // Add more components here if needed
        ]);

        // This allows using <x-document::component-name /> syntax
        $this->app->afterResolving('blade.compiler', function () {
            $this->app['view']->addNamespace('document', module_path($this->moduleName, 'Resources/views/components'));
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }

    private function getPublishableViewPaths(): array
    {
        $paths = [];
        foreach (\Config::get('view.paths') as $path) {
            if (is_dir($path . '/modules/' . $this->moduleNameLower)) {
                $paths[] = $path . '/modules/' . $this->moduleNameLower;
            }
        }
        return $paths;
    }
}
