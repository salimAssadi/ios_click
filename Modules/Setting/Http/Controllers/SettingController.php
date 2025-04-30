<?php

namespace Modules\Setting\Http\Controllers;

use App\Http\Controllers\BaseModuleController;
use Modules\Setting\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    public function footerData(Request $request)
    {   
        $settings = $request->all();
        unset($settings['_token']);
        unset($settings['tenant']);
        foreach ($settings as $s_key => $s_value) {
            if (!empty($s_value)) {
                if (is_array($s_value)) {
                    $s_value = json_encode($s_value); // Convert array to JSON string
                }
                DB::insert(
                    'insert into settings (`value`, `name`, `type`) values (?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`) ',
                    [
                        $s_value,
                        $s_key,
                        null
                    ]
                );
            }            
        }

        return $this->success('Footer settings save successfully.');
    }
    public function show($group)
    {
        $settings = Setting::where('group', $group)->get();
        return $this->view('show', compact('settings', 'group'));
    }
}
