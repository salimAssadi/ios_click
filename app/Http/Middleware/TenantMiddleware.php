<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\Tenant\Services\TenantService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
    // public function handle(Request $request, Closure $next)
    // {
    //     // Skip if not authenticated with tenant guard
    //     if (!Auth::guard('tenant')->check()) {
    //         return redirect()->route('tenant.login');
    //     }

    //     // Get company details from session
    //     $companyName = session('company_name');
    //     $companyEmail = session('company_email');

    //     if (!$companyName || !$companyEmail) {
    //         Auth::guard('tenant')->logout();
    //         session()->flush();
    //         return redirect()->route('tenant.login')->with('error', 'Company information not found');
    //     }

    //     // Try to set up tenant connection
    //     $connection = $this->tenantService->setUpTenantConnection($companyName, $companyEmail);
        
    //     if (!$connection) {
    //         Auth::guard('tenant')->logout();
    //         session()->flush();
    //         return redirect()->route('tenant.login')->with('error', 'Company not found');
    //     }

    //     // Configure database connection for tenant
    //     Config::set('database.default', 'tenant');
    //     Config::set('database.connections.tenant', array_merge(
    //         Config::get('database.connections.mysql'),
    //         [
    //             'database' => $connection['database'],
    //         ]
    //     ));

    //     // Store tenant info in the request for later use
    //     $request->merge(['tenant' => $connection]);

    //     return $next($request);
    // }

    public function handle(Request $request, Closure $next)
    {
        // إذا لم يكن المستخدم مسجل دخول عبر حارس tenant
        if (!Auth::guard('tenant')->check()) {
            return redirect()->route('tenant.login');
        }

        // جلب بيانات الشركة من الجلسة
        $companyName = session('company_name');
        $companyEmail = session('company_email');
        $tenantDatabase = session('tenant_db');

        if (!$companyName || !$companyEmail || !$tenantDatabase) {
            Auth::guard('tenant')->logout();
            session()->flush();
            return redirect()->route('tenant.login')->with('error', 'Company information not found');
        }

        // التحقق إذا كانت قاعدة البيانات مفعّلة مسبقاً
        $currentDb = null;
        try {
            $currentDb = DB::connection('tenant')->getDatabaseName();
        } catch (\Exception $e) {
            // الاتصال غير موجود أو انتهى، نحتاج لإعادة تهيئته
            Log::warning("Tenant DB connection not initialized, will be set.");
        }

        if ($currentDb !== $tenantDatabase) {
            // ضبط الاتصال بالقاعدة فقط إذا لم تكن متصلة
            $connection = $this->tenantService->setUpTenantConnection($companyName, $companyEmail);

            if (!$connection) {
                Auth::guard('tenant')->logout();
                session()->flush();
                return redirect()->route('tenant.login')->with('error', 'Company database connection failed.');
            }

            // تعيين الاتصال الافتراضي لـ Laravel
            Config::set('database.default', 'tenant');
            Config::set('database.connections.tenant', array_merge(
                Config::get('database.connections.mysql'),
                [
                    'database' => $connection['database'],
                ]
            ));

            // إعادة تهيئة الاتصال
            DB::purge('tenant');

            try {
                DB::connection('tenant')->getPdo(); // اختبار الاتصال
            } catch (\Exception $e) {
                Log::error("Tenant DB connection failed: " . $e->getMessage());
                Auth::guard('tenant')->logout();
                session()->flush();
                return redirect()->route('tenant.login')->with('error', 'Failed to connect to tenant database.');
            }

            // تخزين بيانات المستأجر في الطلب
            $request->merge(['tenant' => $connection]);
        }

        return $next($request);
    }

}
