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
    public function index(Request $request)
    {
        // Validate request parameters
        $validator = Validator::make($request->all(), [
            'document_type' => 'nullable|string',
            'related_process' => 'nullable|string',
            'category_id' => 'nullable|integer',
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

        // Process with DataTables
        return DataTables::of($query)
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
            ->addColumn('action', function ($document) {
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
                    $actions .= '<a href="' . route('tenant.document.edit', $encryptedId) . '" 
                                class="btn btn-sm btn-icon btn-light-primary ms-1" 
                                data-bs-toggle="tooltip" 
                                data-bs-placement="top" 
                                title="' . __('Edit') . '">
                                <i class="ti ti-edit"></i>
                            </a>';
                }
                
                // Download action
                if (tenant_can('Download Documents') && $document->lastVersion) {
                    $actions .= '<a href="' . route('tenant.document.download', $encryptedId) . '" 
                                class="btn btn-sm btn-icon btn-light-success ms-1" 
                                data-bs-toggle="tooltip" 
                                data-bs-placement="top" 
                                title="' . __('Download') . '">
                                <i class="ti ti-download"></i>
                            </a>';
                }
                
                // Delete action
                if (tenant_can('Delete Documents')) {
                    $actions .= '<button type="button" 
                                class="btn btn-sm btn-icon btn-light-danger ms-1 delete-document" 
                                data-id="' . $encryptedId . '" 
                                data-bs-toggle="tooltip" 
                                data-bs-placement="top" 
                                title="' . __('Delete') . '">
                                <i class="ti ti-trash"></i>
                            </button>';
                }
                
                return $actions;
            })
            ->rawColumns(['status_badge', 'action'])
            ->make(true);
    }
}
