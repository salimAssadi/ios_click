<?php

namespace Modules\Document\Http\Controllers;

use App\Traits\TenantFileManager;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Modules\Document\Entities\Document;
use Modules\Document\Entities\DocumentHistoryLog;
use Modules\Document\Entities\DocumentVersion;
use Modules\Document\Entities\IsoInstruction;
use Modules\Document\Entities\IsoPolicy;
use Modules\Document\Entities\IsoSystem;
use Modules\Document\Entities\Procedure;
use Modules\Document\Entities\ProcedureTemplate;
use Modules\Document\Entities\Sample;
use Modules\Document\Entities\Status;
use Modules\Setting\Entities\Department;
use Modules\Setting\Entities\Position; // لإضافة تحويل DOCX إلى HTML قبل PDF
use Modules\Tenant\Models\Setting;
use Yajra\DataTables\Facades\DataTables;

class DocumentController extends Controller
{
    use TenantFileManager;

    public function index()
    {
        if (tenant_can('View Documents')) {
            return view('document::document.index');
        } else {
            return redirect()->back()->with('error', __('You do not have permission to view documents'));
        }
    }

    public function create()
    {
        if (tenant_can('Create Documents')) {
            $isoSystems = IsoSystem::where('status', true)->get();
            $department = Department::get();
            return view('document::document.create-document', compact('isoSystems'));
        } else {
            return redirect()->back()->with('error', __('You do not have permission to create documents'));
        }
    }

    public function store(Request $request)
    {
        if (tenant_can('Create Documents')) {
            $request->validate([
                'title' => 'required|string|max:255',
                'document_number' => 'nullable|string|max:50',
                'department' => 'required|string',
                'version' => 'required|string',
                'content' => 'nullable|string',
                'file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:10240', // 10MB max
                'document_type' => 'required|in:procedure,policy,instruction,sample,custom',
                'procedure_data' => 'nullable|string', // Validar el campo JSON para datos de procedimiento
            ]);

            $request->merge([
                'document_number' => Str::uuid()->toString(),
            ]);

            try {
                DB::beginTransaction();
                // Removing dd() that stops execution
                // dd($request->all());
                
                // Log data for debugging
                \Log::info('Document creation data:', [
                    'all_request' => $request->all(),
                    'procedure_data_exists' => $request->filled('procedure_data'),
                    'procedure_setup_data_exists' => $request->filled('procedure_setup_data')
                ]);
                
                if ($request->document_type === 'procedure' && $request->filled('procedure_setup_data')) {
                    // Validar que el JSON es válido
                    $procedure_id = $request->template_id;
                    
                    \Log::info('Procedure data processing:', [
                        'procedure_id' => $procedure_id,
                        'procedure_setup_data' => $request->procedure_setup_data
                    ]);
                    
                    if (!empty($procedure_id)) {
                        $procedureData = json_decode($request->procedure_setup_data, true);
                        if (json_last_error() !== JSON_ERROR_NONE) {
                            throw new \Exception('Invalid procedure data format: ' . json_last_error_msg());
                        }
                        $procedure = Procedure::where('id', $procedure_id)->first();
                        if (!$procedure) {
                            throw new \Exception('Invalid procedure ID');
                        }
                        
                        // Guardar en ProcedureTemplate
                        $procedureTemplate = ProcedureTemplate::create([
                            'title' => $request->title,
                            'procedure_id' => $procedure_id,
                            'content' => $procedureData,
                            'parent_id' =>1
                        ]);
                        
                        \Log::info('Created procedure template:', [
                            'template_id' => $procedureTemplate->id
                        ]);
                    } else {
                        \Log::warning('Empty procedure_id, not creating template');
                    }
                } else {
                    \Log::info('Not processing procedure data - conditions not met:', [
                        'document_type' => $request->document_type,
                        'has_procedure_setup_data' => $request->filled('procedure_setup_data')
                    ]);
                }
                // Create document record
                $document = Document::create([
                    'title' => $request->title,
                    'document_type' => $request->document_type,
                    'document_number' => $request->document_number,
                    'related_process' => $request->related_process,
                    'status_id' => 11,
                    'department' => $request->department,
                    'created_by' => auth('tenant')->id(),
                    'creation_date' => now(),
                ]);
                $document->save();

                // Si es un procedimiento y tiene datos de configuración, guardarlos en ProcedureTemplate
               

                $fileName = $this->generateFileName($request->document_number, $request->title);
                $content = $request->hasFile('file') ? $request->file('file') : $request->content;

                $filePath = $this->saveDocument(
                    auth('tenant')->user()->id,
                    $request->document_type,
                    $fileName,
                    $content,
                    'draft'
                );
                $request->issue_date = now();
                $validYears = 3;
                $expiryDate = Carbon::parse($request->issue_date)->addYears($validYears);
                $reviewDate = Carbon::parse($request->issue_date)->addYears($validYears - 1);

                $documentVersion = DocumentVersion::create([
                    'document_id' => $document->id,
                    'version' => 1.0,
                    'issue_date' => now(),
                    'expiry_date' => $expiryDate,
                    'review_due_date' => $reviewDate,
                    'status_id' => 17,
                    'storage_path' => $filePath,
                    'file_path' => $filePath,
                    'is_active' => true,
                ]);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => __('Document created successfully'),
                    'data' => $document,
                    'redirect' => route('tenant.document.index'),
                ]);

            } catch (\Exception $e) {
                throw $e;
                DB::rollback();
                return response()->json([
                    'success' => false,
                    'message' => __('Error creating document: ') . $e->getMessage(),
                ], 500);
            }
        } else {
            return redirect()->back()->with('error', __('You do not have permission to create documents'));
        }
    }

    public function getTemplates(Request $request)
    {
        $documentType = $request->input('document_type');

        if ($documentType == 'procedure') {
            $templates = Procedure::get();
            $html = view('document::partials.procedure', compact('templates'))->render();
        } elseif ($documentType == 'policy') {
            $templates = IsoPolicy::get();
            $html = view('document::partials.policy', compact('templates'))->render();
        } elseif ($documentType == 'sample') {
            $templates = Sample::get();
            $html = view('document::partials.sample', compact('templates'))->render();
        } elseif ($documentType == 'instruction') {
            $templates = IsoInstruction::get();
            $html = view('document::partials.instruction', compact('templates'))->render();
        } else {
            $content = '';
            $id = 'custom';
            $html = view('document::partials.custom', compact('content', 'id'))->render();
        }

        if (empty($html)) {
            $html = '<div class="col-12"><div class="alert alert-info">No templates found for this selection.</div></div>';
        }

        return response()->json(['html' => $html]);
    }

    public function getTemplateData(Request $request, $templateId)
    {
        $documentType = $request->input('documentType');

        if ($documentType == 'procedure') {
            $template = Procedure::find($templateId);

            if (!$template) {
                return response()->json([
                    'error' => 'Template not found',
                ], 404);
            }

            // Buscar si existe una plantilla de procedimiento ya creada
            $procedureTemplate = ProcedureTemplate::where('procedure_id', $templateId)->first();

            // Si no existe un registro en ProcedureTemplate, crear datos básicos del template tradicional
            if (!$procedureTemplate) {

                $template->template_id = $template->id;
                $template->name = $template->procedure_name_ar;
                $template->number = $template->procedure_number;
                $template->version = $template->version;
                $template->content = $template->content;

                // Obtener datos del procedimiento en formato estructurado
                $jobRoles = Position::all()->pluck('name_ar', 'id');

                // Renderizar la configuración como antes (solo para compatibilidad)
                $defaultContent = ['content' => [], 'id' => 0];

                $template->config = view('document::partials.procedure_setup', [
                    'procedure' => $template,
                    'jobRoles' => $jobRoles,
                    'purposes' => $defaultContent,
                    'scopes' => $defaultContent,
                    'responsibilities' => $defaultContent,
                    'definitions' => $defaultContent,
                    'forms' => $defaultContent,
                    'procedures' => $defaultContent,
                    'risk_matrix' => (object) $defaultContent,
                    'kpis' => (object) $defaultContent,
                ])->render();
            } else {
                // Si existe un registro, usar directamente los datos almacenados en formato JSON
                $template->template_id = $template->id;
                $template->name = $template->procedure_name_ar;
                $template->number = $template->procedure_number;
                $template->version = $template->version;
                $template->content = $template->content;
                $jobRoles = Position::all()->pluck('title_ar', 'id');

                // Convertir datos de JSON a estructura para la vista
                $contentData = $procedureTemplate->content;
                $template->config = view('document::partials.procedure_setup', [
                    'procedure' => $template,
                    'jobRoles' => $jobRoles,
                    'purposes' => ($contentData['purpose'] ?? []),
                    'scopes' => ($contentData['scope'] ?? []),
                    'responsibilities' => ($contentData['responsibility'] ?? []),
                    'definitions' => ($contentData['definitions'] ?? []),
                    'forms' => ($contentData['forms'] ?? []),
                    'procedures' => ($contentData['procedures'] ?? []),
                    'risk_matrix' => ($contentData['risk_matrix'] ?? []),
                    'kpis' => ($contentData['kpis'] ?? []),
                ])->render();

                // dd($template->config);
                // Usar directamente los datos JSON almacenados como config
            }
        } elseif ($documentType == 'policy') {
            $template = IsoPolicy::find($templateId);
            $template->template_id = $template->id;
            $template->name = $template->name;
            $template->number = $template->number;
            $template->version = $template->version;
            $template->content = $template->content;
        } elseif ($documentType == 'sample') {
            $template = Sample::find($templateId);
            $template->template_id = $template->id;
            $template->name = $template->sample_name_ar;
            $template->number = $template->number;
            $template->version = $template->version;
            $template->content = $template->content;
        } elseif ($documentType == 'instruction') {
            $template = IsoInstruction::find($templateId);
            $template->template_id = $template->id;
            $template->name = $template->name;
            $template->number = $template->number;
            $template->version = $template->version;
            $template->content = $template->content;
        } else {
            $template = null;
        }

        if (!$template) {
            return response()->json([
                'error' => 'Template not found',
            ], 404);
        }

        return response()->json([
            'data' => [
                'template_id' => $template->template_id,
                'name' => $template->name,
                'number' => $template->number ?? '',
                'version' => $template->version ?? '1.0',
                'config' => $template->config ?? '',
                'content' => $template->content ?? '',
            ],
        ]);
    }

    public function list(Request $request)
    {
        try {
            $query = Document::query()
                ->with(['creator', 'lastVersion', 'status'])
                ->when($request->document_type, function ($q) use ($request) {
                    return $q->byType($request->document_type);
                })
                ->when($request->status, function ($q) use ($request) {
                    return $q->byStatus($request->status);
                });

            return DataTables::of($query)
                ->addColumn('version_badge', function ($document) {
                    $version = $document->lastVersion->version ?? '1.0';
                    return '<span class="badge bg-info">v' . $version . '</span>';
                })
                ->addColumn('status_badge', function ($document) {
                    return $document->getStatusBadgeAttribute();
                })
                ->addColumn('actions', function ($document) {
                    $actions = [
                        'Edit Documents' => [
                            'route' => route('tenant.document.edit', encrypt($document->id)),
                            'class' => 'btn-warning',
                            'icon' => 'fas fa-edit',
                            'title' => __('Edit Document'),
                        ],
                        'View Document Details' => [
                            'route' => route('tenant.document.show', encrypt($document->id)),
                            'class' => 'btn-info',
                            'icon' => 'fas fa-info-circle',
                            'title' => __('View Details'),
                        ],
                        'Preview Document' => [
                            'route' => route('tenant.document.serve', ['id' => encrypt($document->id), 'preview' => true]),
                            'class' => 'btn-primary',
                            'icon' => 'fas fa-eye',
                            'title' => __('Preview'),
                        ],
                        'Download Document' => [
                            'route' => route('tenant.document.serve', ['id' => encrypt($document->id)]),
                            'class' => 'btn-success',
                            'icon' => 'fas fa-download',
                            'title' => __('Download'),
                        ],
                    ];

                    $buttons = '<div class="btn-group" role="group">';
                    foreach ($actions as $permission => $action) {
                        if (tenant_can($permission)) {
                            $buttons .= '<a href="' . htmlspecialchars($action['route']) . '"
                                class="btn btn-sm ' . $action['class'] . '"
                                title="' . htmlspecialchars($action['title']) . '">
                                <i class="' . htmlspecialchars($action['icon']) . '"></i>
                            </a>';
                        }
                    }
                    $buttons .= '</div>';

                    return $buttons;
                })
                ->rawColumns(['version_badge', 'status_badge', 'actions'])
                ->make(true);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error loading documents: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Import documents from dictionary
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function importFromDictionary(Request $request)
    {
        // Check if documents already exist
        if (getSettingsValByName('import_dictionary') == 1) {
            return response()->json([
                'success' => false,
                'message' => __('Documents have already been imported'),
            ], 400);
        }

        Document::truncate();
        DocumentVersion::truncate();
        $procedures = Procedure::all();
        DB::beginTransaction();
        try {
            foreach ($procedures as $procedure) {
                $document = Document::create([
                    'title' => $procedure->procedure_name_ar,
                    'document_type' => 'procedure',
                    'document_number' => str()->uuid(),
                    'related_process' => null,
                    'department' => null,
                    'created_by' => auth('tenant')->id(),
                    'creation_date' => now(),
                ]);
                DocumentVersion::create([
                    'document_id' => $document->id,
                    'version' => 1.0,
                    'issue_date' => now(),
                    'expiry_date' => now()->addYears(3),
                    'review_due_date' => now()->addYears(3)->subYear(),
                    'status' => 'draft',
                    'storage_path' => $procedure->template_path,
                    'file_path' => $procedure->template_path,
                    'is_active' => true,
                ]);
                $samples = Sample::where('procedure_id', $procedure->id)->get();

                foreach ($samples as $sample) {
                    $sampleDocument = Document::create([
                        'title' => $sample->sample_name_ar,
                        'document_type' => 'form',
                        'document_number' => str()->uuid(),
                        'related_process' => $procedure->procedure_name_ar,
                        'department' => null,
                        'created_by' => auth('tenant')->id(),
                        'creation_date' => now(),
                    ]);

                    DocumentVersion::create([
                        'document_id' => $sampleDocument->id,
                        'version' => 1.0,
                        'issue_date' => now(),
                        'expiry_date' => now()->addYears(3),
                        'review_due_date' => now()->addYears(3)->subYear(),
                        'status' => 'draft',
                        'storage_path' => $sample->sample_file_path,
                        'file_path' => $sample->sample_file_path,
                        'is_active' => true,
                    ]);
                }
            }
            DB::commit();
            Setting::updateOrCreate(['name' => 'import_dictionary'], ['value' => 1], ['parent_id' => '1']);
            return response()->json([
                'success' => true,
                'message' => __('Documents imported successfully'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => __('Error importing documents: ') . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $id = decrypt($id);
        if (tenant_can('Edit Documents')) {
            $document = Document::with(['lastVersion.status'])->findOrFail($id);
            $statuses = Status::all();
            return view('document::document.edit', compact('document', 'statuses'));
        } else {
            return redirect()->back()->with('error', __('You do not have permission to edit documents'));
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $id = decrypt($id);
        $document = Document::findOrFail($id);

        // Validate request
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'nullable|file|max:10240', // 10MB max
            'status' => 'required|numeric',
            'version_notes' => 'nullable|string',
        ]);

        try {
            \DB::beginTransaction();

            // Update document
            $document->update([
                'title' => $request->title,
                'description' => $request->description,
            ]);

            // Handle file upload if provided
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $filePath = $file->store('documents/' . $document->id, 'tenants');

                // Create new version
                $document->versions()->create([
                    'version' => $document->lastVersion->version + 0.1,
                    'file_path' => $filePath,
                    'status_id' => $request->status,
                    'change_notes' => $request->version_notes,
                    'created_by' => auth('tenant')->id(),
                ]);
            } else {
                // Update existing version status if no new file
                $document->lastVersion->update([
                    'status_id' => $request->status,
                    'change_notes' => $request->version_notes,
                ]);
            }

            \DB::commit();

            return redirect()
                ->route('tenant.document.show', encrypt($document->id))
                ->with('success', __('Document updated successfully'));

        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Document update failed', [
                'error' => $e->getMessage(),
                'document_id' => $id,
                'user_id' => auth('tenant')->id(),
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Securely serve document files with access control
     */
    public function serveFile($id, Request $request)
    {
        $id = decrypt($id);
        try {
            // 1. Get document and version
            $document = Document::with(['lastVersion'])->findOrFail($id);

            // 2. Get specific version if requested
            if ($request->has('version')) {
                $version = $document->versions()->findOrFail($request->version);
            } else {
                $version = $document->lastVersion;
            }

            if (!$version) {
                return response()->json(['error' => 'No version found'], 404);
            }

            // 3. Security checks
            if (!auth('tenant')->check()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            // 4. Check tenant access
            $user = auth('tenant')->user();
            if ($user->tenant_id !== $document->tenant_id) {
                \Log::warning('Unauthorized tenant access attempt', [
                    'user_id' => $user->id,
                    'tenant_id' => $user->tenant_id,
                    'document_id' => $document->id,
                    'document_tenant' => $document->tenant_id,
                ]);
                return response()->json(['error' => 'Access denied'], 403);
            }

            // 5. Rate limiting
            if (!RateLimiter::remaining('file-downloads:' . $user->id, 60)) {
                return response()->json(['error' => 'Too many download attempts. Please try again later.'], 429);
            }
            RateLimiter::hit('file-downloads:' . $user->id);

            // 6. Get file path and validate
            $filePath = $version->file_path;
            if (!Storage::disk('tenants')->exists($filePath)) {
                return response()->json([
                    'error' => 'File not found',
                    'path' => $filePath,
                ], 404);
            }

            // 7. Log access
            \Log::info('Document access', [
                'user_id' => $user->id,
                'tenant_id' => $user->tenant_id,
                'document_id' => $document->id,
                'version_id' => $version->id,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            // 8. Generate temporary URL or serve file
            if ($request->get('preview', false)) {
                // For preview, serve directly with strict headers
                return response()->file(
                    Storage::disk('tenants')->path($filePath),
                    [
                        'Content-Type' => Storage::disk('tenants')->mimeType($filePath),
                        'Content-Disposition' => 'inline; filename="' . basename($filePath) . '"',
                        'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
                        'Pragma' => 'no-cache',
                        'Expires' => '0',
                    ]
                );
            }

            // For download, stream the file
            return Storage::disk('tenants')->download($filePath, basename($filePath), [
                'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
                'Pragma' => 'no-cache',
                'Expires' => '0',
            ]);

        } catch (\Exception $e) {
            \Log::error('File Access Error', [
                'error' => $e->getMessage(),
                'user_id' => auth('tenant')->id(),
                'document_id' => $id,
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'error' => 'Error accessing file',
                'message' => app()->environment('local') ? $e->getMessage() : 'An error occurred',
            ], 500);
        }
    }

    public function download($id)
    {
        $id = decrypt($id);
        return $this->serveFile($id, request());
    }

    /**
     * Generate a clean file name from document number and title
     */
    private function generateFileName($documentNumber, $title)
    {
        // Clean and combine document number and title
        $cleanTitle = preg_replace('/[^a-z0-9]+/i', '_', $title);
        $cleanNumber = preg_replace('/[^a-z0-9]+/i', '_', $documentNumber);
        return strtolower("{$cleanNumber}_{$cleanTitle}.pdf");
    }

    public function show($id)
    {
        $id = decrypt($id);
        $document = Document::with(['creator', 'reviewRequests', 'lastVersion', 'status', 'versions' => function ($query) {
            $query->orderBy('created_at', 'desc')->with('status');
        }])->findOrFail($id);
        $latestStatusRequest = $document->reviewRequests()->latest()->first();
        return view('document::document.show', compact('document', 'latestStatusRequest'));
    }

    public function history(Document $document)
    {

        $history = DocumentHistoryLog::with(['performer', 'version'])
            ->where('document_id', $document->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('document::document.history', compact('document', 'history'));
    }

    public function destroy($id)
    {
        // TODO: Delete document implementation
    }
}
