<?php

namespace App\Services;

use Mpdf\Mpdf;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Storage;

class PdfGenerationService
{
    /**
     * Generate PDF from HTML content
     *
     * @param string $content HTML content
     * @param array $options PDF generation options
     * @return string Generated PDF content
     */
    public function generateFromHtml(string $content, array $options = []): string
    {
        $defaultConfig = [
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_header' => 10,
            'margin_footer' => 10,
            'margin_left' => 15,
            'margin_right' => 15,
            'margin_top' => 40,
            'margin_bottom' => 25,
            'orientation' => 'P'
        ];

        $config = array_merge($defaultConfig, $options);
        $mpdf = new Mpdf($config);

        // Set document properties
        $mpdf->SetTitle($options['title'] ?? 'Document');
        $mpdf->SetAuthor($options['author'] ?? config('app.name'));
        $mpdf->SetCreator(config('app.name'));

        // Add watermark for draft documents
        if (isset($options['status']) && $options['status'] === 'draft') {
            $mpdf->SetWatermarkText('DRAFT');
            $mpdf->showWatermarkText = true;
        }

        // Set RTL if needed
        if (isset($options['rtl']) && $options['rtl']) {
            $mpdf->SetDirectionality('rtl');
        }

        // Add header
        if (isset($options['header'])) {
            $mpdf->SetHTMLHeader($options['header']);
        }

        // Add footer with page numbers
        $mpdf->SetHTMLFooter('
            <div style="text-align: center; font-size: 10px; color: #666;">
                ' . config('app.name') . ' - Page {PAGENO} of {nbpg}
            </div>
        ');

        // Write content
        $mpdf->WriteHTML($content);

        return $mpdf->Output('', 'S');
    }

    /**
     * Generate PDF from a blade view
     *
     * @param string $view View name
     * @param array $data View data
     * @param array $options PDF generation options
     * @return string Generated PDF content
     */
    public function generateFromView(string $view, array $data = [], array $options = []): string
    {
        $html = View::make($view, $data)->render();
        return $this->generateFromHtml($html, $options);
    }

    /**
     * Save PDF to storage
     *
     * @param string $content PDF content
     * @param string $path Storage path
     * @return bool
     */
    public function saveToStorage(string $content, string $path): bool
    {
        return Storage::put($path, $content);
    }

    /**
     * Generate document PDF
     *
     * @param string $content Document content
     * @param array $metadata Document metadata
     * @return string Generated PDF content
     */
    public function generateDocument(string $content, array $metadata): string
    {
        $options = [
            'title' => $metadata['title'] ?? 'Document',
            'author' => $metadata['author'] ?? null,
            'status' => $metadata['status'] ?? 'active',
            'rtl' => $metadata['rtl'] ?? false,
            'header' => '
                <div style="text-align: center; border-bottom: 1px solid #ddd; padding-bottom: 5px;">
                    <h2 style="margin: 0;">' . ($metadata['title'] ?? 'Document') . '</h2>
                    <div style="font-size: 12px; color: #666;">
                        Document #: ' . ($metadata['document_number'] ?? '') . ' | 
                        Version: ' . ($metadata['version'] ?? '1.0') . ' | 
                        Department: ' . ($metadata['department'] ?? '') . '
                    </div>
                </div>
            '
        ];

        return $this->generateFromHtml($content, $options);
    }
}
