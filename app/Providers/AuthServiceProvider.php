<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Cache;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        Gate::before(function ($user, $ability) {
            if (auth()->guard('tenant')->check() && auth()->guard('tenant')->user()->id === $user->id) {
                $cacheKey = 'tenant_permissions_' . $user->id;
    
                $permissions = Cache::remember($cacheKey, now()->addMinutes(60), function () use ($user) {
                    return $user->getAllPermissions()->pluck('name')->toArray();
                });
    
                return in_array($ability, $permissions);
            }
    
            return null; 
        });
    }
}
