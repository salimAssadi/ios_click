<?php

namespace Modules\DocumentControl\Http\Controllers;

use App\Traits\TenantFileManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FileManagerController extends Controller
{
    use TenantFileManager;

    /**
     * Display the file manager interface
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $tenantId = Auth::user()->tenant_id;
        $config = $this->getTenantFileManagerConfig($tenantId);
        
        return view('DocumentControl::filemanager', compact('config'));
    }

    /**
     * Get file manager configuration for current tenant
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getConfig(Request $request)
    {
        $tenantId = Auth::user()->tenant_id;
        $config = $this->getTenantFileManagerConfig($tenantId);
        
        return response()->json($config);
    }

    /**
     * Upload file to tenant's storage
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function upload(Request $request)
    {
        $tenantId = Auth::user()->tenant_id;
        $path = $this->getTenantStoragePath($tenantId);
        
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = $file->getClientOriginalName();
            $file->storeAs($path, $filename, 'public');
            
            return response()->json([
                'success' => true,
                'path' => "{$path}/{$filename}"
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'No file uploaded'
        ]);
    }
}