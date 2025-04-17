<?php

namespace Modules\Document\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Traits\TenantFileManager;
use Modules\Document\Entities\IsoInstruction;
use Modules\Document\Entities\IsoPolicy;
use Modules\Document\Entities\IsoSystem;
use Modules\Document\Entities\IsoSystemProcedure;
use Modules\Document\Entities\Procedure;
use Modules\Document\Entities\Sample;
use Modules\Document\Entities\Document;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\Datatables;
use Illuminate\Support\Facades\DB;

class DocumentController extends Controller
{
    use TenantFileManager;

    public function index()
    {
        return view('document::index');
    }

    public function create()
    {
        $isoSystems = IsoSystem::where('status', true)->get();
        return view('document::create', compact('isoSystems'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'document_number' => 'required|string|max:50',
            'department' => 'required|string',
            'version' => 'required|string',
            'content' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:10240', // 10MB max
            'document_type' => 'required|in:procedure,policy,instruction,sample,custom',
            'iso_system_id' => 'required|exists:iso_systems,id'
        ]);

        try {
            DB::beginTransaction();

            // Create document record
            $document = Document::create([
                'title' => $request->title,
                'document_number' => $request->document_number,
                'department_id' => 1,
                'status' => 'draft',
                'document_type' => $request->document_type,
                'created_by' => auth('tenant')->id()
            ]);

            // Save the document file
            $fileName = $this->generateFileName($request->document_number, $request->title);
            $content = $request->hasFile('file') ? $request->file('file') : $request->content;

            $filePath = $this->saveDocument(
                auth('tenant')->user()->id,
                $request->document_type,
                $fileName,
                $content,
                'draft'
            );

            // Update document with file path
            $document->file_path = $fileName;
            $document->storage_path = $filePath;
            $document->save();

            // // Create initial version
            // $document->versions()->create([
            //     'version' => $request->version,
            //     'file_path' => $fileName,
            //     'storage_path' => $filePath,
            //     'created_by' => auth('tenant')->id()
            // ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => __('Document created successfully'),
                'data' => $document
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => __('Error creating document: ') . $e->getMessage()
            ], 500);
        }
    }

    public function getTemplates(Request $request)
    {
        $isoSystemId = $request->input('iso_system_id');
        $documentType = $request->input('document_type');

        if ($documentType == 'procedure') {
            $templates = IsoSystemProcedure::with(['procedure'])
                ->where('iso_system_id', $isoSystemId)
                ->get();
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
                ->with(['creator', 'documentVersion'])
                ->when($request->document_type, function($q) use ($request) {
                    return $q->byType($request->document_type);
                })
                ->when($request->status, function($q) use ($request) {
                    return $q->byStatus($request->status);
                });

            return Datatables::of($query)
                ->addColumn('version_badge', function ($document) {
                    $version = $document->documentVersion?->version ?? '1.0';
                    return '<span class="badge bg-info">v' . $version . '</span>';
                })
                ->addColumn('status_badge', function ($document) {
                    $colors = [
                        'draft' => 'bg-warning',
                        'active' => 'bg-success',
                        'archived' => 'bg-secondary'
                    ];
                    $color = $colors[$document->status] ?? 'bg-info';
                    return '<span class="badge ' . $color . '">' . ucfirst($document->status) . '</span>';
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
                'error' => 'Error loading documents: ' . $e->getMessage()
            ], 500);
        }
    }

    public function preview($id)
    {
        $document = Document::findOrFail($id);
        $content = Storage::disk('public')->get($document->storage_path);
        
        return response($content)->header('Content-Type', 'text/html');
    }

    public function download($id)
    {
        $document = Document::findOrFail($id);
        return Storage::disk('public')->download(
            $document->storage_path,
            $document->document_number . ' - ' . $document->title . '.html'
        );
    }

    /**
     * Generate a clean file name from document number and title
     */
    private function generateFileName($documentNumber, $title)
    {
        // Clean and combine document number and title
        $cleanTitle = preg_replace('/[^a-z0-9]+/i', '_', $title);
        $cleanNumber = preg_replace('/[^a-z0-9]+/i', '_', $documentNumber);
        return strtolower("{$cleanNumber}_{$cleanTitle}.html");
    }

    public function show($id)
    {
        // TODO: Show document implementation
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
