<?php

namespace App\Http\Controllers;
use PhpOffice\PhpWord\PhpWord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\TemplateProcessor;

class DocController extends Controller
{
   
    public function convertToHtml()
    {
        // Define the file name (replace 'إجراء إدارة المعارف جديد.docx' with your actual file name)
        $fileName = 'إجراء ضبط المعلومات جديد.docx';

        // Define the storage path for the .docx file
        $docxPath = storage_path('app/public/ISO_DIC/' . $fileName);
        $templateProcessor = new TemplateProcessor($docxPath);
        $data = [
            // Preparation section
            'prepared_name' => 'غزةيي حسين',
            'prepared_job' => 'مساعد إستشاري',
            'prepared_signature' => '..........................',
        
            'reviewed_name' => 'محمود غنيم',
            'reviewed_job' => 'مدير الجودة',
            'reviewed_signature' => '.............................',
        
            'approved_name' => 'رياض الغيلي',
            'approved_job' => 'الرئيس التنفيذي',
            'approved_signature' => '............................',
        
            // Distribution section
            'distribution_1_department' => 'الإدارة العامة',
            'distribution_1_code' => 'GM',
            'distribution_1_copies' => '1',
        
            'distribution_2_department' => 'قسم الجودة',
            'distribution_2_code' => 'IMS',
            'distribution_2_copies' => 'أصل الوثيقة',
        
            'distribution_3_department' => '',
            'distribution_3_code' => '',
            'distribution_3_copies' => '',
        
            'distribution_4_department' => '',
            'distribution_4_code' => '',
            'distribution_4_copies' => '',
        
            'distribution_5_department' => '',
            'distribution_5_code' => '',
            'distribution_5_copies' => '',
        
            // Revision section
            'revision_1_version' => '',
            'revision_1_date' => '',
            'revision_1_description' => '',
            'revision_1_author' => '',
        
            'revision_2_version' => '',
            'revision_2_date' => '',
            'revision_2_description' => '',
            'revision_2_author' => '',
        
            'revision_3_version' => '',
            'revision_3_date' => '',
            'revision_3_description' => '',
            'revision_3_author' => '',
        
            'revision_4_version' => '',
            'revision_4_date' => '',
            'revision_4_description' => '',
            'revision_4_author' => '',
        
            'revision_5_version' => '',
            'revision_5_date' => '',
            'revision_5_description' => '',
            'revision_5_author' => '',
        ];

        // Check if the file exists 
        if (!file_exists($docxPath)) {
            return response()->json(['error' => 'File not found'], 404);
        }

        // Define the output HTML file path
        $htmlPath = storage_path('app/public/ISO_DIC/output.docx');

        try {
           // Replace placeholders with actual data
            foreach ($data as $key => $value) {
                $templateProcessor->setValue($key, $value);
            }
            $templateProcessor->saveAs($htmlPath);

            // Generate the public URL for the HTML file

            // Return the HTML file for download or provide the URL
            return response()->json([
                'message' => 'Conversion successful',
                'download_url' => $htmlPath,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Conversion failed: ' . $e->getMessage()], 500);
        }
    }
}
