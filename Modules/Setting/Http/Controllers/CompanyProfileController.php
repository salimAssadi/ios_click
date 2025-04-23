<?php

namespace Modules\Setting\Http\Controllers;

use App\Http\Controllers\BaseModuleController;
use Modules\Setting\Entities\CompanyProfile;
use Illuminate\Http\Request;

class CompanyProfileController extends BaseModuleController
{
    public function __construct()
    {
        parent::__construct();
        $this->viewPath = 'setting::company-profile';
        $this->routePrefix = 'settings.company-profile';
        $this->moduleName = 'Setting';
    }

    public function index()
    {
        $profile = CompanyProfile::first();
        return $this->view('index', compact('profile'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string',
            'address' => 'required|string',
            'city' => 'required|string',
            'country' => 'required|string',
            'postal_code' => 'nullable|string',
            'description' => 'nullable|string',
            'website' => 'nullable|url',
            'tax_number' => 'nullable|string',
            'registration_number' => 'nullable|string',
        ]);

        $profile = CompanyProfile::updateOrCreate(
            ['tenant_id' => tenantId()],
            $validated
        );

        if ($request->hasFile('logo')) {
            $logo = $request->file('logo')->store('company-logos', 'public');
            $profile->update(['logo' => $logo]);
        }

        return $this->success('Company profile updated successfully.');
    }
}
