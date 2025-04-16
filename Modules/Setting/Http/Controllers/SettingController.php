<?php

namespace Modules\Setting\Http\Controllers;

use App\Http\Controllers\BaseModuleController;
use Modules\Setting\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends BaseModuleController
{
    public function __construct()
    {
        parent::__construct();
        $this->viewPath = 'setting::settings';
        $this->routePrefix = 'settings';
        $this->moduleName = 'Setting';
    }

    public function index()
    {
        $settings = Setting::orderBy('group')->get()->groupBy('group');
        return $this->view('index', compact('settings'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'settings' => 'required|array',
            'settings.*.key' => 'required|string',
            'settings.*.value' => 'required',
            'settings.*.group' => 'required|string'
        ]);

        foreach ($validated['settings'] as $setting) {
            Setting::set($setting['key'], $setting['value'], $setting['group']);
        }

        return $this->success('Settings updated successfully.');
    }

    public function show($group)
    {
        $settings = Setting::where('group', $group)->get();
        return $this->view('show', compact('settings', 'group'));
    }
}
