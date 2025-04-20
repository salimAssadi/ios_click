<?php

namespace Modules\Document\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Document\Entities\Document;
use Modules\Document\Entities\DocumentVersion;
use Yajra\DataTables\Facades\DataTables;

class DocumentVersionController extends Controller
{
    public function index()
    {
        $documents = Document::with(['lastVersion'])->get();
        return view('document::versions.index', compact('documents'));
    }

    public function data(Request $request)
    {
        try {
            $query = DocumentVersion::with(['document', 'creator', 'approver'])
                ->when($request->document_id, function ($q) use ($request) {
                    return $q->where('document_id', $request->document_id);
                })
                ->when($request->status, function ($q) use ($request) {
                    return $q->where('status', $request->status);
                });

            return DataTables::of($query)
                ->addColumn('document_info', function ($version) {
                    return view('document::versions.partials.document_info', compact('version'))->render();
                })
                ->addColumn('version_info', function ($version) {
                    return view('document::versions.partials.version_info', compact('version'))->render();
                })
                ->addColumn('status_badge', function ($version) {
                    $colors = [
                        'draft' => 'bg-warning',
                        'under_review' => 'bg-info',
                        'approved' => 'bg-success',
                        'modified' => 'bg-primary',
                        'obsolete' => 'bg-secondary'
                    ];
                    $color = $colors[$version->status] ?? 'bg-secondary';
                    return '<span class="badge ' . $color . '">' . __(ucfirst($version->status)) . '</span>';
                })
                ->addColumn('dates', function ($version) {
                    return view('document::versions.partials.dates', compact('version'))->render();
                })
                ->addColumn('actions', function ($version) {
                    return view('document::versions.partials.actions', compact('version'))->render();
                })
                ->rawColumns(['document_info', 'version_info', 'status_badge', 'dates', 'actions'])
                ->make(true);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error loading versions: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request, Document $document)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf,doc,docx',
            'change_notes' => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            // Get the latest version and increment
            $latestVersion = $document->versions()->max('version');
            $newVersion = $latestVersion ? $latestVersion + 0.1 : 1.0;

            // Set previous active version to inactive
            $document->versions()->where('is_active', true)->update(['is_active' => false]);

            // Store the file
            $file = $request->file('file');
            $fileName = $document->document_number . '_v' . number_format($newVersion, 1) . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs(
                "tenants/" . auth()->id() . "/documents/{$document->document_type}/active",
                $fileName,
                'public'
            );

            // Calculate dates
            $issueDate = now();
            $validYears = 3;
            $expiryDate = Carbon::parse($issueDate)->addYears($validYears);
            $reviewDate = Carbon::parse($issueDate)->addYears($validYears - 1);

            // Create new version
            $version = DocumentVersion::create([
                'document_id' => $document->id,
                'version' => $newVersion,
                'issue_date' => $issueDate,
                'expiry_date' => $expiryDate,
                'review_due_date' => $reviewDate,
                'status' => 'draft',
                'change_notes' => $request->change_notes,
                'file_path' => $fileName,
                'storage_path' => $filePath,
                'is_active' => true,
                'created_by' => auth()->id()
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => __('New version created successfully'),
                'data' => $version
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => __('Error creating version: ') . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $version = DocumentVersion::with(['document', 'creator', 'approver'])->findOrFail($id);
        return view('document::versions.show', compact('version'));
    }
}
