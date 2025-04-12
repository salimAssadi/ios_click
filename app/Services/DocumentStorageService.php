<?php

namespace App\Services;

use App\Models\Document;
use App\Models\DocumentVersion;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentStorageService
{
    protected $basePath;
    protected $structure;
    protected $versionsPath;

    public function __construct()
    {
        $this->basePath = config('document-storage.base_path');
        $this->structure = config('document-storage.structure');
        $this->versionsPath = config('document-storage.versions_path');
    }

    public function storeDocument(Document $document, UploadedFile $file, $type = 'procedures')
    {
        if (!isset($this->structure[$type])) {
            throw new \InvalidArgumentException("Invalid document type: {$type}");
        }

        $extension = $file->getClientOriginalExtension();
        if (!in_array($extension, $this->structure[$type]['allowed_extensions'])) {
            throw new \InvalidArgumentException("File type not allowed for {$type}");
        }

        // Create directory structure if it doesn't exist
        $relativePath = $this->getDocumentPath($document, $type);
        Storage::makeDirectory($relativePath);

        // Store the file
        $filename = $this->generateFilename($document, $extension);
        $fullPath = $relativePath . '/' . $filename;
        
        Storage::put($fullPath, file_get_contents($file));

        return $fullPath;
    }

    public function storeVersionFile(DocumentVersion $version, UploadedFile $file)
    {
        $document = $version->document;
        $extension = $file->getClientOriginalExtension();
        
        // Create versions directory
        $relativePath = $this->getVersionPath($document);
        Storage::makeDirectory($relativePath);

        // Store the version file
        $filename = $this->generateVersionFilename($version, $extension);
        $fullPath = $relativePath . '/' . $filename;
        
        Storage::put($fullPath, file_get_contents($file));

        return $fullPath;
    }

    public function getDocumentPath(Document $document, $type)
    {
        $typeConfig = $this->structure[$type];
        $categoryPath = $document->category_id ? '/category_' . $document->category_id : '';
        $subCategoryPath = $document->sub_category_id ? '/subcategory_' . $document->sub_category_id : '';
        
        return $typeConfig['path'] . $categoryPath . $subCategoryPath;
    }

    public function getVersionPath(Document $document)
    {
        $documentPath = $this->getDocumentPath($document, $this->getDocumentType($document));
        return $documentPath . '/' . $this->versionsPath;
    }

    protected function generateFilename(Document $document, $extension)
    {
        return Str::slug($document->name) . '_' . time() . '.' . $extension;
    }

    protected function generateVersionFilename(DocumentVersion $version, $extension)
    {
        return Str::slug($version->document->name) . 
               '_v' . str_replace('.', '-', $version->version_number) . 
               '_' . time() . 
               '.' . $extension;
    }

    protected function getDocumentType(Document $document)
    {
        // You can implement logic here to determine document type based on document attributes
        // For now, we'll default to procedures
        return 'procedures';
    }

    public function getFullPath($relativePath)
    {
        return $this->basePath . '/' . $relativePath;
    }

    public function deleteDocument(Document $document)
    {
        $type = $this->getDocumentType($document);
        $path = $this->getDocumentPath($document, $type);
        Storage::deleteDirectory($path);
    }
}
