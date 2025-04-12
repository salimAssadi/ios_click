<?php

namespace App\Services;

use Meneses\LaravelMpdf\Facades\LaravelMpdf;
use App\Models\DocumentVersion;
use Illuminate\Support\Facades\Storage;

class PdfGenerationService
{
    public function generatePdf($data, $template = 'template.procedures.procedure_template', $watermark = null)
    {
        $mpdf = LaravelMpdf::loadView($template, $data);
        
        // Add watermark for old versions
        if ($watermark) {
            $mpdf->SetWatermarkText($watermark);
            $mpdf->showWatermarkText = true;
            $mpdf->watermarkTextAlpha = 0.1;
        }

        // Add electronic signature if available
        if (isset($data['approval']) && $data['approval']->status === 'approved') {
            $this->addSignature($mpdf, $data['approval']);
        }

        return $mpdf;
    }

    protected function addSignature($mpdf, $approval)
    {
        $approver = $approval->approver;
        
        // Create signature block
        $signatureHtml = '
            <div style="position: absolute; bottom: 50px; right: 30px; width: 200px; text-align: center;">
                <div style="border-bottom: 1px solid #000; margin-bottom: 5px;">
                    <img src="' . ($approver->signature_path ?? '') . '" style="max-width: 150px; max-height: 60px;">
                </div>
                <p style="margin: 5px 0; font-size: 12px;">Approved By: ' . $approver->name . '</p>
                <p style="margin: 5px 0; font-size: 12px;">Date: ' . $approval->approved_at->format('Y-m-d H:i') . '</p>
                <p style="margin: 5px 0; font-size: 10px;">Document ID: ' . $approval->document_version_id . '</p>
            </div>
        ';

        // Add signature block to the last page
        $mpdf->WriteHTML($signatureHtml);
    }

    public function generateVersionedPdf($data, DocumentVersion $version, $basePath)
    {
        $watermark = null;
        
        // Add watermark for non-current versions
        if (!$version->is_current) {
            $watermark = 'SUPERSEDED VERSION ' . $version->version_number;
        }

        // Add version information to the data
        $data['version'] = $version;
        $data['watermark'] = $watermark;

        // Get the appropriate template based on document type
        $template = $this->getTemplateForDocumentType($version->document);

        $mpdf = $this->generatePdf($data, $template, $watermark);

        // Generate filename
        $filename = sprintf(
            '%s_v%s_%s.pdf',
            \Str::slug($version->document->name),
            str_replace('.', '-', $version->version_number),
            time()
        );

        // Save to storage
        $relativePath = $basePath . '/' . $filename;
        Storage::put($relativePath, $mpdf->output());

        return $relativePath;
    }

    private function getTemplateForDocumentType($document)
    {
        // You can customize this based on your document types
        $templates = [
            'procedures' => 'template.procedures.procedure_template',
            'samples' => 'template.procedures.sample_template',
            // Add more templates as needed
        ];

        return $templates[$document->type] ?? 'template.procedures.procedure_template';
    }
}
