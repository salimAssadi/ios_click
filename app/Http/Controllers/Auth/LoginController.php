<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LoginController extends Controller
{
    protected $tenantService;

    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'company_name' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // First check if company exists in CRM
        $company = DB::connection('crm')
            ->table('consulting_companies')
            ->where(function($query) use ($request) {
                $query->where('name_en', $request->company_name)
                      ->orWhere('name_ar', $request->company_name);
            })
            ->where('email', $request->email)
            ->first();

        if (!$company) {
            return back()->withErrors([
                'company_name' => 'Company not found'
            ]);
        }

        // Set up tenant connection
        $connection = $this->tenantService->setUpTenantConnection(
            $request->company_name,
            $request->email
        );

        if (!$connection) {
            return back()->withErrors([
                'email' => 'Unable to connect to company database'
            ]);
        }

        // Attempt to log in using tenant connection
        if (Auth::attempt([
            'email' => $request->email,
            'password' => $request->password
        ])) {
            // Store company info in session
            session([
                'company_name' => $request->company_name,
                'company_email' => $request->email,
                'company_id' => $company->id
            ]);

            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'email' => 'Invalid credentials'
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->flush();
        return redirect('/');
    }
}
