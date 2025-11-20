<?php

if ( ! function_exists ( 'themovation_so_wb_scripts' ) ) :
// Enqueueing Frontend stylesheet and scripts.
    function themovation_so_wb_scripts() {

        //wp_enqueue_script( 'themo-js-head', THEMO_URL . 'js/themo-head.js', array('jquery'), THEMO_VERSION, false);
        $timeChanged = filemtime(THEMO_PATH.'js/themo-foot.js');//THEMO_VERSION;
        wp_enqueue_script( 'themo-js-foot', THEMO_URL . 'js/themo-foot.js', array('jquery'), $timeChanged, true);

        // Enqueue font awesome on all pages
        wp_enqueue_style( 'font-awesome' );

        if ( wp_script_is( 'booked-font-awesome', 'enqueued' ) && wp_style_is( 'font-awesome', 'enqueued' ) ) {
            wp_dequeue_script( 'booked-font-awesome' );
        }
        
        /** some themovation templates might also include the same script but we are using the same handle so there's no problem**/
        wp_deregister_script( 'mphb-flexslider' ); // // Deregister MotoPress Flex Slider JS.
	wp_register_script('t_vendor_footer', THEMO_URL . 'js/vendor_footer.min.js', array(), '1.3', true);
	wp_enqueue_script('t_vendor_footer');
    }
endif;
add_action( 'wp_enqueue_scripts', 'themovation_so_wb_scripts', 20 );


function themovation_specific_scripts($styles) {
    $timeChanged = filemtime(THEMO_PATH . 'js/mphb.js'); // THEMO_VERSION;
    $script_name = 'aloha-wphb';
    wp_register_script( $script_name, THEMO_URL . 'js/mphb.js', array('mphb'), $timeChanged, true );
    wp_enqueue_script($script_name);
    $current_url = $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
    $look_for = 'bellevuetheme.com/demo';
    $data = [
        'is_demo' => strpos($current_url, $look_for) !== false,
        'room_fields' => [
            'adults' => 2,
            'children' => 0,
            'guest-name' => 'Steven Lane',
        ],
        'customer_fields' => [
            'first_name' => 'Steven',
            'last_name' => 'Lane',
            'phone' => '604-402-5290',
            'country' => 'CA',
            'note' => __('I would like to request a late checkout. Thank you.', ALOHA_DOMAIN),
        ]
    ];

    wp_localize_script($script_name, 'aloha_mphb', $data);
    return $styles;
}

add_action('wp_enqueue_scripts', 'themovation_specific_scripts');

add_action('wp_enqueue_scripts', 'themovation_mphb_styles');

function themovation_mphb_styles() {
    //we put the mphb-reviews as the dependency so it loads if the reviews are active
    $timeChanged = filemtime(THEMO_PATH . 'css/mphb-reviews.css');
    wp_enqueue_style('aloha-mphb-reviews', THEMO_URL . 'css/mphb-reviews.css', ['mphb-reviews'], $timeChanged);
}

if('uplands' == THEMO_CURRENT_THEME){
    // GOLF
    // FRONTEND // After Elementor registers all styles.
    //add_action( 'elementor/frontend/after_register_styles', 'th_enqueue_after_frontend_golf' );

    //function th_enqueue_after_frontend_golf() {

    //}
    add_action( 'elementor/editor/before_enqueue_styles', 'th_before_enqueue_styles_golf' );
    function th_before_enqueue_styles_golf() {
        wp_enqueue_style( 'themo-icons', THEMO_ASSETS_URL . 'icons/golf_icons.css', array(), THEMO_VERSION);
    }
    // EDITOR // Before the editor scripts enqueuing.
    add_action( 'elementor/editor/before_enqueue_scripts', 'th_before_enqueue_scripts_golf' );

    function th_before_enqueue_scripts_golf() {
        // JS for the Editor
        //wp_enqueue_script( 'themo-editor-js', THEMO_URL  . 'js/th-editor.js', array(), THEMO_VERSION);
    }
}

// FRONTEND // After Elementor registers all styles.
add_action( 'elementor/frontend/after_register_styles', 'th_enqueue_after_frontend' );

function th_enqueue_after_frontend() {

    if('uplands' == THEMO_CURRENT_THEME) {
        wp_enqueue_style('themo-icons', THEMO_ASSETS_URL . 'icons/golf_icons.css', array(), THEMO_VERSION);
    }else{
        wp_enqueue_style( 'themo-icons', THEMO_ASSETS_URL . 'icons/icons.css', array(), THEMO_VERSION);
    }

    $timeChanged = filemtime(THEMO_PATH.'css/global.css');//THEMO_VERSION;
    wp_enqueue_style( ALOHA_GLOBAL_CSS_HANDLE , THEMO_URL . 'css/global.css', array(), $timeChanged );
}

//load overrides the last
add_action('wp_footer', function () {
    $timeChanged = filemtime(THEMO_PATH . 'css/global-overrides.css');
    wp_enqueue_style(ALOHA_GLOBAL_CSS_HANDLE . '-override', THEMO_URL . 'css/global-overrides.css', array(), $timeChanged);

    $elementor_instance = aloha_hfe_get_elementor_instance();
    //if body typography found, it's likely that even for the new sites, we need to set a numerical line height that elementor won't cater
    if ($elementor_instance) {
        $kit = $elementor_instance->kits_manager->get_active_kit();
//        $kid_id = $kit->get_main_id();
        $body_typography_line_height = $kit->get_settings('body_typography_line_height');
        $existing_size = isset($body_typography_line_height['size']) ?  $body_typography_line_height['size'] : '';
        $existing_unit = isset($body_typography_line_height['unit']) ?  $body_typography_line_height['unit'] : '';
        //set the value if not set by elementor
        if (!empty($existing_size) && !empty($existing_unit)) {
            wp_add_inline_style(ALOHA_GLOBAL_CSS_HANDLE . '-override', 'body,li,p{line-height: ' . $existing_size.$existing_unit . ';}');
        }
    }
});

add_action( 'elementor/editor/before_enqueue_styles', 'th_before_enqueue_styles' );

function th_before_enqueue_styles(){
    wp_enqueue_style( 'themo-icons', THEMO_ASSETS_URL . 'icons/icons.css', array(), THEMO_VERSION);

    $timeChangedEditor = filemtime(THEMO_PATH.'css/editor.css');
    wp_enqueue_style( 'themo-editor', THEMO_URL . 'css/editor.css', array(), $timeChangedEditor);

    $timeChangedFont = filemtime(THEMO_ASSETS_PATH.'icons/editor-icons.css');
    wp_enqueue_style( 'themo-editor-icons', THEMO_ASSETS_URL . 'icons/editor-icons.css', array(), $timeChangedFont);
    
    $timeChanged2 = filemtime(THEMO_PATH.'css/accordion.css');
    wp_enqueue_style( 'thmv-accordion', THEMO_URL . 'css/accordion.css', array(), $timeChanged2 );

    //load font awesome
    if(!wp_style_is( 'font-awesome', 'enqueued' )){
        $elementorFile = ABSPATH . 'wp-content/plugins/elementor/elementor.php';
        $plugin_url = plugins_url('/', $elementorFile) . 'assets/lib/font-awesome';
        wp_enqueue_style('font-awesome', $plugin_url . '/css/all.min.css', array(), THEMO_VERSION);
    }
}
// EDITOR // Before the editor scripts enqueuing.
add_action( 'elementor/editor/before_enqueue_scripts', 'th_before_enqueue_scripts' );

function th_before_enqueue_scripts() {

    // JS for the Editor
    $timeChanged = filemtime(THEMO_PATH.'js/th-editor.js');
    wp_enqueue_script( 'themo-editor-js', THEMO_URL  . 'js/th-editor.js', array(), $timeChanged, true);

    $elementor_is_single_template = false;
    $elementsToTop = [];
    
    $templateBlockType = '';
    if(get_post_type() === 'elementor-thhf'){
        $templateBlockType = get_post_meta(get_the_ID(),'ehf_template_type', true );
        if('type_single' === $templateBlockType){
            $elementor_is_single_template = true;
            $locationSelection = get_post_meta(get_the_ID(),'ehf_target_include_locations', true );
            if(isset($locationSelection['rule']) && is_array($locationSelection['rule'])){
                foreach($locationSelection['rule'] as $location){
                    if(strpos($location, 'product|')!==false){
                        $elementsToTop[] = 'themo-woocommerce';
                        break;
                    }
                }
            }

            $elementsToTop[] = 'themo-single';
        }

    }
   
    $check_for_woocommerce_error = get_the_ID() && get_the_ID()===(INT)get_option( 'woocommerce_checkout_page_id' );
    
    $pattern = 'themo-elementor-%s-mode';
    wp_localize_script('themo-editor-js', 'themo_editor_object', array(
        'ajaxurl' => admin_url( 'admin-ajax.php' ),
        'active_theme' => ALOHA_DOMAIN.' '.'themo-active-theme-'.THEMO_CURRENT_THEME,
        'elementor_theme_ui_class_pattern' => $pattern,
        'elementor_theme_ui' => sprintf($pattern, th_get_elementor_theme_mode()),
        'elementor_single_elementor_slug' => $elementsToTop,
        'elementor_is_single_template' => $templateBlockType,
        'aloha_button_style_id' => ALOHA_BUTTON_STYLE_ID,
        'aloha_button_style_prefix' => ALOHA_BUTTON_STYLE_PREFIX,
        'restore_text' => esc_html__('Restoring... Please wait...'),
        'kit_restore_confirmation' => esc_html__('Are you sure you want to revert to the last known settings?'),
        'check_for_woocommerce_checkout_error' =>$check_for_woocommerce_error,
        'woocommerce_checkout_error_strings' =>['header'=>__('Sorry', ALOHA_DOMAIN), 'message'=>__('You have to add an item to the cart to be able to edit this page.',ALOHA_DOMAIN)],
    ));
}


// PREVIEW // Before the preview styles enqueuing.
add_action( 'elementor/preview/enqueue_styles', 'th_enqueue_preview' );

function th_enqueue_preview() {
    wp_enqueue_style( 'themo-preview-style', THEMO_URL  . 'css/th-preview.css', array(), THEMO_VERSION);
    $time_modified = filemtime(THEMO_PATH . 'js/th-preview.js');
    wp_enqueue_script( 'themo-preview-script', THEMO_URL  . 'js/th-preview.js', array(), $time_modified);
}

// FRONTEND // After Elementor registers all scripts.
function th_enqueue_after_frontend_scripts() {
    
    if(showLibrary()){
        // JS for the Editor
        //wp_enqueue_script( 'themo-editor-js', THEMO_URL  . 'js/th-editor.js', array(), THEMO_VERSION);
        wp_enqueue_style( 'thmv-library-style', ALOHA_TEMPLATE_LIBRARY_URL . 'css/th-library.css', [ 'elementor-editor' ], THEMO_VERSION );
        $th_library_version = filemtime(ALOHA_TEMPLATE_LIBRARY_PATH.'/js/th-library.js');
        wp_enqueue_script( 'thmv-library-script', ALOHA_TEMPLATE_LIBRARY_URL . 'js/th-library.js', [ 'elementor-editor', 'jquery-hover-intent' ], $th_library_version, true );

        $localized_data = [
            'i18n' => [
                'templatesEmptyTitle' => esc_html__( 'No Templates Found', ALOHA_DOMAIN ),
                'templatesEmptyMessage' => esc_html__( 'Try different category or sync for new templates.', ALOHA_DOMAIN ),
                'templatesNoResultsTitle' => esc_html__( 'No Results Found', ALOHA_DOMAIN ),
                'templatesNoResultsMessage' => esc_html__( 'Please make sure your search is spelled correctly or try a different word.', ALOHA_DOMAIN ),
            ]
        ];

        wp_localize_script( 'thmv-library-script', 'ThBlockEditor', $localized_data );
    }
    else{
        //load scripts in the footer, show them they need to be registered for the library to show up
        add_action('elementor/editor/footer', function () {
                include_once ALOHA_TEMPLATE_LIBRARY_PATH . '/register.php';
        });
        $th_library_version = filemtime(ALOHA_TEMPLATE_LIBRARY_PATH.'/js/th-library-empty.js');
        wp_enqueue_script( 'thmv-empty-library-script', ALOHA_TEMPLATE_LIBRARY_URL . 'js/th-library-empty.js', [ 'elementor-editor', 'jquery-hover-intent' ], $th_library_version, true );
    }

}


add_action( 'elementor/editor/after_enqueue_scripts', 'th_enqueue_after_frontend_scripts' );


/* If Elementor P is not active, tuck away widgets. */
if ( ! function_exists ( 'thmv_tuck_pro_widgets' ) ) {
    function thmv_tuck_pro_widgets(){
        ?>
        <style>
            #elementor-panel-category-pro-elements,
            #elementor-panel-category-woocommerce-elements,
            #elementor-panel-category-theme-elements {
                display: none;
            }
        </style>
        <?php
    }
}

if ( !defined( 'ELEMENTOR_PRO_VERSION' )) {
    add_action( 'elementor/editor/footer', 'thmv_tuck_pro_widgets' );
}


if (is_admin()) {

    add_filter('opb_display_by_type', 'add_type_th_room_icons');


    function add_type_th_room_icons($args = array()) {
        
        if($args['type']!=='th_room_icons'){
            return $args;
        }
        
        $elementorFile = ABSPATH . 'wp-content/plugins/elementor/elementor.php';
        if (!file_exists($elementorFile))
            return;

        if(!wp_style_is( 'font-awesome', 'enqueued' )){
            $plugin_url = plugins_url('/', $elementorFile) . 'assets/lib/font-awesome';
            wp_enqueue_style('font-awesome', $plugin_url . '/css/all.min.css', array(), THEMO_VERSION);
        }
        wp_enqueue_style('th-trip', THEMO_ASSETS_URL . 'icons/icons.css', array(), THEMO_VERSION);
        $trip_icons = array_values(array_filter(themo_icons(), function ($key) {return strpos($key, 'th-trip') === 0;}, ARRAY_FILTER_USE_KEY));
        $linea_icons = array_values(array_filter(themo_icons(), function ($key) {return strpos($key, 'th-linea') === 0;}, ARRAY_FILTER_USE_KEY));

        $arrayKeys = ['brands' => 'fab', 'solid' => 'fas', 'regular' => 'far'];
        $urls = [];

        foreach ($arrayKeys as $key => $fa) {
            wp_enqueue_style('font-awesome-' . $key, $plugin_url . '/css/' . $key . '.min.css', array(), time());
            $urls[$key] = $plugin_url . '/js/' . $key . '.js';
        }


        $timeChanged = filemtime(THEMO_PATH.'css/th-icons.css');
        wp_enqueue_style('th-icons', THEMO_URL . 'css/th-icons.css', array(), $timeChanged);

        $timeChanged2 = filemtime(THEMO_PATH.'js/th-icons.js');
        wp_enqueue_script('th-icons', THEMO_URL . 'js/th-icons.js', array(), $timeChanged2);

        wp_localize_script('th-icons', 'th_object',
            array(
                'ajaxurl' => admin_url('admin-ajax.php'),
                'urls' => $urls,
                'keys' => $arrayKeys,
                'trip_icons' => $trip_icons,
                'linea_icons' => $linea_icons,
            )
        );

        $args['field_class'] = (isset($args['field_class']) ? $args['field_class'] : '');
        // Turns arguments array into variables.
        extract($args);

        // Verify a description.
        $has_desc = !empty($field_desc) ? true : false;

        echo '<div class="format-setting ' . ( $has_desc ? 'has-desc' : 'no-desc' ) . '">';

        echo $has_desc ? '<div class="description">' . wp_kses_post(htmlspecialchars_decode($field_desc)) . '</div>' : '';

        echo '<div class="format-setting-inner">';

        echo '<div class="option-tree-th-icons-wrap">';

        $showCount = 12;
        for ($index = 0; $index < $showCount; $index++) {
            $value = isset($field_value[$index]) ? $field_value[$index]['value'] : '';
            $label = isset($field_value[$index]) ? $field_value[$index]['label'] : '';
            $library = isset($field_value[$index]) ? $field_value[$index]['library'] : '';
            $hasSomeValue = !empty($value) || !empty($label) ||$index == 0;
            echo '<div data-index="'.$index.'" class="icon-fields-wrapper '.($hasSomeValue ? 'icon-active' : '').'" ' . ($hasSomeValue || $index == 0 ? '' : 'style="display:none"') . '>';
            echo '<div class="icon-title"><div class="title">Icon</div><div class="order-buttons"><span data-action="up" class="order-up icon opb-icon-chevron-up" aria-hidden="true"></span><span data-action="down" class="order-down icon opb-icon-chevron-down" aria-hidden="true"></span></div></div>';
            echo '<div class="icon-holder add-th-icon">'
                . '<i class="' . (!empty($value) ? $value : 'icon opb-icon-plus-circle') . '" aria-hidden="true" ></i>'
                . '</div>';
            echo '<input type="text" placeholder="Label" name="' . esc_attr($field_name) . '[' . $index . '][label]" id="' . esc_attr($field_id) . '_' . $index . '_label" value="' . esc_attr($label) . '" class="' . esc_attr($field_class) . '"  />';
            echo '<input type="hidden" name="' . esc_attr($field_name) . '[' . $index . '][value]" id="' . esc_attr($field_id) . '_' . $index . '_value" value="' . esc_attr($value) . '" class="th_icon_value ' . esc_attr($field_class) . '" />';
            echo '<input type="hidden" name="' . esc_attr($field_name) . '[' . $index . '][library]" id="' . esc_attr($field_id) . '_' . $index . '_library" value="' . esc_attr($library) . '" class="th_icon_library ' . esc_attr($field_class) . '" />';
            echo '<a style="' . (!empty($hasSomeValue) ? '' : 'display:none') . '" href="#" class="remove-button button option-builder-ui-button button-secondary light"><span class="icon opb-icon-minus-circle"></span></a>';
            echo '</div>';
        }

        echo '</div>';
        echo '<div><a class="add-another-icon button-primary option-builder-ui-button light" href="#"><span class="icon opb-icon-plus-circle"></span></a></div>';

        echo '</div>';

        echo '</div>';
    }

}
