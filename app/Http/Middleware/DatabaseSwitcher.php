<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class DatabaseSwitcher
{
    public function handle(Request $request, Closure $next)
    {
        // Get the first segment of the URL to determine the module
        $segment = $request->segment(1);

        // Set the database connection based on the route
        switch ($segment) {
            case 'iso_dic':
                Config::set('database.default', 'iso_dic');
                DB::purge('iso_dic'); 
                DB::reconnect('iso_dic'); 
                break;

            case 'iso_stream':
                Config::set('database.default', 'iso_stream');
                DB::purge('iso_stream');
                DB::reconnect('iso_stream');
                break;

            case 'crm':
                Config::set('database.default', 'crm');
                DB::purge('crm');
                DB::reconnect('crm');
                break;

            default:
                Config::set('database.default', 'mysql'); // Default connection
                break;
        }

        return $next($request);
    }
}
