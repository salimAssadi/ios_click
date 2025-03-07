<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Omaralalwi\Gpdf\Enums\GpdfDefaultSupportedFonts;
use Omaralalwi\Gpdf\Enums\GpdfSettingKeys;
use Omaralalwi\Gpdf\Facade\Gpdf;
use Omaralalwi\Gpdf\GpdfConfig;

class PdfController extends Controller
{
    protected $config;

    public function generatePdf()
    {
        // Define the custom header HTML
        $header = '
        <div class="header">
            <p dir="rtl" class="Header" style="margin-right:18pt; text-indent:-18pt;">&nbsp;</p>
            <div dir="rtl">
                <table style="width:529.6pt; margin-right:auto; margin-left:auto; border:6pt solid #c00000; padding:0pt; border-collapse:collapse;">
                    <tbody>
                        <tr style="height:78.5pt;">
                            <td style="width:129.25pt; border-left:6pt solid #c00000; border-bottom:6pt solid #c00000; padding:0pt 2.4pt; vertical-align:middle;">
                                <p dir="rtl" style="text-align:center;"><strong><span style="font-family:\'PT Bold Heading\'; font-size:18pt;">إجــــراء</span></strong></p>
                                <p dir="ltr" style="text-align:center; font-size:18pt;"><strong><span style="font-size:14pt;">ضبط المعلومات الموثقة</span></strong><span dir="ltr">&nbsp;</span><strong>ASD-P-IMS-01</strong></p>
                            </td>
                            <td rowspan="2" style="width:145.95pt; border-right:6pt solid #c00000; border-left:6pt solid #c00000; border-bottom:6pt solid #c00000; padding:0pt 2.4pt; vertical-align:middle;">
                                <p dir="rtl" class="Header" style="text-align:center; line-height:115%; font-size:14pt;"><strong><span style="font-family:Arial;">إصدار / مراجعة: 1/0</span></strong></p>
                                <p dir="rtl" class="Header" style="text-align:center; line-height:115%; font-size:14pt;"><strong><span style="font-family:Arial;">تاريـخ الإصـدار: </span></strong><span style="line-height:115%; font-family:Arial; font-size:12pt; font-weight:bold;" dir="ltr">01/02/2025</span></p>
                                <p dir="rtl" class="Header" style="line-height:115%; font-size:14pt;"><strong><span style="font-family:Arial;">&nbsp;</span></strong><strong><span style="font-family:Arial;">تاريـخ المراجعة:</span></strong></p>
                                <p dir="rtl" style="text-align:center; line-height:115%; font-size:14pt;"><strong><span style="font-family:Arial;">صفحـــة رقـــم: </span></strong><span style="font-family:Arial;" dir="ltr">1</span></p>
                            </td>
                            <td style="width:216pt; border-right:6pt solid #c00000; border-bottom:6pt solid #c00000; padding:0pt 2.4pt; vertical-align:middle;">
                                <p dir="rtl" style="text-align:center; font-size:20pt;"><img src="http://127.0.0.1:8000/storage/upload/logo//logo.png" alt="" style="max-width: 100%; height: auto;"></p>
                            </td>
                        </tr>
                        <tr style="height:31pt;">
                            <td style="width:129.25pt; border-top:6pt solid #c00000; border-left:6pt solid #c00000; padding:0pt 2.4pt; vertical-align:middle;">
                                <p dir="rtl" style="text-align:center; font-size:16pt;"><strong><span style="font-family:\'AL-Sarem Bold\'; font-size:14pt;">قسم الجودة</span></strong></p>
                            </td>
                            <td style="width:216pt; border-top:6pt solid #c00000; border-right:6pt solid #c00000; padding:0pt 2.4pt; vertical-align:middle;">
                                <p dir="rtl" class="Header" style="text-align:center; font-size:16pt;"><span dir="ltr"><strong>ISO</strong><strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong><strong>9001: 2015</strong></span></p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <p dir="rtl" class="Header"><span dir="ltr">&nbsp;</span></p>
        </div>
        ';
        $this->config = new GpdfConfig([
            GpdfSettingKeys::FONT_DIR => realpath(__DIR__ . '/assets/fonts/'),
            GpdfSettingKeys::FONT_CACHE => realpath(__DIR__ . '/assets/fonts/'),
            GpdfSettingKeys::DEFAULT_FONT => GpdfDefaultSupportedFonts::DEJAVU_SANS,
            GpdfSettingKeys::IS_JAVASCRIPT_ENABLED => true,
            GpdfSettingKeys::SHOW_NUMBERS_AS_HINDI => false,
        ]);
        // Pass the header and other data to the view
        $html = view('procedure-template.first-template', ['header' => $header])->render();
        // $gpdf = new Gpdf($this->config);
        // $pdfFile = $gpdf->generateWithStream($html);
        // return response($pdfFile, 200, ['Content-Type' => 'application/pdf']);
    
        // $gpdf->generateWithStream($html, 'test-streamed-pdf', true);
        // return response(null, 200, ['Content-Type' => 'application/pdf']);
        // Create a PDF instance
        $pdf = Pdf::loadHTML($html);

        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('document.pdf');
    }
}