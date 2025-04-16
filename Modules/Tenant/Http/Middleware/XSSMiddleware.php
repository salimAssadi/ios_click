<?php

namespace Modules\Tenant\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class XSSMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {   
        if(Auth::guard('tenant')->check())
        {
            $user = Auth::guard('tenant')->user();
            \App::setLocale($user->lang ?? 'english');
            $timezone = getSettingsValByName('timezone') ?? 'UTC';
            \Config::set('app.timezone', $timezone);
        }
        
        $input = $request->all();
        array_walk_recursive($input, function(&$input) {
            $input = strip_tags($input);
        });
        $request->merge($input);
        return $next($request);
    }
}
