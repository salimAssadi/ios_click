<?php

namespace App\Http\Controllers\DocumentManagement;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\DocumentVersion;
use App\Models\DocumentApproval;
use App\Models\DocumentArchive;
use App\Services\DocumentStorageService;
use App\Services\PdfGenerationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;

class DocumentController extends Controller
{
    protected $documentStorage;
    protected $pdfService;

    public function __construct(
        DocumentStorageService $documentStorage,
        PdfGenerationService $pdfService
    ) {
        $this->documentStorage = $documentStorage;
        $this->pdfService = $pdfService;
    }

    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'category_id' => 'required|integer',
            'description' => 'nullable|string',
            'content' => 'required|string', // TinyMCE content
            'type' => 'required|string|in:procedures,samples',
            'reference_id' => 'nullable|string',
            'iso_system_id' => 'nullable|integer'
        ]);

        DB::beginTransaction();
        try {
            $document = Document::create([
                'name' => $request->name,
                'category_id' => $request->category_id,
                'sub_category_id' => $request->sub_category_id,
                'description' => $request->description,
                'reference_id' => $request->reference_id,
                'iso_system_id' => $request->iso_system_id,
                'created_by' => Auth::id(),
                'type' => $request->type
            ]);

            // Create initial version
            $version = DocumentVersion::create([
                'document_id' => $document->id,
                'version_number' => '1.0',
                'changes_description' => 'Initial version',
                'created_by' => Auth::id(),
                'status' => 'draft',
                'is_current' => true
            ]);

            // Prepare data for PDF generation
            $data = [
                'document' => $document,
                'content' => $request->content,
                'iso_system' => $document->isoSystem,
                'created_by' => Auth::user(),
                'version_number' => '1.0',
                'reference_id' => $document->reference_id,
                'category' => $document->category,
                'sub_category' => $document->subCategory
            ];

            // Generate PDF using template
            $pdfPath = $this->pdfService->generateVersionedPdf(
                $data,
                $version,
                $this->documentStorage->getDocumentPath($document, $request->type)
            );

            // Update version with file path
            $version->update(['file_path' => $pdfPath]);

            DB::commit();
            return response()->json([
                'message' => 'Document created successfully',
                'document' => $document,
                'version' => $version
            ], 201);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => 'Error creating document', 'error' => $e->getMessage()], 500);
        }
    }

    public function preview($id)
    {
        try {
            $id = Crypt::decrypt($id);
            $version = DocumentVersion::with(['document', 'document.isoSystem'])->findOrFail($id);
            
            // Prepare data for preview
            $data = [
                'document' => $version->document,
                'content' => $version->content,
                'iso_system' => $version->document->isoSystem,
                'created_by' => $version->creator,
                'version_number' => $version->version_number,
                'reference_id' => $version->document->reference_id,
                'category' => $version->document->category,
                'sub_category' => $version->document->subCategory,
                'approval' => $version->approval
            ];

            // Generate PDF for preview
            $pdf = $this->pdfService->generatePdf($data);

            return response($pdf->output(), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $version->document->name . '.pdf"'
            ]);
        } catch (DecryptException $e) {
            return response()->json(['message' => 'Invalid or corrupted ID'], 400);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error generating preview', 'error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $id = Crypt::decrypt($id);
            $document = Document::findOrFail($id);

            $request->validate([
                'name' => 'string',
                'category_id' => 'integer',
                'description' => 'nullable|string',
                'content' => 'required|string',
                'changes_description' => 'required|string',
            ]);

            DB::beginTransaction();
            try {
                $document->update($request->only([
                    'name', 
                    'category_id', 
                    'sub_category_id', 
                    'description'
                ]));

                // Get latest version and mark it as non-current
                $latestVersion = $document->versions()->latest()->first();
                $latestVersion->update(['is_current' => false]);

                // Create new version
                $newVersionNumber = $this->incrementVersionNumber($latestVersion->version_number);
                $newVersion = DocumentVersion::create([
                    'document_id' => $document->id,
                    'version_number' => $newVersionNumber,
                    'changes_description' => $request->changes_description,
                    'created_by' => Auth::id(),
                    'status' => 'draft',
                    'is_current' => true
                ]);

                // Prepare data for PDF generation
                $data = [
                    'document' => $document,
                    'content' => $request->content,
                    'iso_system' => $document->isoSystem,
                    'created_by' => Auth::user(),
                    'version_number' => $newVersionNumber,
                    'reference_id' => $document->reference_id,
                    'category' => $document->category,
                    'sub_category' => $document->subCategory
                ];

                // Generate new PDF version
                $pdfPath = $this->pdfService->generateVersionedPdf(
                    $data,
                    $newVersion,
                    $this->documentStorage->getDocumentPath($document, $document->type)
                );

                // Update version with file path
                $newVersion->update(['file_path' => $pdfPath]);

                DB::commit();
                return response()->json([
                    'message' => 'Document updated successfully',
                    'document' => $document,
                    'version' => $newVersion
                ]);
            } catch (\Exception $e) {
                DB::rollback();
                throw $e;
            }
        } catch (DecryptException $e) {
            return response()->json(['message' => 'Invalid or corrupted ID'], 400);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error updating document', 'error' => $e->getMessage()], 500);
        }
    }

    public function submitForApproval(DocumentVersion $version)
    {
        if ($version->status !== 'draft') {
            return response()->json(['message' => 'Only draft versions can be submitted for approval'], 400);
        }

        $version->update(['status' => 'under_review']);

        // Create approval requests for all approvers
        // This is a placeholder - you'll need to implement your approval workflow
        $approvers = [1, 2, 3]; // Replace with actual approver IDs from your system
        foreach ($approvers as $approverId) {
            DocumentApproval::create([
                'document_version_id' => $version->id,
                'approver_id' => $approverId,
                'status' => 'pending'
            ]);
        }

        return response()->json(['message' => 'Document submitted for approval']);
    }

    public function approve(Request $request, DocumentVersion $version)
    {
        $approval = $version->approvals()
            ->where('approver_id', Auth::id())
            ->firstOrFail();

        if ($approval->status !== 'pending') {
            return response()->json(['message' => 'This approval request has already been processed'], 400);
        }

        $approval->update([
            'status' => 'approved',
            'comments' => $request->comments,
            'approved_at' => now()
        ]);

        // Check if all approvals are complete
        if (!$version->approvals()->where('status', 'pending')->exists()) {
            $version->update(['status' => 'approved']);
            
            // Regenerate PDF with signature
            $document = $version->document;
            $pdfPath = $this->pdfService->generateVersionedPdf(
                [
                    'document' => $document,
                    'content' => $document->content,
                    'iso_system' => $document->isoSystem,
                    'created_by' => Auth::user(),
                    'version_number' => $version->version_number,
                    'reference_id' => $document->reference_id,
                    'category' => $document->category,
                    'sub_category' => $document->subCategory
                ],
                $version,
                $this->documentStorage->getDocumentPath($document, $document->type)
            );
            
            $version->update(['file_path' => $pdfPath]);
        }

        return response()->json(['message' => 'Document version approved successfully']);
    }

    public function archive(Request $request, Document $document)
    {
        $request->validate([
            'archive_reason' => 'required|string'
        ]);

        DB::beginTransaction();
        try {
            DocumentArchive::create([
                'document_id' => $document->id,
                'archive_reason' => $request->archive_reason,
                'archived_by' => Auth::id(),
                'archived_at' => now(),
                'document_data' => $document->toJson()
            ]);

            $document->update(['status' => 'archived']);
            
            DB::commit();
            return response()->json(['message' => 'Document archived successfully']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => 'Error archiving document', 'error' => $e->getMessage()], 500);
        }
    }

    private function incrementVersionNumber($currentVersion)
    {
        list($major, $minor) = explode('.', $currentVersion);
        return $major . '.' . ((int)$minor + 1);
    }
}
