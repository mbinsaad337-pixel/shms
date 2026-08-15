<?php

return [
    'mode' => 'utf-8',
    'format' => 'A4',
    'default_font_size' => '12',
    'default_font' => 'cairo',
    'margin_left' => 10,
    'margin_right' => 10,
    'margin_top' => 10,
    'margin_bottom' => 10,
    'margin_header' => 0,
    'margin_footer' => 0,
    'orientation' => 'P',
    'title' => 'Student Housing',
    'author' => '',
    'watermark' => '',
    'show_watermark' => false,
    'show_watermark_image' => false,
    'watermark_font' => 'sans-serif',
    'display_mode' => 'fullpage',
    'watermark_text_alpha' => 0.1,
    'watermark_image_path' => '',
    'watermark_image_alpha' => 0.2,
    'watermark_image_size' => 'D',
    'watermark_image_position' => 'P',
    'custom_font_dir' => storage_path('fonts/'),
    'custom_font_data' => [
        'cairo' => [
            'R'  => 'cairo_normal_a5cea5fc45f6bf5f483d9f082575cfe3.ttf',    // regular font
            'B'  => 'cairo_bold_23a9b2dc30935e892c606fbbafd14072.ttf',       // optional: bold font
            'useOTL' => 0xFF,
            'useKashida' => 75,
        ],
        'almarai' => [
            'R'  => 'Almarai-Regular.ttf',    // regular font
            'B'  => 'Almarai-Bold.ttf',       // optional: bold font
            'useOTL' => 0xFF,
            'useKashida' => 75,
        ]
    ],
    'auto_language_detection' => true,
    'temp_dir' => storage_path('app'),
    'pdfa' => false,
    'pdfaauto' => false,
    'use_active_forms' => false,
];
