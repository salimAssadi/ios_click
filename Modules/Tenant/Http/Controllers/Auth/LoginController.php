<?php

namespace Modules\Tenant\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Modules\Tenant\Entities\User;
use Modules\Tenant\Services\TenantService;

class LoginController extends Controller
{
    protected $tenantService;

    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
        $this->middleware('guest:tenant')->except('logout');
    }
    
    protected function guard()
    {
        return Auth::guard('tenant');
    }
    
    public function showLoginForm()
    {
        return view('tenant::auth.login');
    }
    
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'company_name' => ['required', 'string'],
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        // Set up tenant connection
        $connection = $this->tenantService->setUpTenantConnection($credentials['company_name'] );
        
        if (!$connection) {
            return back()
                ->withInput($request->only('email', 'company_name'))
                ->withErrors(['company_name' => 'Company not found or database connection failed.']);
        }

        // Store company info in session
        session(['company_name' => $credentials['company_name']]);
        session(['company_email' => $credentials['email']]);
        session(['tenant_db' => $connection['database']]);
        
        try {
            // Configure the auth guard to use tenant connection
            config(['database.default' => 'tenant']);

            $user = User::on('tenant')->where('email', $credentials['email'])->first();
            if (!$user) {
                return back()
                    ->withInput($request->only('email', 'company_name'))
                    ->withErrors(['email' => 'User not found in tenant database.']);
            }

            // // Check if user is already logged in on another device
            // if ($user->session_id && $user->session_id !== session()->getId()) {

            //     return back()
            //         ->withInput($request->only('email', 'company_name'))
            //         ->withErrors(['email' => 'User is already logged in on another device. Please logout first.']);
            // }

            if ($user && Hash::check($credentials['password'], $user->password)) {
                // Update session and login info
                $user->update([
                    'session_id' => session()->getId(),
                    'last_login_at' => now(),
                    'last_login_ip' => $request->ip()
                ]);
                $this->guard()->login($user);
                $request->session()->regenerateToken();
                return redirect()->intended(route('tenant.dashboard'));
            }
        } catch (\Exception $e) {
            \Log::error('Login error: ' . $e->getMessage());
            return back()
                ->withInput($request->only('email', 'company_name'))
                ->withErrors(['email' => 'Authentication failed. Please check your credentials.']);
        }
        
        return back()
            ->withInput($request->only('email', 'company_name'))
            ->withErrors(['password' => 'Invalid password.']);
    }

    public function logout(Request $request)
    {
        // Clear session ID from user record
        if ($this->guard()->check()) {
            $this->guard()->user()->update(['session_id' => null]);
        }
        $this->guard()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('tenant.login');
    }
}
