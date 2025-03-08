<?php

namespace App\Http\Controllers\iso_dic\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\LoggedHistory;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticatedIsoDicController extends Controller
{
    /**
     * Display the login view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {   
        if (!file_exists(setup())) {
            header('location:install');
            die;
        }
        if(!Auth::user()){

            $user = \App\Models\User::find(1);
            \App::setLocale($user->lang);
            return view($this->iso_dic_path.'.auth.login');
        }else{
            return redirect()->intended($this->iso_dic_path.'/home');
        }
    }

    /**
     * Handle an incoming authentication request.
     *
     * @param  \App\Http\Requests\Auth\LoginRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(LoginRequest $request)
    {
        $google_recaptcha = getSettingsValByName('google_recaptcha');
        if ($google_recaptcha == 'on') {
            $validation['g-recaptcha-response'] = 'required|captcha';
        } else {
            $validation = [];
        }
        $this->validate($request, $validation);

        $request->authenticate();
        $request->session()->regenerate();
        $loginUser = Auth::user();
        if ($loginUser->is_active == 0) {
            auth()->logout();
            return redirect()->route($this->iso_dic_path.'.login')->with('error', __('Your account is temporarily inactive. Please contact your administrator to reactivate your account.'));
        }
        if (empty($loginUser->email_verified_at)) {
            auth()->logout();
            return redirect()->route($this->iso_dic_path.'.login')->with('error', __('Verification required: Please check your email to verify your account before continuing.'));
        }
        userLoggedHistory();
        // if ($loginUser->type == 'owner') {

        //     if ($loginUser->subscription_expire_date != null && date('Y-m-d') > $loginUser->subscription_expire_date) {
        //         assignSubscription(1);
        //         return redirect()->intended(RouteServiceProvider::HOME)->with('error', __('Your subscription has ended, and access to premium features is now restricted. To continue using our services without interruption, please renew your plan or upgrade to a higher-tier package.'));
        //     }
        // }
        return redirect()->intended($this->iso_dic_path.'/home');
    }

    /**
     * Destroy an authenticated session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
