<?php

namespace Modules\Document\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Document\Entities\Document;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class DocumentDatatableController extends Controller
{
    /**
     * Process datatable AJAX request for documents
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * Get the appropriate edit URL for a document based on its type
     *
     * @param Document $document The document model
     * @param string $defaultUrl The default URL to use if no specific match is found
     * @return string The URL for editing this document
     */
    protected function getDocumentEditUrl($document, $defaultUrl)
    {
        $editRoutes = [
            'Modules\Document\Entities\IsoSystemProcedure' => 'tenant.document.procedures.configure',
            'Modules\Document\Entities\SupportingDocument' => 'tenant.document.supporting.edit',
            'Modules\Document\Entities\Procedure' => 'tenant.document.procedures.edit',
            // 'Modules\Document\Entities\Form' => 'tenant.document.forms.edit',
        ];

        if($document->document_type === 'supporting') {
            return route('tenant.document.supporting-documents.edit', $document->id);
        }

        if ($document->documentable_type && $document->documentable_id) {
            $category_id = $document->category_id;
            
            if ($category_id && strpos($document->documentable_type, 'IsoSystemProcedure') !== false) {
                if (isset($category_id)) {
                    return route('tenant.document.procedures.edit', [encrypt($document->documentable_id),encrypt($document->category_id)]);
                }
            }
            
            if (isset($editRoutes[$document->documentable_type])) {
                return route($editRoutes[$document->documentable_type], $document->documentable_id);
            }
            
            foreach ($editRoutes as $type => $routeName) {
                if (strpos($document->documentable_type, basename(str_replace('\\', '\\', $type))) !== false) {
                    return route($routeName, $document->documentable_id);
                }
            }
        }
        
        return $defaultUrl;
    }

    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'document_type' => 'nullable|string',
            'related_process' => 'nullable|string',
            'category_id' => 'nullable|integer',
            'custom_columns' => 'nullable|array',
            'custom_columns.*.data' => 'required_with:custom_columns|string',
            'custom_columns.*.title' => 'required_with:custom_columns|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->first()
            ], 422);
        }

        // Check permissions
        if (!tenant_can('View Documents')) {
            return response()->json([
                'error' => __('You do not have permission to view documents')
            ], 403);
        }

        // Start query builder
        $query = Document::with(['status', 'category', 'creator', 'lastVersion']);

        // Apply filters
        if ($request->filled('document_type')) {
            $query->where('document_type', $request->document_type);
        }

        if ($request->filled('related_process')) {
            // Get the class name from the frontend
            $relatedProcess = $request->related_process;
            
            // Convert from format like "ModulesDocumentEntitiesIsoSystemProcedure"
            // to "Modules\Document\Entities\IsoSystemProcedure"
            $className = preg_replace('/Modules(\w+)Entities(\w+)/', 'Modules\\\\$1\\\\Entities\\\\$2', $relatedProcess);
            
            // Use both formats in the query to be safe
            $query->where(function($q) use ($relatedProcess, $className) {
                $q->where('documentable_type', $relatedProcess)
                  ->orWhere('documentable_type', $className);
            });
            
            // Log for debugging purposes
            \Log::info('Document search by class:', [
                'original' => $relatedProcess,
                'converted' => $className
            ]);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // إعداد DataTables
        $dataTables = DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('title', function ($document) {
                return $document->title;
            })
            ->addColumn('category', function ($document) {
                return $document->category ? $document->category->title : '-';
            })
            ->addColumn('status_badge', function ($document) {
                return $document->status_badge;
            })
            ->addColumn('version', function ($document) {
                return $document->lastVersion?->version;
            });
        
        // إضافة الأعمدة المخصصة إذا وجدت
        if ($request->filled('custom_columns') && is_array($request->custom_columns)) {
            foreach ($request->custom_columns as $column) {
                if (isset($column['data'])) {
                    $dataTables->addColumn($column['data'], function ($document) use ($column) {
                        // محاولة الحصول على البيانات من المستند أو العلاقات المرتبطة
                        $field = $column['data'];
                        $value = null;
                        
                        // فحص المستند نفسه
                        if (isset($document->$field)) {
                            $value = $document->$field;
                        } 
                        // فحص متغيرات متداخلة مثل documentable.fieldname
                        elseif (str_contains($field, '.')) {
                            $parts = explode('.', $field);
                            $property = $document;
                            foreach ($parts as $part) {
                                if (is_object($property) && isset($property->$part)) {
                                    $property = $property->$part;
                                } else {
                                    $property = null;
                                    break;
                                }
                            }
                            $value = $property;
                        }
                        // فحص الإصدار الأخير
                        elseif ($document->lastVersion && isset($document->lastVersion->$field)) {
                            $value = $document->lastVersion->$field;
                        }
                        // فحص المستندات المرتبطة
                        elseif ($document->documentable && isset($document->documentable->$field)) {
                            $value = $document->documentable->$field;
                        }
                        
                        // معالجة واجهة مخصصة إذا كانت محددة
                        if (isset($column['formatter']) && is_callable($column['formatter'])) {
                            return $column['formatter']($value, $document);
                        }
                        
                        // القيمة الافتراضية
                        return $value !== null ? $value : (isset($column['default']) ? $column['default'] : '-');
                    });
                }
            }
        }
        // إضافة عمود الإجراءات
        $dataTables->addColumn('action', function ($document) {
                $encryptedId = encrypt($document->id);
                $actions = '';
                
                // View action
                if (tenant_can('View Documents')) {
                    $actions .= '<a href="' . route('tenant.document.show', $encryptedId) . '" 
                                class="btn btn-sm btn-icon btn-light-info" 
                                data-bs-toggle="tooltip" 
                                data-bs-placement="top" 
                                title="' . __('View') . '">
                                <i class="ti ti-eye"></i>
                            </a>';
                }
                
                // Edit action
                if (tenant_can('Edit Documents')) {
                    // Get the appropriate edit URL using our helper method
                    $defaultUrl = route('tenant.document.edit', $encryptedId);
                    $editUrl = $this->getDocumentEditUrl($document, $defaultUrl);
                    
                    $actions .= '<a href="' . $editUrl . '" 
                                class="btn btn-sm btn-icon btn-light-primary ms-1" 
                                data-bs-toggle="tooltip" 
                                data-bs-placement="top" 
                                title="' . __('Edit') . '">
                                <i class="ti ti-edit"></i>
                            </a>';
                }
                
                // Download action
                // if (tenant_can('Download Document') && $document->lastVersion) {
                    $actions .= '<a href="' . route('tenant.document.serve', $encryptedId) . '" 
                                class="btn btn-sm btn-icon btn-light-success ms-1" 
                                data-bs-toggle="tooltip" 
                                data-bs-placement="top" 
                                title="' . __('Download') . '">
                                <i class="ti ti-download"></i>
                            </a>';
                // }

                // print action
                // if (tenant_can('Download Document') && $document->lastVersion) {
                    $actions .= '<a href="' . route('tenant.document.serve', $encryptedId) . '?preview=1" 
                                class="btn btn-sm btn-icon btn-light-success ms-1" 
                                data-bs-toggle="tooltip" 
                                data-bs-placement="top" 
                                title="' . __('Print') . '">
                                <i class="ti ti-printer"></i>
                            </a>';
                // }
                

                // Delete action
                if (tenant_can('Delete Documents')) {
                   $actions .= '<form class="d-inline" action="' . route('tenant.document.destroy', $encryptedId) . '" method="POST">
                            ' . csrf_field() . '
                            ' . method_field('DELETE') . '
                            <button type="button" 
                                class="btn btn-sm btn-icon btn-light-danger ms-1 confirm_dialog" 
                                data-id="' . $encryptedId . '" 
                                data-title="' . __('Delete Document') . '" 
                                data-message="' . __('Are you sure you want to delete this document?') . '" 
                                data-bs-toggle="tooltip" 
                                data-bs-placement="top" 
                                title="' . __('Delete') . '">
                                <i class="ti ti-trash"></i>
                            </button>
                            </form>';
                }
                
                return $actions;
            });
        
        // تحديد الأعمدة التي تحتوي على HTML
        $rawColumns = ['status_badge', 'action'];
        
        if ($request->filled('custom_columns') && is_array($request->custom_columns)) {
            foreach ($request->custom_columns as $column) {
                if (isset($column['raw']) && $column['raw'] === true && isset($column['data'])) {
                    $rawColumns[] = $column['data'];
                }
            }
        }
        
        return $dataTables->rawColumns($rawColumns)
            ->make(true);
    }

    
}
