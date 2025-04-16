<?php

namespace Modules\Tenant\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\Tenant\Services\TenantService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

class TenantMiddleware
{
    protected $tenantService;

    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Skip if not authenticated with tenant guard
        if (!Auth::guard('tenant')->check()) {
            return redirect()->route('tenant.login');
        }

        // Get company details from session
        $companyName = session('company_name');
        $companyEmail = session('company_email');

        if (!$companyName || !$companyEmail) {
            Auth::guard('tenant')->logout();
            session()->flush();
            return redirect()->route('tenant.login')->with('error', 'Company information not found');
        }

        // Try to set up tenant connection
        $connection = $this->tenantService->setUpTenantConnection($companyName, $companyEmail);
        
        if (!$connection) {
            Auth::guard('tenant')->logout();
            session()->flush();
            return redirect()->route('tenant.login')->with('error', 'Company not found');
        }

        // Configure database connection for tenant
        Config::set('database.default', 'tenant');
        Config::set('database.connections.tenant', array_merge(
            Config::get('database.connections.mysql'),
            [
                'database' => $connection['database'],
            ]
        ));

        // Store tenant info in the request for later use
        $request->merge(['tenant' => $connection]);

        return $next($request);
    }
}
