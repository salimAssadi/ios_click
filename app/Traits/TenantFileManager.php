<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;

trait TenantFileManager
{
    /**
     * Create tenant storage directory
     *
     * @param string $tenantId
     * @return void
     */
    public function createTenantStorage($tenantId)
    {
        $path = "tenants/{$tenantId}";
        
        // Create main tenant directory
        Storage::makeDirectory($path);
        
        // Create subdirectories for different types of files
        $directories = [
            'documents',
            'audits',
            'training',
            'risks',
            'complaints',
            'temp'
        ];

        foreach ($directories as $dir) {
            Storage::makeDirectory("{$path}/{$dir}");
        }
    }

    /**
     * Get tenant storage path
     *
     * @param string $tenantId
     * @return string
     */
    public function getTenantStoragePath($tenantId)
    {
        return "tenants/{$tenantId}";
    }

    /**
     * Get tenant file manager configuration
     *
     * @param string $tenantId
     * @return array
     */
    public function getTenantFileManagerConfig($tenantId)
    {
        $basePath = $this->getTenantStoragePath($tenantId);
        
        return [
            'base_path' => $basePath,
            'disk' => 'public',
            'max_size' => 50000, // 50MB
            'valid_mime' => [
                'image/jpeg',
                'image/png',
                'image/gif',
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            ]
        ];
    }
}
