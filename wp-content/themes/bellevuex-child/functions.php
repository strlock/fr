<?php
define('FR_THEME_PATH', ABSPATH . "wp-content/themes/bellevuex-child/");

// Hook the function to the 'wp_enqueue_scripts' action
add_action( 'wp_enqueue_scripts', function(){
    // Register the script
    wp_register_script(
        'fr-main-js', // 1. Unique handle for the script
        get_stylesheet_directory_uri() . '/assets/js/main.js', // 2. Path to the script file
        array('jquery'), // 3. Dependencies (e.g., loads after jQuery)
        '1.0', // 4. Version number
        true // 5. Load in footer (true) or header (false)
    );

    // Enqueue the script
    wp_enqueue_script( 'fr-main-js' );
});