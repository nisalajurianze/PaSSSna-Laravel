<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Settings
    |--------------------------------------------------------------------------
    |
    | These settings configure the behavior of the DOMPDF class.
    |
    */
    'settings' => [
        /**
         * The location of the DOMPDF font directory
         *
         * The location of the directory where DOMPDF will store fonts and font metrics
         * Note: This directory must exist and be writable by the webserver process.
         *
         * Default: storage_path('fonts/')
         */
        'font_dir' => storage_path('fonts/'),

        /**
         * The location of the DOMPDF font cache directory
         *
         * This directory contains the cached font metrics for the fonts used by DOMPDF.
         * This directory can be the same as font_dir
         *
         * Default: storage_path('fonts/')
         */
        'font_cache' => storage_path('fonts/'),

        /**
         * The location of a temporary directory.
         *
         * The directory must be writeable by the webserver process.
         * The temporary directory is required to download remote images and when
         * using the PFDLib back end.
         *
         * Default: sys_get_temp_dir()
         */
        'temp_dir' => storage_path('app/'),

        /**
         * ==== IMPORTANT ====
         *
         * dompdf's "chroot": Prevents dompdf from accessing system files or other
         * files on the webserver.  All local files opened by dompdf must be in a
         * subdirectory of the directory. DO NOT set it to '/' since this could
         * allow an attacker to use dompdf to read any files on the server.
         *
         * Default: realpath(base_path())
         */
        'chroot' => realpath(base_path()),

        /**
         * Protocol whitelist
         *
         * Protocols and PHP wrappers allowed in URIs, and the validation rules
         * that determine if a resouce may be loaded. Full support is not guaranteed
         * for the protocols/wrappers specified
         * by this array.
         *
         * Default: ["file://", "http://", "https://"]
         */
        'allowed_protocols' => ['file://', 'http://', 'https://'],

        /**
         * Operational artifact (log files, temporary files) path validation
         *
         * Regular expression applied to URIs to prevent malicious read/write to
         * system files. Set to an empty string to allow all files.
         *
         * Default: ''
         */
        'artifact_path_regex' => '',

        /**
         * The PDF rendering backend to use
         *
         * Valid settings are 'PDFLib', 'CPDF', 'GD', and 'auto'. 'auto' will
         * look for PDFLib and use it if found, or if not it will fall back on
         * CPDF. 'GD' renders PDFs to graphic files. {@link * Canvas_Factory} ultimately
         * determines which rendering class to instantiate
         * based on this setting.
         *
         * Default: 'CPDF'
         */
        'pdf_backend' => 'CPDF',

        /**
         * PDFlib license key
         *
         * If you are using a licensed, commercial version of PDFlib, specify
         * your license key here.  If you are using PDFlib-Lite or are evaluating
         * the commercial version of PDFlib, comment out this setting.
         *
         * Default: ''
         */
        'pdflib_license' => '',

        /**
         * html target media view which should be rendered into pdf.
         * List of types and parsing rules for future extensions:
         * http://www.w3.org/TR/CSS21/media.html%20media-types
         *
         * Default: 'print'
         */
        'default_media_type' => 'print',

        /**
         * The default paper size.
         *
         * North America standard is "letter"; other countries generally "a4"
         *
         * Default: 'a4'
         */
        'default_paper_size' => 'a4',

        /**
         * The default paper orientation.
         *
         * Default: 'portrait'
         */
        'default_paper_orientation' => 'portrait',

        /**
         * The default font family
         *
         * Used if no suitable fonts can be found. This must exist in the font folder.
         *
         * Default: 'serif'
         */
        'default_font' => 'sans-serif',

        /**
         * Image DPI setting
         *
         * This setting determines the default DPI setting for images and fonts.
         * The DPI may be overridden for inline images by explictly setting the
         * image's width & height style attributes (i.e. if the image's native
         * width is 600 pixels and you specify the image's width as 72 points,
         * the image will have a DPI of 600 in the PDF at 72 points size).
         *
         * Default: 96
         */
        'dpi' => 96,

        /**
         * Enable embedded PHP
         *
         * If this setting is set to true then DOMPDF will automatically evaluate
         * embedded PHP contained within <script type="text/php"> ... </script> tags.
         *
         * Default: false
         */
        'enable_php' => false,

        /**
         * Enable inline JavaScript
         *
         * If this setting is set to true then DOMPDF will automatically insert
         * JavaScript code contained within <script type="text/javascript"> ... </script> tags.
         *
         * Default: true
         */
        'enable_javascript' => true,

        /**
         * Enable remote file access
         *
         * If this setting is set to true, DOMPDF will access remote sites for
         * images and CSS files as required.
         * This allows direct linking and direct output of the PDF to the browser,
         * but may expose the installer to remote scripting attacks.
         *
         * Default: true
         */
        'enable_remote' => true,

        /**
         * List of allowed remote hosts
         *
         * Only used if enable_remote is true. An array of base domains that are
         * allowed to be accessed. Wildcards (*) are allowed.
         *
         * Default: []
         */
        'allowed_remote_hosts' => [],

        /**
         * A ratio applied to the fonts height to be more like browsers' line height
         *
         * Default: 1.1
         */
        'font_height_ratio' => 1.1,

        /**
         * Use the HTML5 Lib parser
         *
         * Default: false
         */
        'enable_html5_parser' => true,

        /**
         * Use the Subsetter
         *
         * If true, the font subsetter will be used to reduce the size of embedded fonts.
         *
         * Default: true
         */
        'enable_font_subsetting' => true,

        /**
         * Debug settings
         *
         * If true, additional debug information is logged to the log file.
         * This is useful for debugging font issues.
         *
         * Default: false
         */
        'debug_png' => false,
        'debug_keep_temp' => false,
        'debug_css' => false,
        'debug_layout' => false,
        'debug_layout_lines' => true,
        'debug_layout_blocks' => true,
        'debug_layout_inline' => true,
        'debug_layout_padding_box' => true,
    ],

];
