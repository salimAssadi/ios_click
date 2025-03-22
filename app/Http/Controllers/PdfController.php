<?php
namespace App\Http\Controllers;

use App\Models\IsoSystemProcedure;
use App\Models\Procedure;
use App\Models\ProcedureTemplate;
use Illuminate\Contracts\Encryption\DecryptException;
// use Illuminate\Http\Request;
// use App\Traits\{HasViewDynamicParams, HasDataGenerator};
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Crypt;
// use Omaralalwi\Gpdf\Gpdf;
// use Omaralalwi\Gpdf\Facade\Gpdf as GpdfFacAde;
// use Omaralalwi\Gpdf\Enums\{GpdfDefaultSettings as GpdfDefault,
//     GpdfSettingKeys as GpdfSet,
//     GpdfDefaultSupportedFonts,
//     GpdfStorageDrivers};
use Illuminate\Support\Facades\Log;
use Meneses\LaravelMpdf\Facades\LaravelMpdf;

class PdfController extends Controller
{
 
    // use HasViewDynamicParams, HasDataGenerator;

   public function generatePdf(){

   }
    public function preview($id)
    {
        try {
            $id = Crypt::decrypt($id); // Decrypt the ID
        } catch (DecryptException $e) {
            return redirect()->back()->with('error', __('Invalid or corrupted ID.'));
        }
        $procedure   = IsoSystemProcedure::with('procedures','isoSystem')->where('id',$id)->first();
        $templateTitles = [
                    Config::get('procedure_templates.purpose_title'),
                    Config::get('procedure_templates.scope_title'),
                    Config::get('procedure_templates.responsibility_title'),
                    Config::get('procedure_templates.definition_title'),
                    Config::get('procedure_templates.forms_title'),
                    Config::get('procedure_templates.procedure_title'),
                    Config::get('procedure_templates.risk_matrix_title'),
                ];

        $procedureTemplates = ProcedureTemplate::whereIn('title', $templateTitles)->get();

        $groupedTemplates = $procedureTemplates->keyBy('title');
        $pageTitle = __('Configure') . ' ' . $procedure->procedure_name;
        $jobRoles = [
            "مدير إدارة",
            "رئيس لجنة الجودة",
            "موظف جودة",
            "مشرف قسم",
            "مدير مشروع",
            "أخصائي تدريب",
            "مسؤول موارد بشرية",
            "مهندس جودة",
            "فني صيانة",
            "مستشار قانوني"
        ];
        // $pdf = PDF::loadView('pdf.document')
        $pdf =  LaravelMpdf::loadView('template.procedure_template', [
            'pageTitle' => $pageTitle,
            'jobRoles' => $jobRoles,
            'purposes' => $groupedTemplates->get(Config::get('procedure_templates.purpose_title')),
            'scopes' => $groupedTemplates->get(Config::get('procedure_templates.scope_title')),
            'responsibilities' => $groupedTemplates->get(Config::get('procedure_templates.responsibility_title')),
            'definitions' => $groupedTemplates->get(Config::get('procedure_templates.definition_title')),
            'forms' => $groupedTemplates->get(Config::get('procedure_templates.forms_title')),
            'procedures' => $groupedTemplates->get(Config::get('procedure_templates.procedure_title')),
            'risk_matrix' => $groupedTemplates->get(Config::get('procedure_templates.risk_matrix_title')),
        ]);
        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="document.pdf"'
        ]);	   
         // return $pdf->download('pdf.pdf');
        // $html = view('procedure-template.first-template', compact('pages'))->render();
        // Config::set('gpdf.'.GpdfSet::DEFAULT_FONT, GpdfDefaultSupportedFonts::ALMARAI);
        // $gpdf = app(Gpdf::class);
        // $gpdf->generateWithStream($html,'test-pdf-files', true);
        // $pdfFile = $gpdf->generate($html);
        // return response($pdfFile, 200, ['Content-Type' => 'application/pdf']);
        // $pdfContent = GpdfFacAde::generate($html);

    }

     /*
     * this is second way depend on injection in service provider. (no need to inject it, it ready injected in GpdfServiceProvider)
     */
    public function generateSecondWayPdf()
    {
        $data = $this->getDynamicParams();

        $html = view('pdf.example-2',$data)->render();

        $gpdf = app(Gpdf::class);
        $pdfFile = $gpdf->generate($html);

        return response($pdfFile, 200, ['Content-Type' => 'application/pdf']);
    }

    /*
     * override some configs
     * change paper size and font  and some configs, for this pdf file only
     */
    public function generateWithCustomInlineConfig()
    {
        $data = $this->getDynamicParams();

        Config::set('gpdf.'.GpdfSet::DEFAULT_PAPER_SIZE, 'a3');
        Config::set('gpdf.'.GpdfSet::DPI, 300);
        Config::set('gpdf.'.GpdfSet::DEFAULT_FONT, GpdfDefaultSupportedFonts::COURIER);

        $html = view('pdf.example-2',$data)->render();

        $gpdf = app(Gpdf::class);
        $pdfFile = $gpdf->generate($html);

        return response($pdfFile, 200, ['Content-Type' => 'application/pdf']);
    }

    public function generateAndStream()
    {
        $data = $this->getDynamicParams();

        $html = view('pdf.example-2',$data)->render();

        $gpdf = app(Gpdf::class);
        $gpdf->generateWithStream($html,'test-pdf-files', true);

        $pdfFile = $gpdf->generate($html); // optional
        return response($pdfFile, 200, ['Content-Type' => 'application/pdf']);
    }

    public function generateAndStore()
    {
        $data = $this->getDynamicParams();
        $html = view('pdf.example-2',$data)->render();
        $gpdf = app(Gpdf::class);
        $file = $gpdf->generateWithStore($html, null, 'test-store-pdf-fle', true, false); // we used default storage path /public/downloads/pdfs
        $fileUrl = $file['ObjectURL'];

        return $fileUrl; // return file url as string to store it to db or do any action
    }

    public function generateAndStoreToS3()
    {
        $data = $this->getDynamicParams();
        $html = view('pdf.example-2',$data)->render();
        $gpdf = app(Gpdf::class);
        $file = $gpdf->generateWithStoreToS3($html, null, 'test-store-pdf-fle', true, true);

        return $file['ObjectURL']; // return file url as string
    }

    public function generateAndStoreMultiplePages()
    {
        $data = $this->getDynamicParams();

        // generate from many html pages in same pdf file
        $html = collect(['pdf.example-1', 'pdf.example-2', 'pdf.example-3'])
            ->map(fn($view) => view($view, $data)->render())
            ->implode('');

        $gpdf = app(Gpdf::class);
        $file = $gpdf->generateWithStore($html, null, 'test-store-pdf-fle'); // file name optionals
        $fileUrl = $file['ObjectURL'];

        Log::info($fileUrl);
    }

    public function generatePdfWithArabicContent()
    {
        $data = $this->getDynamicParams();

        $html = view('pdf.example-2-with-arabic', $data)->render();
        $pdfContent = GpdfFacAde::generate($html);
        return response($pdfContent, 200, ['Content-Type' => 'application/pdf']);
    }

    public function generateAdvanceWithFixedHeader()
    {
        $pages = $this->generateData(10);
        $html = view('pdf.advance-example', compact('pages'))->render();

        $gpdf = app(Gpdf::class);
        $file = $gpdf->generateWithStore($html, null, 'complex-advance-pdf', true, false);
        $fileUrl = $file['ObjectURL'];

        Log::info($fileUrl);
    }
}