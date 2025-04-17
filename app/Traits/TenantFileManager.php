<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;
use App\Services\PdfGenerationService;
use Illuminate\Http\UploadedFile;

trait TenantFileManager
{
    protected PdfGenerationService $pdfService;

    public function __construct()
    {
        $this->pdfService = app(PdfGenerationService::class);
    }

    /**
     * Create storage directories for a tenant
     *
     * @param string $tenantId
     * @return void
     */
    protected function createTenantStorage($tenantId)
    {
        $path = "tenants/{$tenantId}";
        
        // Create main tenant directory
        Storage::makeDirectory($path);
        
        // Define the hierarchical directory structure
        $directories = [
            'documents' => [
                'procedures' => [
                    'active',
                    'archived',
                    'drafts',
                    'versions'
                ],
                'policies' => [
                    'active',
                    'archived',
                    'drafts',
                    'versions'
                ],
                'instructions' => [
                    'active',
                    'archived',
                    'drafts',
                    'versions'
                ],
                'samples' => [
                    'active',
                    'archived',
                    'drafts',
                    'versions'
                ],
                'custom' => [
                    'active',
                    'archived',
                    'drafts',
                    'versions'
                ]
            ],
            'audits' => [
                'internal',
                'external',
                'reports',
                'findings',
                'archived'
            ],
            'training' => [
                'materials',
                'records',
                'certificates',
                'assessments',
                'archived'
            ],
            'risks' => [
                'assessments',
                'treatments',
                'reports',
                'archived'
            ],
            'complaints' => [
                'incoming',
                'processing',
                'resolved',
                'reports',
                'archived'
            ],
            'temp' => []
        ];

        // Create the directory structure recursively
        $this->createDirectoryStructure($path, $directories);
    }

    /**
     * Create directory structure recursively
     *
     * @param string $basePath
     * @param array $directories
     * @return void
     */
    private function createDirectoryStructure($basePath, $directories)
    {
        foreach ($directories as $dir => $subDirs) {
            $path = "{$basePath}/{$dir}";
            Storage::makeDirectory($path);

            if (is_array($subDirs)) {
                $this->createDirectoryStructure($path, $subDirs);
            }
        }
    }

    /**
     * Save document file
     *
     * @param string $tenantId
     * @param string $documentType procedures|policies|instructions|samples|custom
     * @param string $fileName
     * @param string|UploadedFile $content Content or uploaded file
     * @param string $status active|archived|drafts
     * @return string File path
     */
    protected function saveDocument($tenantId, $documentType, $fileName, $content, $status = 'active')
    {   
        $path = "tenants/{$tenantId}/documents/{$documentType}/{$status}/{$fileName}";

        // Handle file upload
        if ($content instanceof UploadedFile) {
            Storage::putFileAs(dirname($path), $content, basename($path));
            return $path;
        }

        // Handle content as string (generate PDF)
        if (is_string($content) && !empty($content)) {
            $metadata = [
                'title' => pathinfo($fileName, PATHINFO_FILENAME),
                'status' => $status,
                'document_type' => $documentType
            ];

            $pdf = $this->pdfService->generateDocument($content, $metadata);
            Storage::put($path, $pdf);
            return $path;
        }

        // If no content and no file, create empty file
        Storage::put($path, '');
        return $path;
    }

    /**
     * Create document version
     *
     * @param string $tenantId
     * @param string $documentType
     * @param string $fileName
     * @param string|UploadedFile $content
     * @param string $version
     * @return string Version file path
     */
    protected function createDocumentVersion($tenantId, $documentType, $fileName, $content, $version)
    {
        $versionFileName = pathinfo($fileName, PATHINFO_FILENAME) . "_v{$version}." . pathinfo($fileName, PATHINFO_EXTENSION);
        $path = "tenants/{$tenantId}/documents/{$documentType}/versions/{$versionFileName}";

        // Handle file upload
        if ($content instanceof UploadedFile) {
            Storage::putFileAs(dirname($path), $content, basename($path));
            return $path;
        }

        // Handle content as string (generate PDF)
        if (is_string($content) && !empty($content)) {
            $metadata = [
                'title' => pathinfo($versionFileName, PATHINFO_FILENAME),
                'version' => $version,
                'document_type' => $documentType,
                'status' => 'active'
            ];

            $pdf = $this->pdfService->generateDocument($content, $metadata);
            Storage::put($path, $pdf);
            return $path;
        }

        // If no content and no file, create empty file
        Storage::put($path, '');
        return $path;
    }

    /**
     * Get document file path
     *
     * @param string $tenantId
     * @param string $documentType
     * @param string $fileName
     * @param string $status
     * @return string|null
     */
    protected function getDocumentPath($tenantId, $documentType, $fileName, $status = 'active')
    {
        $path = "tenants/{$tenantId}/documents/{$documentType}/{$status}/{$fileName}";
        return Storage::exists($path) ? $path : null;
    }

    /**
     * Generate unique file name
     *
     * @param string $documentNumber
     * @param string $title
     * @return string
     */
    protected function generateFileName($documentNumber, $title)
    {
        return $documentNumber . '.pdf';
    }

    /**
     * Archive a document
     *
     * @param string $tenantId
     * @param string $documentType
     * @param string $fileName
     * @return bool
     */
    protected function archiveDocument($tenantId, $documentType, $fileName)
    {
        $sourcePath = "tenants/{$tenantId}/documents/{$documentType}/active/{$fileName}";
        $targetPath = "tenants/{$tenantId}/documents/{$documentType}/archived/{$fileName}";
        
        if (Storage::exists($sourcePath)) {
            Storage::move($sourcePath, $targetPath);
            return true;
        }
        
        return false;
    }

    /**
     * Get document content
     *
     * @param string $tenantId
     * @param string $documentType
     * @param string $fileName
     * @param string $status active|archived|drafts
     * @return string|null
     */
    protected function getDocument($tenantId, $documentType, $fileName, $status = 'active')
    {
        $path = "tenants/{$tenantId}/documents/{$documentType}/{$status}/{$fileName}";
        return Storage::exists($path) ? Storage::get($path) : null;
    }

    /**
     * Get all versions of a document
     *
     * @param string $tenantId
     * @param string $documentType
     * @param string $fileName
     * @return array
     */
    protected function getDocumentVersions($tenantId, $documentType, $fileName)
    {
        $baseName = pathinfo($fileName, PATHINFO_FILENAME);
        $extension = pathinfo($fileName, PATHINFO_EXTENSION);
        $pattern = "tenants/{$tenantId}/documents/{$documentType}/versions/{$baseName}_v*{$extension}";
        
        return collect(Storage::files(dirname($pattern)))
            ->filter(function($file) use ($baseName) {
                return str_starts_with(basename($file), $baseName . '_v');
            })
            ->map(function($file) {
                return [
                    'path' => $file,
                    'version' => $this->extractVersionNumber($file),
                    'created_at' => Storage::lastModified($file)
                ];
            })
            ->sortByDesc('version')
            ->values()
            ->all();
    }

    /**
     * Extract version number from file name
     *
     * @param string $filePath
     * @return string
     */
    private function extractVersionNumber($filePath)
    {
        preg_match('/_v(\d+(?:\.\d+)?)\./', $filePath, $matches);
        return $matches[1] ?? '1.0';
    }

    /**
     * Delete a document and all its versions
     *
     * @param string $tenantId
     * @param string $documentType
     * @param string $fileName
     * @return bool
     */
    protected function deleteDocument($tenantId, $documentType, $fileName)
    {
        $statuses = ['active', 'archived', 'drafts'];
        $deleted = false;

        // Delete from all status folders
        foreach ($statuses as $status) {
            $path = "tenants/{$tenantId}/documents/{$documentType}/{$status}/{$fileName}";
            if (Storage::exists($path)) {
                Storage::delete($path);
                $deleted = true;
            }
        }

        // Delete all versions
        $baseName = pathinfo($fileName, PATHINFO_FILENAME);
        $extension = pathinfo($fileName, PATHINFO_EXTENSION);
        $pattern = "tenants/{$tenantId}/documents/{$documentType}/versions/{$baseName}_v*{$extension}";
        
        foreach (Storage::files(dirname($pattern)) as $file) {
            if (str_starts_with(basename($file), $baseName . '_v')) {
                Storage::delete($file);
                $deleted = true;
            }
        }

        return $deleted;
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
