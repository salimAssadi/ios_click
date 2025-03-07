<?php

namespace App\Providers;

use Illuminate\Database\Migrations\Migrator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
       
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // $paths = [
        //     database_path('migrations/iso_dic'),
        //     database_path('migrations/iso_stream'),
        //     database_path('migrations/crm'),
        // ];
    
        // app(Migrator::class)->path($paths);
    }
}
