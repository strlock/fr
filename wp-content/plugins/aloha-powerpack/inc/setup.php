<?php
use Elementor\Controls_Manager;   
use Elementor\Core\Settings\Manager as SettingsManager;

function th_get_elementor_theme_mode(){
    $editor_preferences = SettingsManager::get_settings_managers( 'editorPreferences' );
    return $editor_preferences->get_model()->get_settings( 'ui_theme' );
}
// Adding Custom Icons for Icon Control
require_once THEMO_PATH . 'fields/icons.php' ;

require_once THEMO_PATH . 'inc/helper-functions.php' ;

if ( ! function_exists( 'themovation_elements' ) ) {
    function themovation_elements()
    {
        require_once THEMO_PATH . 'elements/slider.php';
        require_once THEMO_PATH . 'elements/header.php';
        require_once THEMO_PATH . 'elements/button.php';
        require_once THEMO_PATH . 'elements/call-to-action.php';
        require_once THEMO_PATH . 'elements/testimonial.php';
        require_once THEMO_PATH . 'elements/service-block.php';
        
        if(is_plugin_active('formidable/formidable.php')){
            require_once THEMO_PATH . 'elements/formidable-form.php';
        }
        
        require_once THEMO_PATH . 'elements/info-card.php';
        require_once THEMO_PATH . 'elements/team_2.php';
        require_once THEMO_PATH . 'elements/room-grid.php';
        require_once THEMO_PATH . 'elements/room-info.php';
        require_once THEMO_PATH . 'elements/package_2.php';
        require_once THEMO_PATH . 'elements/accommodation_listing.php';
        require_once THEMO_PATH . 'elements/accommodation_search.php';
        require_once THEMO_PATH . 'elements/tabs.php';
        require_once THEMO_PATH . 'elements/itinerary.php';
        require_once THEMO_PATH . 'elements/pricing.php';
        require_once THEMO_PATH . 'elements/pricing-list.php';
        require_once THEMO_PATH . 'elements/image-carousel-timeline.php';
        require_once THEMO_PATH . 'elements/blog_2.php';
        require_once THEMO_PATH . 'elements/image-gallery.php';
        require_once THEMO_PATH . 'elements/google-maps.php';
        
        // Check if the MotoPress Hotel Booking is active
        if (class_exists('HotelBookingPlugin')) {
            require_once THEMO_PATH . 'elements/MPHB/mphb_accommodation_grid.php';
            require_once THEMO_PATH . 'elements/MPHB/mphb_availability_calendar.php';
            require_once THEMO_PATH . 'elements/MPHB/mphb_booking_form.php';
            require_once THEMO_PATH . 'elements/MPHB/mphb_accommodation_details.php';
            require_once THEMO_PATH . 'elements/MPHB/mphb_accommodation_rates.php';
            require_once THEMO_PATH . 'elements/MPHB/mphb_service_details.php';
            require_once THEMO_PATH . 'elements/MPHB/mphb_search_form.php';
            require_once THEMO_PATH . 'elements/MPHB/mphb_search_results.php';
            require_once THEMO_PATH . 'elements/MPHB/mphb_checkout_form.php';
        }
        if (function_exists('wpbs_menu')) {
            require_once THEMO_PATH . 'elements/WPBS/wp-booking-system.php';
        }
    }
}

add_filter( 'elementor/kit/export/manifest-data', 'aloha_export', 10, 2 );
function aloha_export($manifest_data, $exportObj) {
    //get settings from theme_mods
    $theme_mods_to_get = [
        ALOHA_SETTING_BUTTON_STYLE_ID,
            'themo_preloader',
            'themo_boxed_layout',
            'th_boxed_bg_color',
            'th_boxed_bg_image',
            'themo_retinajs',
            'themo_retina_support',
            'themo_room_rewrite_slug',
            'tribe_events_layout_show_header',
            'tribe_events_layout_header_float',
            'tribe_events_layout_sidebar',
            
            'themo_mphb_use_theme_styling',
            'themo_mphp_category_show_header',
            'themo_mphp_category_header_float',
            'themo_mphp_category_sidebar',
            'themo_mphp_category_masonry',
            'themo_mphp_tag_show_header',
            'themo_mphp_tag_header_float',
            'themo_mphp_tag_sidebar',
            'themo_mphp_tag_masonry',
            'themo_mphp_amenities_show_header',
            'themo_mphp_amenities_header_float',
            'themo_mphp_amenities_sidebar',
            'themo_mphp_amenities_masonry',
            'themo_mphp_service_show_header',
            'themo_mphp_service_header_float',
            'themo_mphp_service_sidebar',
            'themo_mphp_service_masonry',
        
            'themo_automatic_post_excerpts',
            'themo_blog_index_layout_show_header',
            'themo_blog_index_layout_header_float',
            'themo_blog_index_layout_sidebar',
            'themo_single_post_layout_show_header',
            'themo_single_post_layout_header_float',
            'themo_single_post_layout_sidebar',
            'themo_default_layout_show_header',
            'themo_default_layout_header_float',
            'themo_default_layout_sidebar',
            'themo_blog_index_layout_masonry',
        
            'themo_mphb_date_colors',//multi field
        
    ];
    $theme_mods = [];
    foreach($theme_mods_to_get as $key){
        $temp_value = get_theme_mod($key, null);
        if(null!==$temp_value){
            $theme_mods[$key] = $temp_value;
        }
    }
    if(count($theme_mods)){
        $exportObj->add_json_file( 'aloha_settings', $theme_mods );
    }
    
    return $manifest_data;
}
// Include Custom Widgets
add_filter( 'elementor/widgets/register', 'themovation_elements' );

//library is for the registered users and only loaded in the elementor edit mode
function aloha_load_library_classes() {
    if (is_admin() && showLibrary() ) {
        include_once(ABSPATH . 'wp-admin/includes/plugin.php');
        include_once ALOHA_TEMPLATE_LIBRARY_PATH . '/library-manager.class.php';
        include_once ALOHA_TEMPLATE_LIBRARY_PATH . '/library-source.class.php';
    }
}

//only load the classes during elementor editor or during the ajax call
if(isset($_REQUEST['action']) && $_REQUEST['action'] ==='elementor_ajax'){
    add_action( 'init', 'aloha_load_library_classes' );
}
add_action( 'elementor/editor/init', 'aloha_load_library_classes' );


// Include scripts, custom post type, shortcodes
// Older version of Elementor (older than version 2) use the old grouping.
if(defined('ELEMENTOR_PATH') && intval('2') > intval(ELEMENTOR_VERSION) ){
    require_once THEMO_PATH . 'inc/elementor-section-old.php';
}else{
    require_once THEMO_PATH . 'inc/elementor-section.php';
}
require_once THEMO_PATH . 'inc/enqueue.php';

require_once THEMO_PATH . 'inc/cpt_room.php' ;
if (class_exists('HotelBookingPlugin')) {
    require_once THEMO_PATH . 'inc/MPHB/cpt_mphb_room_type.php';
}

require_once THEMO_PATH . 'inc/shortcodes.php' ;


// GLOBAL VARIABLES
global $th_map_id;
$th_map_id = 0;

// When plugin is installed for the first time, set global elementor settings.



if ( ! function_exists( 'themovation_so_widgets_bundle_setup_elementor_settings' ) ) {
    function themovation_so_widgets_bundle_setup_elementor_settings()
    {

        // Disable color schemes
        $elementor_disable_color_schemes = get_option('elementor_disable_color_schemes');
        if (empty($elementor_disable_color_schemes)) {
            update_option('elementor_disable_color_schemes', 'yes');
        }

        // Disable typography schemes
        $elementor_disable_typography_schemes = get_option('elementor_disable_typography_schemes');
        if (empty($elementor_disable_typography_schemes)) {
            update_option('elementor_disable_typography_schemes', 'yes');
        }

        // Disable global lightbox by default.
        update_option('elementor_global_image_lightbox', '');

        // Check for our custom post type, if it's not included, include it.
        $elementor_cpt_support = get_option('elementor_cpt_support');
        if (empty($elementor_cpt_support)) {
            $elementor_cpt_support = array();
        }

        if (!in_array("page", $elementor_cpt_support)) {
            array_push($elementor_cpt_support,"page");
            update_option('elementor_cpt_support', $elementor_cpt_support);
        }

        if (!in_array("post", $elementor_cpt_support)) {
            array_push($elementor_cpt_support,"post");
            update_option('elementor_cpt_support', $elementor_cpt_support);
        }

        if (!in_array("themo_tour", $elementor_cpt_support)) {
            array_push($elementor_cpt_support,"themo_tour");
            update_option('elementor_cpt_support', $elementor_cpt_support);
        }

        if (!in_array("themo_portfolio", $elementor_cpt_support)) {
            array_push($elementor_cpt_support,"themo_portfolio");
            update_option('elementor_cpt_support', $elementor_cpt_support);
        }

        if (!in_array("themo_room", $elementor_cpt_support)) {
            array_push($elementor_cpt_support,"themo_room");
            update_option('elementor_cpt_support', $elementor_cpt_support);
        }

        if (!in_array("mphb_room_type", $elementor_cpt_support)) {
            array_push($elementor_cpt_support,"mphb_room_type");
            update_option('elementor_cpt_support', $elementor_cpt_support);
        }

        if (!in_array("mphb_room_service", $elementor_cpt_support)) {
            array_push($elementor_cpt_support,"mphb_room_service");
            update_option('elementor_cpt_support', $elementor_cpt_support);
        }

        if (!in_array("themo_hole", $elementor_cpt_support)) {
            array_push($elementor_cpt_support,"themo_hole");
            update_option('elementor_cpt_support', $elementor_cpt_support);
        }

        if (!in_array("product", $elementor_cpt_support)) {
            array_push($elementor_cpt_support,"product");
            update_option('elementor_cpt_support', $elementor_cpt_support);
        }
        // Enable Elementor Support for HFE
        if (!in_array("elementor-thhf", $elementor_cpt_support)) {
            array_push($elementor_cpt_support,"elementor-thhf");
            update_option('elementor_cpt_support', $elementor_cpt_support);
        }

    }
}

// on plugin Activaton, set Elementor Global Options and register Custom Post Types.

if ( ! function_exists( 'themovation_so_widgets_bundle_install' ) ) {
    function themovation_so_widgets_bundle_install()
    {
        // trigger our function that sets up Elementor Global Settings
        themovation_so_widgets_bundle_setup_elementor_settings();

        // Regsiter Custom Post Types
        themo_room_custom_post_type();

        // Register Custom Taxonomy
        themo_room_type();

        // clear the permalinks after the post type has been registered
        flush_rewrite_rules();
    }
}
register_activation_hook( THEMO__FILE__, 'themovation_so_widgets_bundle_install' );


// Add custom controls to the Page Settings inside the Elementor Global Options.

// Top of section
if ( ! function_exists( 'th_add_custom_controls_elem_post_settings_top' ) ) {

    function th_add_custom_controls_elem_post_settings_top(Elementor\Core\DocumentTypes\PageBase $page)
    {
        // Is elementor Pro loaded
        $elm_pro_loaded = false;
        if( function_exists( 'elementor_pro_load_plugin' ) ) {
            $elm_pro_loaded = true;
        }


        if(isset($page) && $page->get_id() > ""){
            $th_post_type = false;

            $th_post_type = get_post_type($page->get_id());

            if($th_post_type == 'page' || $th_post_type == 'themo_tour' || $th_post_type == 'themo_portfolio' ||
                $th_post_type == 'themo_room' || $th_post_type == 'themo_hole' || $th_post_type == 'mphb_room_type'||
                $th_post_type == 'mphb_room_service' || ($elm_pro_loaded && $th_post_type == 'post')  || ($elm_pro_loaded && $th_post_type == 'revision')){

                // Standard Header Options
                $page->start_controls_section(
                    'thmv_doc_settings_header',
                    [
                        'label' => __( 'Standard Header', ALOHA_DOMAIN ),
                        'tab' => \Elementor\Controls_Manager::TAB_SETTINGS,
                    ]
                );

                $page->add_control(
                    'important_note',
                    [
                        //'label' => __( 'Note', ALOHA_DOMAIN ),
                        'type' => \Elementor\Controls_Manager::RAW_HTML,
                        'raw' => '<div class="elementor-control-title">'.esc_html__('Applies to Standard Header only.', ALOHA_DOMAIN).'</div><div class="elementor-control-field-description">' . sprintf(__('<a href="%1$s" target="_blank">Learn more</p>', ALOHA_DOMAIN), 'https://help.bellevuetheme.com/article/311-custom-header-footer#standard-header-footer') . '</div>',
                        'content_classes' => 'themo-elem-html-control',
                        'separator' => 'before'
                    ]
                );

                $page->add_control(
                    'themo_transparent_header',
                    [
                        'label' => __( 'Transparent Header', ALOHA_DOMAIN ),
                        'type' => Elementor\Controls_Manager::SWITCHER,
                        'default' => 'Off',
                        'label_on' => __( 'On', ALOHA_DOMAIN ),
                        'label_off' => __( 'Off', ALOHA_DOMAIN ),
                        'return_value' => 'on',
                    ]
                );

                $page->add_control(
                    'themo_header_content_style',
                    [
                        'label' => __( 'Transparent Header Content Style', ALOHA_DOMAIN ),
                        'type' => Elementor\Controls_Manager::SELECT,
                        'label_block' => true,
                        'default' => 'light',
                        'options' => [
                            'light' => __( 'Light', ALOHA_DOMAIN ),
                            'dark' => __( 'Dark', ALOHA_DOMAIN ),
                        ],
                        'condition' => [
                            'themo_transparent_header' => 'on',
                        ],
                    ]
                );

                $page->add_control(
                    'themo_alt_logo',
                    [
                        'label' => __( 'Use Alternative Logo', ALOHA_DOMAIN ),
                        'description' => __( 'You can upload an alternative logo under Appearance / Customize / Theme Options / Logo / ', ALOHA_DOMAIN ),
                        'type' => Elementor\Controls_Manager::SWITCHER,
                        'default' => 'Off',
                        'label_on' => __( 'On', ALOHA_DOMAIN ),
                        'label_off' => __( 'Off', ALOHA_DOMAIN ),
                        'return_value' => 'on',
                        'condition' => [
                            'themo_transparent_header' => 'on',
                        ],
                    ]
                );


                $page->add_control(
                    'themo_header_hide_shadow',
                    [
                        'label' => __( 'Hide Header Shadow', ALOHA_DOMAIN ),
                        'type' => Elementor\Controls_Manager::SWITCHER,
                        'label_off' => __( 'No', 'elementor' ),
                        'label_on' => __( 'Yes', 'elementor' ),

                        'selectors' => [
                            '{{WRAPPER}} .navbar-default' => 'border: none',
                        ],
                    ]
                );

                $page_title_selector = get_option( 'elementor_page_title_selector' );
                if ( empty( $page_title_selector ) ) {
                    $page_title_selector = 'h1.entry-title';
                }


                $page->add_control(
                    'themo_page_title_margin',
                    [
                        'label' => __( 'Title  Margin', ALOHA_DOMAIN ),
                        'type' => Elementor\Controls_Manager::SLIDER,
                        'default' => [
                            'size' => 1,
                        ],
                        'range' => [
                            'px' => [
                                'min' => 0,
                                'max' => 1000,
                                'step' => 5,
                            ],
                            '%' => [
                                'min' => 0,
                                'max' => 100,
                            ],
                        ],
                        'size_units' => [ 'px', '%' ],
                        'selectors' => [
                            $page_title_selector => 'margin-top: {{SIZE}}{{UNIT}};',
                        ],
                        'dynamic' => [
                            'active' => true,
                        ],
                    ]
                );
                $page->end_controls_section();
            }

            if ($th_post_type == 'page' || $th_post_type == 'themo_tour' || $th_post_type == 'themo_portfolio'
                || $th_post_type == 'themo_room' || $th_post_type == 'themo_hole' || $th_post_type == 'mphb_room_type'
                || $th_post_type == 'mphb_room_service') {

                // Standard Header Options
                $page->start_controls_section(
                    'thmv_doc_settings_sidebar',
                    [
                        'label' => __( 'Sidebar', ALOHA_DOMAIN ),
                        'tab' => \Elementor\Controls_Manager::TAB_SETTINGS,
                    ]
                );

                $page->add_control(
                    'themo_page_layout',
                    [
                        'label' => __( 'Sidebar', ALOHA_DOMAIN ),
                        'type' => Elementor\Controls_Manager::CHOOSE,
                        'default' => 'full',
                        'options' => [
                            'left'    => [
                                'title' => __( 'Left', ALOHA_DOMAIN ),
                                'icon' => 'fa fa-long-arrow-left',
                            ],
                            'full' => [
                                'title' => __( 'No Sidebar', ALOHA_DOMAIN ),
                                'icon' => 'fa fa-times',
                            ],
                            'right' => [
                                'title' => __( 'Right', ALOHA_DOMAIN ),
                                'icon' => 'fa fa-long-arrow-right',
                            ],

                        ],
                        'return_value' => 'yes',
                    ]
                );

                $page->end_controls_section();
            }
        }

    }
}
add_action('elementor/document/after_save', 'aloha_save_elementor_colors');
/**
 * theme mod is values used by some widgets like mphb and portfolio grid, tabs and more
 * @param type $kit
 */
function aloha_save_elementor_colors($kit) {
    $post_id = isset($_REQUEST['editor_post_id']) ? $_REQUEST['editor_post_id']: false;
    if (is_object($kit) && isset($post_id)) {
        $instance = \Elementor\Plugin::instance();
        $kit = $instance->kits_manager->get_active_kit();
        $system_colors = $kit->get_settings('system_colors');
        if (is_array($system_colors) && count($system_colors)) {
            foreach ($system_colors as $color) {
                if ($color['_id']==='thmv_accent') {
                    set_theme_mod('color_accent', $color['color']);
                }
                if ($color['_id']==='thmv_primary' ) {
                    set_theme_mod('color_primary', $color['color']);
                }
            }
        }
    }
}

add_action( 'wp_ajax_aloha_get_old_kit_name', 'aloha_get_old_kit_name' );
function aloha_get_old_kit_name() {
    $instance = \Elementor\Plugin::instance();
    $kit = $instance->kits_manager->get_active_kit();
    $current_kit = $kit->get_settings(ALOHA_KIT_KEY);
    wp_send_json(['kit'=>$current_kit]);
}

add_action( 'wp_ajax_aloha_restore_backup', 'aloha_restore_backup' );
function aloha_restore_backup() {
    $result = aloha_switch_kit('', true);
    wp_send_json(['success'=>$result]);
}
add_action( 'wp_ajax_aloha_switch_kits', 'aloha_switch_kits' );

function aloha_switch_kits() {

    $new_kit = isset($_REQUEST['new_kit']) ? $_REQUEST['new_kit'] : '';
    $current_kit = isset($_REQUEST['old_kit']) ? $_REQUEST['old_kit'] : '';
    $instance = \Elementor\Plugin::instance();
    $kit = $instance->kits_manager->get_active_kit();

    if ($current_kit !== $new_kit) {
        //take backup of the existing kit if it's not a preset and if changed.
        $current_settings = $kit->get_settings();

        //if empty, meaning custom kit, take a backup
        //if a preset, then compare to the preset file, take backup if necessary

        if (empty($current_kit) || (!empty($current_kit) && aloha_is_backup_required($current_kit, $current_settings))) {

            $backup = aloha_take_kit_backup();
            if (!$backup) {
               wp_send_json(['success'=>$backup]); //failed
            }
            

            //backup taken
            $result = aloha_switch_kit($new_kit);
            wp_send_json(['success'=>$result]);
        }
    }

    //something went wrong
    wp_send_json(['success'=>false]); //failed
}

function aloha_take_kit_backup() {
    $instance = \Elementor\Plugin::instance();
    $kit = $instance->kits_manager->get_active_kit();
    $settings = $kit->get_settings();

    //ignore the active kit setting, remove from the kit
    unset($settings[ALOHA_KIT_KEY]);

    $settings_json = json_encode(['content' => [], 'settings' => $settings]);
    $backup_file = aloha_get_backup_file();
    return file_put_contents($backup_file, $settings_json);
}

/**
 * 
 * @param type $kit
 */
function aloha_is_backup_required($kit, $settings) {
    //compare with the active preset
    $kit_file = ALOHA_KITS_PATH . '/' . $kit . '.json';
    if (file_exists($kit_file)) {

        $json_content = file_get_contents($kit_file);
        $file_data = json_decode($json_content, true);
        //if same settings, return
        if (isset($file_data['settings'])) {
            //ignore the active kit setting, remove from the kit
            $kit_settings = $file_data['settings'];

            unset($settings[ALOHA_KIT_KEY]);
            unset($kit_settings[ALOHA_KIT_KEY]);
//                 echo "<pre><div style='width:50%; float:left;'>";print_r($kit_settings);echo "</div>";
//                echo "<div style='width:50%;float:left;'>";print_r($settings);echo "</div>";
//                exit;
            //@todo, not sure what goes in the content key of the json. We are only comparing the settings key
            if ($settings === $kit_settings) {

                return false;
            }
        }
    }

    return true;
}

/**
 * 
 * @param type $requested_kit
 * @return boolean
 */
function aloha_switch_kit($requested_kit='', $backupRestore=false) {
    $restoreName = $requested_kit;
    if($backupRestore){
        $requested_kit = 'backup';
        $restoreName = '';
    }
    $file = ALOHA_KITS_PATH . '/' . $requested_kit . '.json';
    
    if (file_exists($file)) {
        $file_conents = file_get_contents($file);
        $settings = json_decode($file_conents, true);

        if (is_array($settings) && isset($settings['settings'])) {
            $importSettings = $settings['settings'];
            $instance = \Elementor\Plugin::instance();
            $kit = $instance->kits_manager->get_active_kit();

            foreach ($importSettings as $key => $value) {
                $kit->update_settings([$key => $value]);
            }
            //set the requested kit to active
            $kit->update_settings([ALOHA_KIT_KEY => $restoreName]);
            
            //clear cache
            \Elementor\Plugin::$instance->files_manager->clear_cache();

            return true;
        }
    }
    return false;
}

function aloha_get_backup_file() {
    $backup_file_key = 'backup';
    return ALOHA_KITS_PATH . '/' . $backup_file_key . '.json';
}

/**
 * this is only for the existing template installs, otherwise we use the importer for new install
 * elementor\core\app\modules\import-export\directories\root.php import function
 */
add_action('elementor/frontend/before_enqueue_styles', 'aloha_elementor_setup_global_settings');
function aloha_elementor_setup_global_settings() {
    
    if (!current_user_can('activate_plugins') || !function_exists('thmv_get_template_first_install_version')) {
        return;
    }
    
    //only for the users upgrading, we want to move their settings to Aloha
    if (!get_option('aloha_kit_update_done', false) && thmv_get_template_first_install_version()<4) {

        //take backup of the settings
        aloha_take_kit_backup();
        
        $instance = \Elementor\Plugin::instance();
        $kit = $instance->kits_manager->get_active_kit();
        if(!$kit->get_id()){
            return;
        }
        
        $updated = false;
        
       
        //remove thmv_primary and thmv_accent from custom colors if exists (some old versions had it)
        $custom_colors = $kit->get_settings('custom_colors');
        $custom_colors_updated = false;

        $found_primary_color = get_theme_mod('color_primary', '#1A222C');
        $found_color_accent = get_theme_mod('color_accent', '#4A86CC');
        //we prioritize elementor thmv_primary as it was used in the CSS
        foreach($custom_colors as $key=>$color){
            if($color['_id']==='thmv_primary' || $color['_id']==='thmv_accent'){
                if($color['_id']==='thmv_primary'){
                    $found_primary_color = $color['color'];
                }
                if($color['_id']==='thmv_accent'){
                    $found_color_accent = $color['color'];
                }
                unset($custom_colors[$key]);
                $custom_colors_updated = true;
            }
        }
        
        if($custom_colors_updated){
            //reset keys
            $kit->update_settings(['custom_colors' => array_values($custom_colors)]);
            set_theme_mod('color_primary', $found_primary_color);//set these so can be used below correctly
            set_theme_mod('color_accent', $found_color_accent);//set these so can be used below correctly
            $updated = true;
        }
        
        
        if (is_plugin_active('kirki/kirki.php')) {
            $headingsCount = 6;
            //if no color has been set, set our default scheme, otherwise back out
            //@todo - should we check once or always?
            $setupColors = true;
            $headingColorPattern = 'h[index]_color';
            for ($i = 1; $i <= $headingsCount; $i++) {
                $currentHeading = str_replace('[index]', $i, $headingColorPattern);
                $color = $kit->get_settings($currentHeading);
                if (!empty($color)) {
                    $setupColors = false;
                    break;
                }
            }
            if ($setupColors) {
                //get body color as headings don't have their own color
                $headingColor = get_theme_mod('color_primary');
                for ($i = 1; $i <= $headingsCount; $i++) {
                    $currentHeading = str_replace('[index]', $i, $headingColorPattern);
                    $kit->update_settings([$currentHeading => $headingColor]);
                    $updated = true;
                }
            }

            //setup headings typography
            $headers_typography = get_theme_mod('headers_typography');

            $headers_family = 'Spinnaker'; //Spinnaker
            $headers_font_weight = '400';
            $headers_transform = '';
            //only set if font family is present
            if (is_array($headers_typography)) {
                if (!empty($headers_typography['font-family'])) {
                    $headers_family = $headers_typography['font-family'];
                }

                $headers_font_weight = $headers_typography['variant'];
                if ($headers_font_weight === 'regular') {
                    $headers_font_weight = 'normal';
                }
                if (isset($headers_typography['text-transform']) && !empty($headers_typography['text-transform'])) {
                    $headers_transform = $headers_typography['text-transform'];
                    if ($headers_transform === 'none') {
                        $headers_transform = '';
                    }
                }
            }
            $headings_settings = [
                'typography_font_family' => $headers_family,
                'typography_font_weight' => $headers_font_weight,
                'typography_text_transform' => $headers_transform,
                'typography_typography' => 'custom',
            ];

            for ($i = 1; $i <= $headingsCount; $i++) {
                $heading_typography = $kit->get_settings('h' . $i . '_typography_typography');
                if ($heading_typography !== 'custom') {
                    foreach ($headings_settings as $setting => $value) {
                        $key = 'h' . $i . '_' . $setting;
                        $kit->update_settings([$key => $value]);
                        $updated = true;
                    }
                }
            }


            //check body typography, if not set, then set it
            $theme_body_typography = get_theme_mod('body_typography');

            $default_family = 'Open Sans';
            $default_font_size = '16';
            $default_font_weight = '400';
            $default_color = '#5c5c5c';
            $default_line_height = '';
            $default_line_height_unit = '';
            $default_font_size_unit = 'px';

            if (is_array($theme_body_typography)) {
                if (isset($theme_body_typography['font-family'])) {
                    $default_family = $theme_body_typography['font-family'];
                }
                if (isset($theme_body_typography['font-weight']) && !empty($theme_body_typography['font-weight'])) {
                    $default_font_weight = $theme_body_typography['font-weight'];
                }
                if (isset($theme_body_typography['color']) && !empty($theme_body_typography['color'])) {
                    $default_color = $theme_body_typography['color'];
                }
                if (isset($theme_body_typography['font-size']) && !empty($theme_body_typography['font-size'])) {
                    $default_font_size = (FLOAT) $theme_body_typography['font-size'];
                }
                if (isset($theme_body_typography['line-height']) && !empty($theme_body_typography['line-height'])) {
                    $tempLineHeight = (FLOAT) $theme_body_typography['line-height'];
                    $tempUnit = trim(str_replace($tempLineHeight, "", $theme_body_typography['line-height']));
                    if (!empty($tempLineHeight)) {
                        //if empty temp unit then it is em
                        if (empty($tempUnit)) {
                            $tempUnit = 'em';
                        }
                        if ($tempUnit == 'px' || $tempUnit == 'em') {
                            $default_line_height_unit = $tempUnit; //number*font size
                            $default_line_height = $tempLineHeight;
                        }
                    }
                }
            }
            $body_settings = [
                'body_typography_font_family' => $default_family,
                'body_typography_font_weight' => $default_font_weight,
                'body_typography_font_size' => ['size' => $default_font_size, 'unit' => $default_font_size_unit],
                'body_color' => $default_color,
                'body_typography_typography' => 'custom',
            ];

            if (!empty($default_line_height_unit) && !empty($default_line_height)) {
                $body_settings['body_typography_line_height'] = ['size' => $default_line_height, 'unit' => $default_line_height_unit];
            }

            $body_typography = $kit->get_settings('body_typography_typography');
            if ($body_typography !== 'custom') {
                //no custom settings set
                foreach ($body_settings as $setting => $value) {
                    $kit->update_settings([$setting => $value]);
                    $updated = true;
                }
            }



            //also check 3 additional fonts from the theme and add them as custom fonts    
            $custom_typography = $kit->get_settings('custom_typography');

            if (!$custom_typography) {
                $custom_fonts = [];
                for ($i = 1; $i <= 3; $i++) {
                    $additional = get_theme_mod('additional_fonts_' . $i);
                    if (is_array($additional)) {
                        $font_weight = (INT) isset($additional['font-weight']) && !empty($additional['font-weight']) ? $additional['font-weight'] : '';
                        $font_style = str_replace($font_weight, '', $additional['variant']);
                        if ($font_style === 'regular') {
                            $font_style = 'normal';
                        }
                        $custom = [
                            "_id" => 'additional_fonts_' . $i,
                            "title" => __("Additional Font", ALOHA_DOMAIN) . ' ' . $i,
                            "typography_typography" => "custom",
                            "typography_font_family" => $additional['font-family'],
                        ];
                        if (!empty($font_weight)) {
                            $custom['typography_font_weight'] = $font_weight;
                        }
                        if (!empty($font_style)) {
                            $custom['typography_font_style'] = $font_style;
                        }
                        $custom_fonts[] = $custom;
                    }
                }
                if (count($custom_fonts)) {
                    $kit->update_settings(['custom_typography' => $custom_fonts]);
                    $updated = true;
                }
            }
        }

        $name = aloha_get_elementor_tab_prefix();
        //see if these values already exist (from a past theme_mod)
        $colorsToAdd = [
            ['_id' => 'thmv_primary', 'title' => __('Primary (' . $name . ')', ALOHA_DOMAIN), 'color' => get_theme_mod('color_primary')],
            ['_id' => 'thmv_accent', 'title' => __('Accent (' . $name . ')', ALOHA_DOMAIN), 'color' => get_theme_mod('color_accent')],
            ['_id' => 'thmv_dark', 'title' => __('Dark (' . $name . ')', ALOHA_DOMAIN), 'color' => '#151515'],
            ['_id' => 'thmv_shadow', 'title' => __('Shadow (' . $name . ')', ALOHA_DOMAIN), 'color' => '#707070'],
            ['_id' => 'thmv_midtone', 'title' => __('Midtone (' . $name . ')', ALOHA_DOMAIN), 'color' => '#B9B8B8'],
            ['_id' => 'thmv_highlight', 'title' => __('Highlight (' . $name . ')', ALOHA_DOMAIN), 'color' => '#F4F4F4'],
            ['_id' => 'thmv_light', 'title' => __('Light (' . $name . ')', ALOHA_DOMAIN), 'color' => '#FFFFFF'],
        ];
        $labelCheckArray = $colorsToAdd;

        $idArray = [];
        foreach ($colorsToAdd as $colorToAdd) {
            $idArray[$colorToAdd['_id']] = $colorToAdd['_id'];
        }


        $system_colors = $kit->get_settings('system_colors');

        //check if already exists, then skip it
        foreach ($system_colors as $elemColor) {
            $keyToRemove = array_search($elemColor['_id'], array_keys($idArray));
            if ($keyToRemove !== false) {
                unset($colorsToAdd[$keyToRemove]);
            }
        }

        if (count($colorsToAdd)) {
            $system_colors = array_merge($system_colors, $colorsToAdd);

            $kit->update_settings(['system_colors' => $system_colors]);
            $updated = true;
        }
       
        //if the current decided name is different than the current then update it
        if (count($labelCheckArray) && get_option('aloha_last_used_name', false) !== $name) {
            //nothing new to add, just check if the template has changed and the color exists, change its title
            foreach ($system_colors as $key => $elemColor) {
                if (in_array($elemColor['_id'], $idArray)) {
                    $colorKey = array_search($elemColor['_id'], array_keys($idArray));
                    $system_colors[$key]['title'] = $labelCheckArray[$colorKey]['title'];
                }
            }
            $kit->update_settings(['system_colors' => $system_colors]);
            $updated = true;
            update_option('aloha_last_used_name', $name);
        }

        if ($updated) {
            //flush cache
            $instance->files_manager->clear_cache();
        }
        
        update_option('aloha_kit_update_done', true);
    }
}

add_action( 'elementor/element/wp-post/document_settings/before_section_start', 'th_add_custom_controls_elem_post_settings_top',10, 2);
add_action( 'elementor/element/wp-page/document_settings/before_section_start', 'th_add_custom_controls_elem_post_settings_top',10, 2);

// Add Parallax Control (Switch) to Section Element in the Editor.
function add_elementor_section_background_controls( Elementor\Element_Section $section ) {
    
//    $ui_theme = 'el-ui-theme-'.th_get_elementor_theme_mode();
    
    $section->add_control(
        'th_thmv_section_title',
        [
            'type'            => Controls_Manager::RAW_HTML,
            'raw'             => '<b>Themovation</b>',
            'separator'       => 'before',
            'conditions' => [
                'terms' => [
                    [
                        'name' => 'background_background',
                        'operator' => '==',
                        'value' => 'classic',
                    ],
                ],
            ],
        ]
    );
    $section->add_control(
        'th_section_parallax',
        [
            'label' => __( 'Parallax', ALOHA_DOMAIN ),
            'description' => 'Adds parallax effect to the section background image.',
            'type' => Elementor\Controls_Manager::SWITCHER,
            'label_off' => __( 'Off', ALOHA_DOMAIN ),
            'label_on' => __( 'On', ALOHA_DOMAIN ),
            'default' => 'no',
            'conditions' => [
                'terms' => [
                    [
                        'name' => 'background_background',
                        'operator' => '==',
                        'value' => 'classic',
                    ],
                ],
            ],
        ]
    );
}

add_action( 'elementor/element/section/section_background/before_section_end', 'add_elementor_section_background_controls' );

// Render section backgrou]d parallax
function render_elementor_section_parallax_background( Elementor\Element_Base $element ) {

    if('section' === $element->get_name()){

        if ( 'yes' === $element->get_settings_for_display( 'th_section_parallax' ) ) {

            $th_background = $element->get_settings_for_display( 'background_image' );
            $th_background_URL = $th_background['url'];

            $element->add_render_attribute( '_wrapper', [
                'class' => 'th-parallax',
                'data-parallax' => 'scroll',
                'data-image-src' => $th_background_URL,
            ] ) ;
        }

    }
}

add_action( 'elementor/frontend/section/before_render', 'render_elementor_section_parallax_background' );


// Future use - Get parallax working in Live Preview.
// https://github.com/pojome/elementor/issues/2588
/*add_action( 'elementor/element/print_template', function( $template, $element ) {
    if ( 'section' === $element->get_name() ) {
        echo '<pre>';
        echo 'OVERHERE';
        echo print_r($element);
        echo print_r($template);
        echo '</pre>';
        //$old_template = '<a href="\' + settings.link.url + \'">\' + title_html + \'</a>';
        //$new_template = '<a href="\' + settings.link.url + \'">\' + title_html + ( settings.link.is_external ? \'<i class="fa fa-external-link" aria-hidden="true"></i>\' : \'\' ) + \'</a>';
        $template = str_replace( 'data-id', 'data-id-zzz', $template );
    }

    return $template;
}, 10, 2 );*/



// Adding custom icons to icon control in Elementor
function th_add_custom_icons_tab( $tabs = array() ) {

    $trip_icons = array_values(array_filter(themo_icons(), function ($key) {return strpos($key, 'th-trip') === 0;}, ARRAY_FILTER_USE_KEY));
    $linea_icons = array_values(array_filter(themo_icons(), function ($key) {return strpos($key, 'th-linea') === 0;}, ARRAY_FILTER_USE_KEY));
    $golf_icons = array_values(array_filter(themo_icons(), function ($key) {return strpos($key, 'th-golf') === 0;}, ARRAY_FILTER_USE_KEY));

	if (!empty($trip_icons)) {
        $tabs['th-trip'] = array(
            'name'          => 'th-trip',
            'label'         => __( 'Themovation Trip', ALOHA_DOMAIN ),
            'labelIcon'     => 'fas fa-icons',
            'prefix'        => 'th-trip travelpack-',
            'displayPrefix' => 'th-trip travelpack',
            'url'           => THEMO_ASSETS_URL . 'icons/icons.css',
            'icons'         => $trip_icons,
            'ver'           => THEMO_VERSION,
        );
    }

    if (!empty($linea_icons)) {
        $tabs['th-linea'] = array(
            'name'          => 'th-linea',
            'label'         => __( 'Themovation Linea', ALOHA_DOMAIN ),
            'labelIcon'     => 'fas fa-icons',
            'prefix'        => 'th-linea icon-',
            'displayPrefix' => 'th-linea icon',
            'url'           => THEMO_ASSETS_URL . 'icons/icons.css',
            'icons'         => $linea_icons,
            'ver'           => THEMO_VERSION,
        );
    }
    
    if (!empty($golf_icons)) {
        $tabs['th-golf'] = array(
            'name'          => 'th-golf',
            'label'         => __( 'Themovation Golf', ALOHA_DOMAIN ),
            'labelIcon'     => 'fas fa-icons',
            'prefix'        => 'th-golf golfpack-',
            'displayPrefix' => 'th-golf golfpack',
            'url'           => THEMO_ASSETS_URL . 'icons/golf_icons.css',
            'icons'         => $golf_icons,
            'ver'           => THEMO_VERSION,
        );
    }



	return $tabs;
}

add_filter( 'elementor/icons_manager/additional_tabs', 'th_add_custom_icons_tab' );


function aloha_print_calendar_styles($settings, $suffix) {
    $styles = ['date_booked_color' => '--mphb-booked-date-bg', 'date_available_color' => '--mphb-available-date-bg'];
    $css = '';
    foreach ($styles as $key => $value) {
        if (isset($settings[$key]) && !empty($settings[$key])) {
            $css .= $value . ': ' . $settings[$key] . ';';
        }
    }
    if (!empty($css)) {
        echo '<style>'
        . ':root .datepick-popup .mphb-datepick-popup.' . $suffix . ', '
        . ':root .datepick-popup [class*="mphb-datepicker-"].mphb-datepick-popup.' . $suffix . ''
        . '{' . $css . '}'
        . '</style>';
    }
}
