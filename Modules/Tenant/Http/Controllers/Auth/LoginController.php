<?php

namespace Modules\Tenant\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Tenant\Services\TenantService;

class LoginController extends Controller
{
    protected $tenantService;

    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
    }

    public function showLoginForm()
    {
        return view('tenant::auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
            'company_name' => ['required']
        ]);

        // Try to set up tenant connection
        $connection = $this->tenantService->setUpTenantConnection(
            $credentials['company_name'],
            $credentials['email']
        );

        if (!$connection) {
            return back()
                ->withInput($request->only('email', 'company_name'))
                ->withErrors(['company_name' => 'Company not found']);
        }

        // Store company info in session
        session(['company_name' => $credentials['company_name']]);
        session(['company_email' => $credentials['email']]);
        if (Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password']])) {
            $request->session()->regenerate();
            return redirect()->intended(route('tenant.dashboard'));
        }

        
        return back()
            ->withInput($request->only('email', 'company_name'))
            ->withErrors(['email' => 'Invalid credentials']);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('tenant.login');
    }
}
