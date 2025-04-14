<?php

namespace Modules\Tenant\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\Tenant\Services\TenantService;

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
        // Skip if not authenticated
        if (!auth()->check()) {
            return $next($request);
        }

        // Get company details from session
        $companyName = session('company_name');
        $companyEmail = session('company_email');

        if (!$companyName || !$companyEmail) {
            return redirect()->route('tenant.login');
        }

        // Try to set up tenant connection
        $connection = $this->tenantService->setUpTenantConnection($companyName, $companyEmail);
        
        if (!$connection) {
            auth()->logout();
            session()->flush();
            return redirect()->route('tenant.login')->with('error', 'Company not found');
        }

        // Store tenant info in the request for later use
        $request->merge(['tenant' => $connection]);

        return $next($request);
    }
}
