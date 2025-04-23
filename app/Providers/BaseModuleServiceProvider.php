<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

abstract class BaseModuleServiceProvider extends ServiceProvider
{
    protected $moduleName;
    protected $moduleNamespace;

    public function register()
    {
        // Register module configuration
        $this->mergeConfigFrom(
            module_path($this->moduleName, 'Config/config.php'), $this->moduleName
        );

        // Register module views
        $this->loadViewsFrom(module_path($this->moduleName, 'Resources/views'), $this->moduleName);

        // Register module translations
        $this->loadTranslationsFrom(module_path($this->moduleName, 'Resources/lang'), $this->moduleName);

        // Register module migrations
        $this->loadMigrationsFrom(module_path($this->moduleName, 'Database/Migrations'));

        // Register module factories
        if (is_dir(module_path($this->moduleName, 'Database/factories'))) {
            $this->app->make('Illuminate\Database\Eloquent\Factory')
                ->load(module_path($this->moduleName, 'Database/factories'));
        }
    }

    public function boot()
    {
        // Register module routes
        if (file_exists(module_path($this->moduleName, 'Routes/web.php'))) {
            $this->loadRoutesFrom(module_path($this->moduleName, 'Routes/web.php'));
        }

        if (file_exists(module_path($this->moduleName, 'Routes/api.php'))) {
            $this->loadRoutesFrom(module_path($this->moduleName, 'Routes/api.php'));
        }

        // Register module middleware
        $this->app['router']->aliasMiddleware('auth:tenant', \App\Http\Middleware\TenantMiddleware::class);
    }
}
