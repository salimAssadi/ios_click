<?php

namespace Modules\Document\Http\Controllers;

use App\Traits\TenantFileManager;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Modules\Document\Entities\Document;
use Modules\Document\Entities\DocumentVersion;
use Modules\Document\Entities\IsoInstruction;
use Modules\Document\Entities\IsoPolicy;
use Modules\Document\Entities\IsoSystem;
use Modules\Document\Entities\IsoSystemProcedure;
use Modules\Document\Entities\Procedure;
use Modules\Document\Entities\Sample;
use Yajra\DataTables\Facades\Datatables;
use Modules\Tenant\Models\Setting;

class DocumentController extends Controller
{
    use TenantFileManager;

    public function index()
    {
        return view('document::document.index');
    }

    public function create()
    {
        $isoSystems = IsoSystem::where('status', true)->get();
        return view('document::document.create-document', compact('isoSystems'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'document_number' => 'nullable|string|max:50',
            'department' => 'required|string',
            'version' => 'required|string',
            'content' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:10240', // 10MB max
            'document_type' => 'required|in:procedure,policy,instruction,sample,custom',

        ]);

        $request->merge([
            'document_number' => Str::uuid()->toString(),
        ]);

        try {
            DB::beginTransaction();

            // Create document record
            $document = Document::create([
                'title' => $request->title,
                'document_type' => $request->document_type,
                'document_number' => $request->document_number,
                'related_process' => $request->related_process,
                'department' => $request->department,
                'created_by' => auth('tenant')->id(),
                'creation_date' => now(),
            ]);
            $document->save();

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
                'status' => 'draft',
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
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => __('Error creating document: ') . $e->getMessage(),
            ], 500);
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
            $template = IsoSystemProcedure::with(['procedure'])->find($templateId);
            $template->name = $template->procedure->procedure_name_ar;
            $template->number = $template->procedure->procedure_number;
            $template->version = $template->procedure->version;
            $template->content = $template->procedure->content;
        } elseif ($documentType == 'policy') {
            $template = IsoPolicy::find($templateId);
            $template->name = $template->name;
            $template->number = $template->number;
            $template->version = $template->version;
            $template->content = $template->content;
        } elseif ($documentType == 'sample') {
            $template = Sample::find($templateId);
            $template->name = $template->sample_name_ar;
            $template->number = $template->number;
            $template->version = $template->version;
            $template->content = $template->content;
        } elseif ($documentType == 'instruction') {
            $template = IsoInstruction::find($templateId);
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
                'name' => $template->name,
                'number' => $template->number ?? '',
                'version' => $template->version ?? '1.0',
                'content' => $template->content ?? '',
            ],
        ]);
    }

    public function list(Request $request)
    {
        try {
            $query = Document::query()
                ->with(['creator', 'lastVersion'])
                ->when($request->document_type, function ($q) use ($request) {
                    return $q->byType($request->document_type);
                })
                ->when($request->status, function ($q) use ($request) {
                    return $q->byStatus($request->status);
                });

            return Datatables::of($query)
                ->addColumn('version_badge', function ($document) {
                    $version = $document->lastVersion->version ?? '1.0';
                    return '<span class="badge bg-info">v' . $version . '</span>';
                })
                ->addColumn('status_badge', function ($document) {
                    $status = $document->lastVersion->status ?? 'draft';
                    $colors = [
                        'draft' => 'bg-warning',
                        'active' => 'bg-success',
                        'archived' => 'bg-secondary',
                    ];
                    $color = $colors[$status] ?? 'bg-info';
                    return '<span class="badge ' . $color . '">' . ucfirst($status) . '</span>';
                })
                ->addColumn('preview_url', function ($document) {
                    return route('tenant.document.preview', $document);
                })
                ->addColumn('download_url', function ($document) {
                    return route('tenant.document.download', $document);
                })
                ->rawColumns(['version_badge', 'status_badge'])
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
                'message' => __('Documents have already been imported')
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
            Setting::updateOrCreate(['name' => 'import_dictionary'], ['value' => 1] , ['parent_id' => '1']);
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

    public function preview($id)
    {
        $document = Document::with(['creator', 'lastVersion'])->findOrFail($id);
        return view('document::document.show', compact('document'));
    }

    public function serveFile($id, Request $request)
    {
        $document = Document::with(['lastVersion'])->findOrFail($id);
        
        // Get the specific version if requested, otherwise use the latest
        if ($request->has('version')) {
            $version = $document->versions()->findOrFail($request->version);
        } else {
            $version = $document->lastVersion;
        }

        if (!$version) {
            return response()->json(['error' => 'No version found'], 404);
        }

        // Check if user has permission to access this document
        if (!auth('tenant')->check()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            $filePath = $version->file_path;
          
            // Check if file exists
            if (!Storage::disk('tenants')->exists($filePath)) {
                return response()->json([
                    'error' => 'File not found',
                    'path' => $filePath
                ], 404);
            }

            // For preview (inline display)
            if ($request->get('preview', false)) {
                return response()->file(
                    Storage::disk('tenants')->path($filePath),
                    [
                        'Content-Type' => Storage::disk('tenants')->mimeType($filePath),
                        'Content-Disposition' => 'inline; filename="' . basename($filePath) . '"'
                    ]
                );
            }

            // For download
            return Storage::disk('tenants')->download($filePath);
            
        } catch (\Exception $e) {
            \Log::error('File Access Error', [
                'error' => $e->getMessage(),
                'file_path' => $filePath ?? null
            ]);
            
            return response()->json([
                'error' => 'Error accessing file',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function download($id)
    {
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
        $document = Document::with(['creator', 'lastVersion', 'versions' => function($query) {
            $query->orderBy('created_at', 'desc');
        }])->findOrFail($id);
        return view('document::document.show', compact('document'));
    }

    public function edit($id)
    {
        // TODO: Edit document implementation
    }

    public function update(Request $request, $id)
    {
        // TODO: Update document implementation
    }

    public function destroy($id)
    {
        // TODO: Delete document implementation
    }
}
