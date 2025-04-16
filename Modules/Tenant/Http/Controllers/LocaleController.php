<?php

namespace Modules\Tenant\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class LocaleController extends Controller
{
    /**
     * Set the application locale.
     *
     * @param  string  $locale
     * @return \Illuminate\Http\RedirectResponse
     */
    public function setLocale($locale)
    {
        $validLocale = in_array($locale, ['ar', 'en']) ? $locale : 'en';
        $langValue = $validLocale === 'ar' ? 'arabic' : 'english';
        
        // Update user's language preference if authenticated
        if (auth()->guard('tenant')->check()) {
            auth()->guard('tenant')->user()->update(['lang' => $langValue]);
        }

        // Also store in session for guests
        session()->put('locale', $validLocale);
        
        return redirect()->back();
    }
}
