<?php

namespace Modules\Setting\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Setting\Entities\CompanySeal;
use Illuminate\Support\Facades\Storage;

class CompanySealController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    
    public function index()
    {
        $companySeals = CompanySeal::all();
        return view('setting::company-seals.index', compact('companySeals'));
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('setting::company-seals.create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'seal_file' => 'required|file|mimes:jpeg,png,jpg,gif,svg',
            'is_active' => 'sometimes|boolean',
        ]);
        $type = str_replace(' ', '_', strtolower($request->name_en));
        $filePath = null;
        if ($request->hasFile('seal_file')) {
            $tenantRoot = getTenantRoot();
            if(!$tenantRoot){
                return redirect()->back()->with('error', 'Tenant root not found');
            }
            $file = $request->file('seal_file');
            $filePath = $file->store($tenantRoot.'/seals', 'tenants');
        }

        CompanySeal::create([
            'name_ar' => $request->name_ar,
            'name_en' => $request->name_en,
            'type' => $type,
            'file_path' => $filePath,
            'is_active' => $request->is_active ?? true,
        ]);

        return redirect()->route('tenant.setting.company-seals.index')
            ->with('success', __('Company seal created successfully'));
    }

    /**
     * Show the specified resource.
     * @param CompanySeal $companySeal
     * @return Renderable
     */
    public function show(CompanySeal $companySeal)
    {
        return view('setting::company-seals.show', compact('companySeal'));
    }

    /**
     * Show the form for editing the specified resource.
     * @param CompanySeal $companySeal
     * @return Renderable
     */
    public function edit(CompanySeal $companySeal)
    {
        return view('setting::company-seals.edit', compact('companySeal'));
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param CompanySeal $companySeal
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, CompanySeal $companySeal)
    {
        $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'seal_file' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg',
            'is_active' => 'sometimes|boolean',
        ]);
        $type = str_replace(' ', '_', strtolower($request->name_en));
        $tenantRoot = getTenantRoot();
        if(!$tenantRoot){
            return redirect()->back()->with('error', 'Tenant root not found');
        }
        $data = [
            'name_ar' => $request->name_ar,
            'name_en' => $request->name_en,
            'type' => $type,
            'is_active' => $request->is_active ?? $companySeal->is_active,
        ];

        if ($request->hasFile('seal_file')) {
            // Delete old file if exists
            if ($companySeal->file_path && Storage::disk('tenants')->exists($companySeal->file_path)) {
                Storage::disk('tenants')->delete($companySeal->file_path);
            }
            
            $file = $request->file('seal_file');
            $data['file_path'] = $file->store($tenantRoot.'/seals', 'tenants');
        }

        $companySeal->update($data);

        return redirect()->route('tenant.setting.company-seals.index')
            ->with('success', __('Company seal updated successfully'));
    }

    /**
     * Remove the specified resource from storage.
     * @param CompanySeal $companySeal
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(CompanySeal $companySeal)
    {
        $tenantRoot = getTenantRoot();
        if(!$tenantRoot){
            return redirect()->back()->with('error', 'Tenant root not found');
        }
        // Delete file if exists
        if ($companySeal->file_path && Storage::disk('tenants')->exists($companySeal->file_path)) {
            Storage::disk('tenants')->delete($companySeal->file_path);
        }

        $companySeal->delete();

        return redirect()->route('setting.company-seals.index')
            ->with('success', __('Company seal deleted successfully'));
    }
}
