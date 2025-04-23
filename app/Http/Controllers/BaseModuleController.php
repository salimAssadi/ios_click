<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;

abstract class BaseModuleController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $viewPath;
    protected $routePrefix;
    protected $moduleName;

    public function __construct()
    {
        $this->middleware('auth:tenant');
    }

    protected function view($view, $data = [])
    {
        return view($this->viewPath . '.' . $view, $data);
    }

    protected function redirect($route, $params = [])
    {
        return redirect()->route($this->routePrefix . '.' . $route, $params);
    }

    protected function success($message)
    {
        return back()->with('success', $message);
    }

    protected function error($message)
    {
        return back()->with('error', $message)->withInput();
    }
}
