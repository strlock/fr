<?php

use IgniteKit\WP\OptionBuilder\Framework;

DEFINE('ALOHA_ADMIN_ASSETS_DIR_URL', plugins_url('assets', __FILE__));
DEFINE('ALOHA_ADMIN_IMAGES_DIR_URL', plugins_url('assets/images', __FILE__));
DEFINE('ALOHA_ADMIN_JS_DIR_URL', plugins_url('assets/js', __FILE__));
DEFINE('ALOHA_ADMIN_CSS_DIR_URL', plugins_url('assets/css', __FILE__));

DEFINE('ALOHA_ADMIN_ASSETS_DIR', __DIR__ . '/assets');
DEFINE('ALOHA_ADMIN_IMAGES_DIR', ALOHA_ADMIN_ASSETS_DIR . '/images');
DEFINE('ALOHA_ADMIN_JS_DIR', ALOHA_ADMIN_ASSETS_DIR . '/js');
DEFINE('ALOHA_ADMIN_CSS_DIR', ALOHA_ADMIN_ASSETS_DIR . '/css');

DEFINE('ALOHA_ADMIN_LIB_DIR', __DIR__ . '/lib');

DEFINE('ALOHA_SETUP_VIDEO', 'https://www.youtube.com/embed/YJ9a9TDmBCI');
DEFINE('ALOHA_ADMIN_ALOHA_TEXT', 'Aloha');
DEFINE('ALOHA_SYSTEM_RECOMMENDED_PHP_VERSION', '7.4');
DEFINE('ALOHA_SYSTEM_RECOMMENDED_MAX_EXECUTION_TIME', '180');
DEFINE('ALOHA_SYSTEM_RECOMMENDED_MAX_INPUT_TIME', '180');
DEFINE('ALOHA_SYSTEM_RECOMMENDED_MEMORY_LIMIT', '256M');
DEFINE('ALOHA_SYSTEM_RECOMMENDED_POST_MAX_SIZE', '32M');
DEFINE('ALOHA_SYSTEM_RECOMMENDED_UPLOAD_MAX_FILESIZE', '32M');

DEFINE('ALOHA_OPTION_MAILCHIMP_SUBSCRIPTION', 'bellevue_mailchimp_subscription');
DEFINE('ALOHA_TGMPA_ID', 'aloha-powerpack');

DEFINE('ALOHA_ALOHA_SETTINGS_PAGE_SLUG', 'aloha_settings');
DEFINE('ALOHA_OPB_OPTIONS_PAGE_ID', 'aloha_settings_page');

add_action('admin_menu', 'aloha_add_admin_menu');
//add_action('after_setup_theme', 'setup_global_options');
//add_action('admin_init', 'aloha_sync_opb_settings', 1);

require_once ALOHA_ADMIN_LIB_DIR . '/session.php';
require_once ALOHA_ADMIN_LIB_DIR . '/mailchimp.php';
require_once ALOHA_ADMIN_LIB_DIR . '/system_status.php';
require_once ALOHA_ADMIN_LIB_DIR . '/plugins.php';

function aloha_active_page($pageCheck = false) {
    $active = isset($_GET['page']) ? $_GET['page'] : false;
    if (!$active || strpos($active, 'aloha_') !== 0 || !$pageCheck) {
        return false;
    }

    $currentPage = str_replace('aloha_', '', $active);
    if ($currentPage === $pageCheck) {
        return true;
    }

    return false;
}

//function aloha_sync_opb_settings() {
//    $postPage = isset($_POST['option_page']) ? esc_attr(wp_unslash($_POST['option_page'])) : '';
//    if (ALOHA_OPB_OPTIONS_PAGE_ID === $postPage && isset($_POST[ALOHA_OPB_OPTIONS_PAGE_ID])) {
//        foreach ($_POST[ALOHA_OPB_OPTIONS_PAGE_ID] as $key => $value) {
//            set_theme_mod($key, $value);
//        }
//    }
//}

function aloha_add_admin_menu() {

    $logo_setup = 'dashicons-admin-network';
    $menu_text = apply_filters('aloha_menu_name', ALOHA_ADMIN_ALOHA_TEXT);
    
    add_menu_page(
            ALOHA_MENU,
            __($menu_text, ALOHA_DOMAIN),
            'manage_options',
            ALOHA_MENU_SLUG,
            'aloha_menu_dashboard',
            $logo_setup,
            3
    );
    add_submenu_page(
            ALOHA_MENU_SLUG,
            __('Welcome', ALOHA_DOMAIN),
            __('Welcome', ALOHA_DOMAIN),
            'manage_options',
            ALOHA_MENU_SLUG,
            'aloha_menu_dashboard'
    );
    if (current_user_can('install_plugins')) {
        add_submenu_page(
                ALOHA_MENU_SLUG,
                __('Plugins', ALOHA_DOMAIN),
                __('Plugins', ALOHA_DOMAIN),
                'manage_options',
                'aloha_plugins',
                'aloha_menu_plugins'
        );
    }

    add_submenu_page(
            ALOHA_MENU_SLUG,
            __('Documentation', ALOHA_DOMAIN),
            __('Documentation', ALOHA_DOMAIN),
            'manage_options',
            'aloha_documentation',
            'aloha_menu_documentation'
    );
    if (isElementorActive()) {
        add_submenu_page(
                ALOHA_MENU_SLUG,
                __('Global Templates', ALOHA_DOMAIN),
                __('Global Templates', ALOHA_DOMAIN),
                'edit_pages',
                'edit.php?post_type=elementor-thhf',
                '', 3
        );
    }
    add_submenu_page(
            ALOHA_MENU_SLUG,
            __('Template Library', ALOHA_DOMAIN),
            __('Template Library', ALOHA_DOMAIN),
            'manage_options',
            'aloha_template_library',
            'aloha_menu_template_library'
    );

    do_action('aloha_register_admin_menu');
}

function aloha_menu_setup() {
    wp_register_style('aloha-google-font-inter', 'https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&display=swap', false, '1');
    wp_enqueue_style('aloha-google-font-inter');
    $modified = filemtime(ALOHA_ADMIN_CSS_DIR . '/admin-dashboard.css');
    wp_enqueue_style('aloha-dashboard', ALOHA_ADMIN_CSS_DIR_URL . '/admin-dashboard.css', array(), $modified);

    $jsTimeModified = filemtime(ALOHA_ADMIN_JS_DIR . '/admin-scripts.js');
    wp_register_script('aloha-admin-scripts', ALOHA_ADMIN_JS_DIR_URL . '/admin-scripts.js', ['jquery'], $jsTimeModified, true);
    wp_enqueue_script('aloha-admin-scripts');
    
    do_action('aloha_admin_init');
}

function aloha_loadMenu($function) {
    $slug = str_replace("aloha_menu_", "", $function);
    $file = __DIR__ . '/' . $slug . '.php';
    if (file_exists($file)) {
        aloha_menu_setup();
        require_once __DIR__ . '/' . $slug . '.php';
    }
}

function aloha_menu_dashboard() {
    aloha_loadMenu(__FUNCTION__);
}

function aloha_menu_documentation() {
    aloha_loadMenu(__FUNCTION__);
}

function aloha_menu_settings() {
    aloha_loadMenu(__FUNCTION__);
}

function aloha_menu_plugins() {
    aloha_loadMenu(__FUNCTION__);
}

function aloha_menu_template_library() {
    aloha_loadMenu(__FUNCTION__);
}