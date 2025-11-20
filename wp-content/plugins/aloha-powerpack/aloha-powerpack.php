<?php

/**
 * Plugin Name: Aloha PowerPack
 * Version: 1.2.12
 * Plugin URI: https://help.bellevuetheme.com/
 * Description: Elementor PowerPack for Hotels and Vacation Rentals
 * Author: PixelMakers
 * Author URI: https://help.bellevuetheme.com/
 * Text Domain: aloha-powerpack
 * Domain Path: /languages/
 * License: GPL v3
 */
use IgniteKit\WP\OptionBuilder\Framework;
define('ALOHA_WIDGET_PACK_PLUGIN', 'th-widget-pack/th-widget-pack.php');
define('ALOHA_HFE_PLUGIN_FILE', 'header-footer-elementor/header-footer-elementor.php');
define('ALOHA_HFE_ERROR','Elementor Header and Footer plugin is not compatible with Aloha Powerpack. Before you deactivate you may wish to export / import any Header Footer templates and import them as Global Templates under the Bellevue Admin Menu.');
define('ALOHA_ELEMENTOR_FILE', 'elementor/elementor.php');
define('ALOHA_MIN_THEME_VERSION_REQUIREMENT', '4.0');
define('ALOHA_THEME_VERSION_ERROR', 'Aloha Powerpack requires a minimum theme version of %1$s%2$s%3$s.');
define('ALOHA_ELEMENTOR_ERROR', 'Aloha Powerpack requires Elementor.');
define('ALOHA_DOMAIN', 'aloha-powerpack');
define('ALOHA_INIT_ERROR_1', 'You can safely deactivate & uninstall the %1$sPage Builder Widget Pack%2$s plugin. It is no longer needed.');
define('ALOHA__FILE', __FILE__);
define('ALOHA_PLUGIN_BASE', plugin_basename(ALOHA__FILE));
define('ALOHA_OPTION_TREE_PLUGIN', 'option-tree/ot-loader.php');
DEFINE('ALOHA_OPTION_TREE_NOTICE', 'You can safely deactivate & uninstall the OptionTree plugin.');

define('ALOHA_MENU', 'Aloha');
define('ALOHA_MENU_SLUG', 'aloha_dashboard');
define('ALOHA_SETTING_BUTTON_STYLE_ID', 'themo_button_style');
define('ALOHA_SETTING_BUTTON_STYLE_DEFAULT', 'round');
define('ALOHA_SETTING_BUTTON_BLOG_EXCERPTS_ID', 'themo_automatic_post_excerpts');
define('ALOHA_SETTING_BUTTON_BLOG_EXCERPTS_DEFAULT', 'on');
define('ALOHA_WIDGETS_HELP_URL_PREFIX', 'https://link.bellevuetheme.com/');

define('ALOHA_BUTTON_STYLE_ID', 'aloha-button-style');
define('ALOHA_BUTTON_STYLE_PREFIX', 'button-styles-');
define('ALOHA_ELEMENTOR_CALENDAR_CLASS_PREFIX', 'elementor-calendar-id-');
define('ALOHA_ELEMENTOR_CALENDAR_AND_OTHER_FORMS_FILENAME', 'themo-formidable-form-and-other-forms');

define('ALOHA_URL', plugin_dir_url(__FILE__));
define('ALOHA_PATH', __DIR__);
define('ALOHA_CSS_PATH', ALOHA_PATH . '/css');
define('ALOHA_CSS_URL', ALOHA_URL . 'css');
define('ALOHA_JS_PATH', ALOHA_PATH . '/js');
define('ALOHA_JS_URL', ALOHA_URL . 'js');

define('ALOHA_GLOBAL_CSS_HANDLE', 'thmv-global');

define('ALOHA_HFE_PATH', ALOHA_PATH . '/library/header-footer');
define('ALOHA_HFE_OLD_POST_TYPE', 'elementor-hf');
define('ALOHA_HFE_NEW_POST_TYPE', 'elementor-thhf');

define('ALOHA_TEMPLATE_LIBRARY_PATH', ALOHA_PATH . '/library/template-library');
define('ALOHA_TEMPLATE_LIBRARY_URL', ALOHA_URL . 'library/template-library/');

add_action('plugins_loaded', 'aloha_init_check');
add_action('wp_enqueue_scripts', 'aloha_frontend_css_actions', 99);
add_action('elementor/kit/register_tabs', 'aloha_elementor_register_tabs');
add_action('admin_enqueue_scripts', 'aloha_load_backend_script');

define('ALOHA_TYPOGRAPHY_DEFAULT_HEADINGS_COLOR', '#3E3E3E');
define('ALOHA_ELEMENTOR_TABS_DIR', ALOHA_PATH . '/elementor_settings');
define('ALOHA_KITS_PATH', ALOHA_ELEMENTOR_TABS_DIR . '/kits');
DEFINE('ALOHA_KIT_KEY', 'aloha-active-kit');

define('ALOHA_MOTOPRESS_COOKIE_OPTION', 'aloha_motopress_cookie_disabled');
define('ALOHA_MOTOPRESS_RESERVATION_CACHE_OPTION', 'aloha_motopress_reservation_page_cache_disabled');

define('ALOHA_MAPS_OPTION', 'aloha_maps_key');
if (!defined('ALOHA_CURL_ERROR_HELP_URL')) {
    DEFINE('ALOHA_CURL_ERROR_HELP_URL', 'https://help.bellevuetheme.com/article/290-how-to-fix-curl-error-28-connection-timed-out');
}
define('ALOHA_REST_HELPER_URL', 'https://service.bellevuetheme.com/wp-json/aloha-helper/');


function registerSiteActions() {
    add_filter('body_class', 'aloha_css_class');
}

add_filter('aloha_plugin_server_test', 'aloha_plugin_server_test');
/**
 * Check if the update site can be connected to
 * save the info for 5 minutes so as not to hog the system
 */
function aloha_plugin_server_test() {
    require_once ALOHA_PATH . '/admin/lib/session.php';
    require_once ALOHA_ADMIN_LIB_DIR . '/plugins.php';
    
    $key = 'plugin_server_errror';

    if (AlohaSession::get($key)) {
        //if we had error, we saved it in db not to repeat connecting again
        return false; //test failed 
    }
    //laod from the plugins
    $instance = AlohaPlugins::getInstance();
    $result = $instance->fetchPluginList();
    if (!$result) {
        //if false
        //save the info in the session for 5 minutes
        AlohaSession::set($key, true, "+5 minutes");
        return false; //test failed 
    } else {
        AlohaSession::delete($key);
    }

    return true;
}

function aloha_css_class($classes) {
    $classes[] = ' aloha-active';
    return $classes;
}

function aloha_elementor_register_tabs($kit) {
    require_once ALOHA_ELEMENTOR_TABS_DIR . '/misc.php';
    $kit->register_tab('aloha-settings-misc', 'Aloha_Settings_Misc');

    require_once ALOHA_ELEMENTOR_TABS_DIR . '/blog.php';
    $kit->register_tab('aloha-settings-blog', 'Aloha_Settings_Blog');

    //if booking component is installed?
    require_once ALOHA_ELEMENTOR_TABS_DIR . '/booking.php';
    $kit->register_tab('aloha-settings-booking', 'Aloha_Settings_Booking');
    
    require_once ALOHA_ELEMENTOR_TABS_DIR . '/style_switcher.php';
    $kit->register_tab('aloha-settings-style-switcher', 'Aloha_Settings_Style_Switcher');
}

function isElementorActive() {
    if (defined('ELEMENTOR_VERSION') && is_callable('Elementor\Plugin::instance')) {
        return true;
    }

    return false;
}

function aloha_load_backend_script() {
    $format_types = ['quote', 'gallery', 'audio', 'link', 'video'];
    $currentFormat = get_post_format();

    $isGutenberg = false;
    if (function_exists('is_gutenberg_page') && is_gutenberg_page()) {
        $isGutenberg = true;
    }

    $current_screen = get_current_screen();
    if (method_exists($current_screen, 'is_block_editor') && $current_screen->is_block_editor()) {
        $isGutenberg = true;
    }

    $file = 'aloha-admin.js';
    $modified = filemtime(ALOHA_JS_PATH . '/' . $file);
    wp_enqueue_script('aloha-admin-main', ALOHA_JS_URL . '/' . $file, array('jquery'), $modified, true);
    wp_localize_script('aloha-admin-main', 'aloha_vars', array(
        'format_types' => $format_types,
        'selected_format_type' => $currentFormat,
        'is_gutenberg' => $isGutenberg,
        'dismissible_nonce' => wp_create_nonce( 'dismissible-notice' )
    ));
}

/**
 * Is admin notice active?
 *
 * @param string $arg data-dismissible content of notice.
 *
 * @return bool
 */
function aloha_is_admin_notice_active($arg) {
    $array = explode('-', $arg);
    $length = array_pop($array);
    $option_name = implode('-', $array);
    $db_record = aloha_get_admin_notice_cache($option_name);

    if ('forever' === $db_record) {
        return false;
    } elseif (absint($db_record) >= time()) {
        return false;
    } else {
        return true;
    }
}

/**
 * Sets admin notice timeout in site option.
 *
 * @access public
 *
 * @param string      $id       Data Identifier.
 * @param string|bool $timeout  Timeout for admin notice.
 *
 * @return bool
 */
function aloha_set_admin_notice_cache($id, $timeout) {
    $cache_key = 'aloha-notices-' . md5($id);
    update_site_option($cache_key, $timeout);

    return true;
}

/**
 * Returns admin notice cached timeout.
 *
 * @access public
 *
 * @param string|bool $id admin notice name or false.
 *
 * @return array|bool The timeout. False if expired.
 */
function aloha_get_admin_notice_cache($id = false) {
    if (!$id) {
        return false;
    }
    $cache_key = 'aloha-notices-' . md5($id);
    $timeout = get_site_option($cache_key);
    $timeout = 'forever' === $timeout ? time() + 60 : $timeout;

    if (empty($timeout) || time() > $timeout) {
        return false;
    }

    return $timeout;
}
add_action( 'wp_ajax_aloha_dismiss_admin_notice', 'aloha_dismiss_admin_notice');

function aloha_dismiss_admin_notice() {
    $option_name = isset($_POST['option_name']) ? sanitize_text_field(wp_unslash($_POST['option_name'])) : '';
    $dismissible_length = isset($_POST['dismissible_length']) ? sanitize_text_field(wp_unslash($_POST['dismissible_length'])) : 0;
    if ('forever' !== $dismissible_length) {
        // If $dismissible_length is not an integer default to 1.
        $dismissible_length = ( 0 === absint($dismissible_length) ) ? 1 : $dismissible_length;
        $dismissible_length = strtotime(absint($dismissible_length) . ' days');
    }

    check_ajax_referer('dismissible-notice', 'nonce');
    aloha_set_admin_notice_cache($option_name, $dismissible_length);
    wp_die();
}

function aloha_frontend_css_actions() {
    $button_style = get_theme_mod(ALOHA_SETTING_BUTTON_STYLE_ID, ALOHA_SETTING_BUTTON_STYLE_DEFAULT);
    $cssFile = ALOHA_BUTTON_STYLE_PREFIX . $button_style . '.css';
    $modified = filemtime(ALOHA_CSS_PATH . '/' . $cssFile);
    wp_enqueue_style(ALOHA_BUTTON_STYLE_ID, ALOHA_CSS_URL . '/' . $cssFile, array(), $modified);
}

function aloha_load_wp_options() {
    require_once __DIR__ . '/library/optionbuilder/autoload.php';
    //we trigger this so the hooks are triggered and CSS is loaded.
    new Framework();
}

function hfe_load_custom_types() {
    //load option tree if not a themovation template
    //register post types
    require_once ALOHA_PATH . '/inc/custom-types.php';
}

function aloha_get_elementor_tab_prefix() {
    if (is_themovation_template()) {
        $theme = wp_get_theme();
        $themeToCheck = $theme->parent() ? $theme->parent() : $theme;
        return $themeToCheck->get('Name');
    }
    return ALOHA_MENU;
}
if (!function_exists('is_themovation_template')) {
    function is_themovation_template()
    {
        $theme = wp_get_theme();
        $themeToCheck = $theme->parent() ? $theme->parent() : $theme;
        $author_array = ['themovation','pixel makers creative inc.'];
        return in_array(strtolower($themeToCheck->get('Author')), $author_array);
    }
}

function aloha_template_compat() {
    if (is_themovation_template()) {
        $theme = wp_get_theme();
        $themeToCheck = $theme->parent() ? $theme->parent() : $theme;
        $thisTemplateVersion = $themeToCheck->get('Version');
        if (version_compare($thisTemplateVersion, ALOHA_MIN_THEME_VERSION_REQUIREMENT) >= 0) {
            return true;
        }
        return false;
    }

    return true;
}

/**
 * error will show up when the widget pack is activated while this plugin is active too
 * @return type
 */
function aloha_init_check() {
    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
    $isPluginActive = is_plugin_active(ALOHA_WIDGET_PACK_PLUGIN);
    $installed = file_exists(WP_PLUGIN_DIR.'/'.ALOHA_WIDGET_PACK_PLUGIN);
    //for the backend, show the error message
    if (is_admin()) {
        
        $user_can_edit_plugins = ( current_user_can('activate_plugins') && current_user_can('install_plugins') );
        
        //don't show during the installation
        if (isset($_REQUEST['action']) && $_REQUEST['action'] === 'install-plugin') {
            return;
        }

        if (!aloha_template_compat()) {
            if($user_can_edit_plugins){
                add_action('admin_notices', 'aloha_error_update_template');
            }
            
            return;
        }
        if (!isElementorActive()) {
            if($user_can_edit_plugins){
                add_action('admin_notices', 'aloha_error_elementor_missing');
            }
            
            return;
        }
        if (!$isPluginActive && $installed) {
            if($user_can_edit_plugins){
                add_action('admin_notices', 'aloha_error_disable_th_widget_pack');
            }
        }
        
        if ($isPluginActive) {
            if($user_can_edit_plugins){
                add_action('admin_notices', 'aloha_error_disable_th_widget_pack');
            }
            
        } else {
            if($user_can_edit_plugins){
                add_action('admin_notices', 'aloha_option_tree_message');
            }
            aloha_init();
        }
    } else if (!$isPluginActive) {
        aloha_init();
    }
}

//if option tree is still present - show a dismissable message
function aloha_option_tree_message() {
    if (is_plugin_active(ALOHA_OPTION_TREE_PLUGIN) || file_exists(WP_PLUGIN_DIR . '/' . ALOHA_OPTION_TREE_PLUGIN)) {
        //hide for a day
        $key = 'option-tree-delete-notice-1';
        if (!aloha_is_admin_notice_active($key)) {
            return;
        }
        $class = 'notice-warning notice is-dismissible';
        
        printf('<div class="%1$s" data-dismissible="%3$s"><p>%2$s</p></div>', esc_attr($class), __(ALOHA_OPTION_TREE_NOTICE, ALOHA_DOMAIN), $key);
    }
}
function aloha_error_disable_HFE() {
    $action_url = wp_nonce_url('plugins.php?action=deactivate&amp;plugin=' . ALOHA_HFE_PLUGIN_FILE . '&amp;plugin_status=all&amp;paged=1&amp;s', 'deactivate-plugin_' . ALOHA_HFE_PLUGIN_FILE);
    $button_label = __('Deactivate', ALOHA_DOMAIN);
    $button = '<p><a href="' . $action_url . '" class="button-primary">' . $button_label . '</a></p><p></p>';

    $class = 'notice notice-error';
    $message = sprintf(__(ALOHA_HFE_ERROR, ALOHA_DOMAIN), '<strong>', '</strong>');
    printf('<div class="%1$s"><p>%2$s</p>%3$s</div>', esc_attr($class), wp_kses_post($message), wp_kses_post($button));
}
function aloha_error_disable_th_widget_pack() {
    $action_url = wp_nonce_url('plugins.php?action=deactivate&amp;plugin=' . ALOHA_WIDGET_PACK_PLUGIN . '&amp;plugin_status=all&amp;paged=1&amp;s', 'deactivate-plugin_' . ALOHA_WIDGET_PACK_PLUGIN);
    $button_label = __('Deactivate Widget Pack', ALOHA_DOMAIN);
    $button = '<p><a href="' . $action_url . '" class="button-primary">' . $button_label . '</a></p><p></p>';

    $class = 'notice notice-error';
    $message = sprintf(__(ALOHA_INIT_ERROR_1, ALOHA_DOMAIN), '<strong>', '</strong>');
    $isPluginActive = is_plugin_active(ALOHA_WIDGET_PACK_PLUGIN);
    if(!$isPluginActive){
        $button = '';
    }
    printf('<div class="%1$s"><p>%2$s</p>%3$s</div>', esc_attr($class), wp_kses_post($message), wp_kses_post($button));
}

function aloha_error_update_template() {
    $action_url = admin_url('themes.php');
    $button_label = __('Update template', ALOHA_DOMAIN);
    $button = '<p><a href="' . $action_url . '" class="button-primary">' . $button_label . '</a></p><p></p>';

    $class = 'notice notice-error';
    $message = sprintf(__(ALOHA_THEME_VERSION_ERROR, ALOHA_DOMAIN), '<strong>', ALOHA_MIN_THEME_VERSION_REQUIREMENT, '</strong>');

    printf('<div class="%1$s"><p>%2$s</p>%3$s</div>', esc_attr($class), wp_kses_post($message), wp_kses_post($button));
}

function aloha_error_elementor_missing() {
    //check if missing or just deactivated
    $exists = file_exists(ABSPATH . '/wp-content/plugins/' . ALOHA_ELEMENTOR_FILE);
    if (!$exists) {
        //missing
        $action_url = wp_nonce_url(self_admin_url('update.php?action=install-plugin&plugin=elementor'), 'install-plugin_elementor');
        $button_label = __('Install Elementor', ALOHA_DOMAIN);
    } else {
        //activation message
        $button_label = __('Activate Elementor', ALOHA_DOMAIN);
        $action_url = wp_nonce_url('plugins.php?action=activate&amp;plugin=' . ALOHA_ELEMENTOR_FILE . '&amp;plugin_status=all&amp;paged=1&amp;s', 'activate-plugin_' . ALOHA_ELEMENTOR_FILE);
    }

    $message = __(ALOHA_ELEMENTOR_ERROR, ALOHA_DOMAIN);

    $button = '<p><a href="' . $action_url . '" class="button-primary">' . $button_label . '</a></p><p></p>';

    $class = 'notice notice-error';

    printf('<div class="%1$s"><p>%2$s</p>%3$s</div>', esc_attr($class), wp_kses_post($message), wp_kses_post($button));
}

register_activation_hook(__FILE__, 'aloha_activate');

function aloha_register_image_sizes() {
    if (function_exists('add_image_size')) {
        add_image_size('th_img_xs', 0, 80); // 80 high
        add_image_size('th_img_sm_landscape', 394, 303, array('center', 'center')); // 394 w / 303 h
        add_image_size('th_img_sm_portrait', 394, 512, array('center', 'center')); // 394 w / 512 h
        add_image_size('th_img_sm_square', 394, 394, array('center', 'center')); // 394 w / 394 h
        add_image_size('th_img_sm_standard', 394, 303); // 394 w / 303 h

        add_image_size('th_img_md_landscape', 605, 465, array('center', 'center')); // 605 w / 465 h
        add_image_size('th_img_md_portrait', 605, 806, array('center', 'center')); // 394 w / 806 h
        add_image_size('th_img_md_square', 605, 605, array('center', 'center')); // 605 w / 605 h

        add_image_size('th_img_lg', 915, 700); // 915 w / 700 h
        add_image_size('th_img_xl', 1240, 950); // 1240 w / 700 h
        add_image_size('th_img_xxl', 1920, 1080); // 915 w / 700 h
    }
}

function aloha_activate() {
    //try to disable plugins automatically
    aloha_disable_old_plugins();
    //if the widget pack is still active, warn the user
    if (is_plugin_active(ALOHA_WIDGET_PACK_PLUGIN)) {
        $message = sprintf(__(ALOHA_INIT_ERROR_1, ALOHA_DOMAIN), '<strong>', '</strong>');
        //prevent activation
        die($message);
    }
    if (!aloha_template_compat()) {
        $message = sprintf(__(ALOHA_THEME_VERSION_ERROR, ALOHA_DOMAIN), '<strong>', ALOHA_MIN_THEME_VERSION_REQUIREMENT, '</strong>');
        //prevent activation
        die($message);
    }
    //@todo, do this properly. 
    //wpml_update_text_domain_to_aloha();
}

function aloha_init() {
    if (!is_plugin_active(ALOHA_WIDGET_PACK_PLUGIN)) {
        if (is_admin()) {
            require_once __DIR__ . '/admin/aloha_menu.php';
        }
        if (!is_admin()) {
            registerSiteActions();
        }

        //register image sizes
        aloha_register_image_sizes();
        //disable plugins not required by aloha        
        require_once 'aloha_setup.php';
        
        $isHFEActive = is_plugin_active(ALOHA_HFE_PLUGIN_FILE);
        if($isHFEActive){
          add_action('admin_notices', 'aloha_error_disable_HFE');
        }
        else{
           require_once ALOHA_PATH . '/header-footer/aloha_hfe_overrides.php';
        }
        hfe_load_custom_types();
        aloha_motopress_cookie_fix();
        
        add_action('template_redirect', 'aloha_motopress_reservation_page_cache_fix');
        
        add_action('init', function(){
            th_translation_ready();

        });
    }
}

function aloha_motopress_cookie_fix() {
    //if the setting is set to disable, do it
    $disableCookie = get_option(ALOHA_MOTOPRESS_COOKIE_OPTION, false);
    if (is_plugin_active('motopress-hotel-booking/motopress-hotel-booking.php') && $disableCookie) {
        $fileName = ABSPATH . 'wp-content/plugins/motopress-hotel-booking/includes/libraries/wp-session-manager/class-wp-session.php';
        if (file_exists($fileName)) {
            $sessionFileContentsRaw = file_get_contents($fileName);
            $sessionFileContents = preg_replace('/<\?php/', '/**STARTING PHP REPLACED **/', $sessionFileContentsRaw, 1);
            $fileNew = str_replace('$this->set_cookie();', '', $sessionFileContents);
            @eval($fileNew);
        }
    }
}



function aloha_motopress_reservation_page_cache_fix() {
   
    if (is_plugin_active('motopress-hotel-booking/motopress-hotel-booking.php')) {
        $disableCache = get_option(ALOHA_MOTOPRESS_RESERVATION_CACHE_OPTION, false);
        if ($disableCache && isset($_GET['mphb_check_in_date'])) {
            header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
            header("Cache-Control: post-check=0, pre-check=0", false);
            header("Pragma: no-cache");
        }
    }
}

function aloha_disable_old_plugins() {
    deactivate_plugins(ALOHA_WIDGET_PACK_PLUGIN);
    deactivate_plugins(ALOHA_OPTION_TREE_PLUGIN);
}

function wpml_update_text_domain_to_aloha() {
    if (!get_option('aloha_text_domain_changed', false) && is_admin()) {

        //first check if there is some strings already translated under the domain aloha-powerpack
        //if not, then modify the existing ones to aloha-powerpack domain
        global $wpdb;
        $table = $wpdb->prefix . 'icl_strings';
        $table2 = $wpdb->prefix . 'icl_string_translations';
        $tabl3 = $wpdb->prefix . 'icl_string_positions';

        $query = $wpdb->prepare("SELECT COUNT(t.id) FROM $table2 AS t JOIN $table AS s ON s.id=t.string_id WHERE s.context='" . ALOHA_DOMAIN . "';");
        $result = (INT) $wpdb->get_var($query);
        if ($result === 0) {
            $query = "DELETE FROM wp_icl_strings WHERE context='" . ALOHA_DOMAIN . "'";
            $result2 = $wpdb->query($query);

            //get all the string ids that are found in the files, that's the only for some otherwise we can mistaknely translate other domains like bellevue the template
            $query = "SELECT GROUP_CONCAT(string_id) FROM $tabl3 WHERE position_in_page LIKE '%th-widget-pack%';";
            $stringIds = $wpdb->get_var($query);
            if ($stringIds) {
                $query = "UPDATE $table SET domain_name_context_md5 =MD5(CONCAT(`context`,`name`,`gettext_context`)), context='" . ALOHA_DOMAIN . "' WHERE id IN(" . $stringIds . ");";
                $result3 = $wpdb->query($query);
            }
        }

        add_option('aloha_text_domain_changed', 1);
    }
}

function aloha_hfe_get_elementor_instance() {
    if (defined('ELEMENTOR_VERSION') && is_callable('Elementor\Plugin::instance')) {
        return $elementor_instance = Elementor\Plugin::instance();
    }
    return false;
}
//call after theme has loaded
function aloha_is_theme_registered() {
    if (function_exists('thmv_is_registered')) {
        return thmv_is_registered();
    }
    return false;
}

if (!function_exists('showLibrary')) {
    function showLibrary()
    {
        return is_user_logged_in() && (ENABLE_BLOCK_LIBRARY === true) && (is_themovation_template() && aloha_is_theme_registered());
    }
}
