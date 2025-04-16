<?php

namespace Modules\Tenant\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;
use Illuminate\Support\Facades\Blade;
use Modules\Tenant\Http\Middleware\TenantMiddleware;
use Modules\Tenant\Http\Middleware\XSSMiddleware;
use Illuminate\Support\Facades\Auth;

class TenantServiceProvider extends ServiceProvider
{
    /**
     * @var string $moduleName
     */
    protected $moduleName = 'Tenant';

    /**
     * @var string $moduleNameLower
     */
    protected $moduleNameLower = 'tenant';

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
        $this->loadMigrationsFrom(module_path($this->moduleName, 'Database/Migrations'));
        $this->registerComponents();
        // Register the middleware
        $this->app['router']->aliasMiddleware('tenant', TenantMiddleware::class);
        $this->app['router']->aliasMiddleware('xss', XSSMiddleware::class);

        // Set locale from user's language preference
        if(Auth::guard('tenant')->check())
        {
            $user = Auth::guard('tenant')->user();
            \App::setLocale($user->lang ?? 'english');
            $timezone = getSettingsValByName('timezone') ?? 'UTC';
            \Config::set('app.timezone', $timezone);
        }else{
            \App::setLocale('arabic');
            \Config::set('app.timezone', 'UTC');
            
        }

    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(RouteServiceProvider::class);
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
     * Register components.
     *
     * @return void
     */
    protected function registerComponents()
    {
        // Register Blade Components
        Blade::componentNamespace('Modules\\Tenant\\View\\Components', 'tenant');

        // Anonymous Blade Components
        $components = [
            'application-logo',
            'dropdown',
            'dropdown-link',
            'nav-link',
            'responsive-nav-link',
            'auth-session-status',
            'input-error',
            'input-label',
            'text-input',
            'primary-button'
        ];

        foreach ($components as $component) {
            Blade::component('tenant::components.' . $component, $component);
        }
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
