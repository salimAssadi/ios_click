<?php
return [
    'mode'                 => '',
    'format'               => 'A4',
    'default_font_size'    => '16',
    'default_font'         => 'Arial',
    'margin_left'          => 10,
    'margin_right'         => 10,
    'margin_top'           => 36,
    'margin_bottom'        => 20,
    'margin_header'        => 5,
    'margin_footer'        => 5,

    'orientation'          => 'P',
    'title'                => 'RCAT',
    'author'               => 'RCAT',
    'watermark'            => '',
    'show_watermark'       => false,
    'watermark_font'       => 'Arial',
    'display_mode'         => 'fullpage',
    'watermark_text_alpha' => 0.1,
    'custom_font_dir' => base_path('resources/fonts/'), // don't forget the trailing slash!
    'custom_font_data' => [
        // 'arabic' => [
        //     "R"=> "NotoNaskhArabic-Normal.ttf",
        //     "B"=>"NotoNaskhArabic-Bold.ttf",
        //     "I"=> "NotoNaskhArabic-Italic.ttf",
        //     "BI"=> "NotoNaskhArabic-BoldItalic.ttf",
        //     'useOTL' => 0xFF,
        //     'useKashida' => 75, // Try disabling Kashida support
        // ],
        'almarai' => [
            'R'  => 'Almarai-Normal.ttf',    // regular font
            'B'  => 'Almarai-Bold.ttf',       // optional: bold font
            'I'  => 'Almarai-Italic.ttf',     // optional: italic font
            'BI' => 'Almarai-BoldItalic.ttf', // optional: bold-italic font,
            'useOTL' => 0xFF,
            'useKashida' => 75,
        ],
        
    ],
    'auto_language_detection'  => true,
    'temp_dir'               => base_path('temp'),
];