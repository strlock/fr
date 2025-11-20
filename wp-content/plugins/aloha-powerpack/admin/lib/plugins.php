<?php

/**
 * plugins lib for processing backend plugins
 *
 */

define('ALOHA_PLUGIN_LIST_URL', 'https://update.bellevuetheme.com/bellevue-plugins.json');
define('ALOHA_PLUGIN_LIST_KEY', 'aloha_plugins_list');
require_once 'class-tgm-plugin-activation.php';
if (!function_exists('get_plugins')) {
    require_once ABSPATH . 'wp-admin/includes/plugin.php';
}

class AlohaPlugins {

    static $instance;
    static $plugins;
    static $cantInstallSomePlugins = false;

    public static function getInstance() {
        static $instance;
        $class = __CLASS__;
        if (!$instance instanceof $class) {
            $instance = new $class;
            $instance->init();
        }
        return $instance;
    }

    function init() {
        //call this function to register the status of the theme that is set in a cookie
        //add_action('after_setup_theme', [self::getInstance(), 'getAllPlugins']);
        //register stuff, load css js
        if (isset($_GET['page']) && $_GET['page'] === 'aloha_plugins') {
            add_action('admin_enqueue_scripts', [self::getInstance(), 'loadAssets']);
        }

        add_action('wp_ajax_get_update_url', [self::getInstance(), 'getPluginUpdateURL']);
        add_action('wp_ajax_get_installation_url', [self::getInstance(), 'getPluginInstallationURL']);
        add_action('wp_ajax_aloha_plugin_activate', [self::getInstance(), 'activatePlugin']);
        add_action('wp_ajax_aloha_set_motopress_cookie_setting', [$this, 'setMotoPressCookieSetting']);
        add_action('wp_ajax_aloha_set_motopress_reservation_page_cache_setting', [$this, 'setMotoPressReservationPageCacheSetting']);
    }

    function setMotoPressCookieSetting() {
        $motopress_cookie = filter_var($_POST['motopress_cookie'], FILTER_VALIDATE_BOOLEAN);
        $success = false;
        if (isset($motopress_cookie)) {
            $success = true;
        }

        update_option(ALOHA_MOTOPRESS_COOKIE_OPTION, $motopress_cookie);

        $response = array(
            'success' => $success,
            'message' => $motopress_cookie
        );

        wp_send_json($response);
    }
    
    function setMotoPressReservationPageCacheSetting() {
        $motopress_cookie = filter_var($_POST['motopress_reservation_page_cache'], FILTER_VALIDATE_BOOLEAN);
        $success = false;
        if (isset($motopress_cookie)) {
            $success = true;
        }

        update_option(ALOHA_MOTOPRESS_RESERVATION_CACHE_OPTION, $motopress_cookie);

        $response = array(
            'success' => $success,
            'message' => $motopress_cookie
        );

        wp_send_json($response);
    }
        
    function loadAssets() {
        // Admin Scripts
        $jsTimeModified = filemtime(ALOHA_ADMIN_JS_DIR . '/plugins.js');
        wp_register_script('aloha-plugins-script', ALOHA_ADMIN_JS_DIR_URL . '/plugins.js', array('jquery'), $jsTimeModified, true);
        wp_enqueue_script('aloha-plugins-script');
        wp_localize_script('aloha-plugins-script', 'plugins_params', array(
            'wpnonce' => wp_create_nonce('envato_setup_nonce'),
            '_wpnonce' => wp_create_nonce('updates'),
            'verify_text' => esc_html__('...verifying', 'bellevue'),
            'install_action' => 'get_installation_url',
            'activate_action' => 'aloha_plugin_activate',
            'uninstall_action' => 'delete-plugin',
            'deactivate_action' => 'deactivate_plugin',
            'isThemeRegistered' => aloha_is_theme_registered(),
            'cant_uninstall' => 'You can\'t uninstall this plugin',
            'update_action' => 'get_update_url',
            'uninstall_prompt' => esc_html__('This plugin is required, are you sure?', 'bellevue'),
            'uninstall_prompt_yes' => esc_html__('Yes', 'bellevue'),
            'uninstall_prompt_no' => esc_html__('No', 'bellevue'),
            'uninstall_uninstalling' => esc_html__('Uninstalling', 'bellevue'),
            'uninstall_deactivating' => esc_html__('Deactivating', 'bellevue'),
            'install_failed' => esc_html__('Installation failed. Refresh the page and try again.', 'bellevue'),
        ));
    }

    function getPluginUpdateURL() {
        if (isset($_POST['slug'])) {
            $json = array(
                'url' => 'themes.php?page=tgmpa-install-plugins',
                'plugin' => array($_POST['slug']),
                'tgmpa-page' => 'tgmpa-install-plugins',
                'plugin_status' => 'all',
                '_wpnonce' => wp_create_nonce('bulk-plugins'),
                'action' => 'tgmpa-bulk-update',
                'action2' => - 1,
                'message' => esc_html__('Updating Plugin', 'bellevue'),
            );
            wp_send_json($json);
        }
        wp_send_json_error(array('success' => false, 'message' => 'Error Updating'));
    }

    function getPluginFileName($slug) {
        $all_plugins = get_plugins(); //wp-function with updated plugin info
        $filename = $slug;
        if (!isset($all_plugins[$slug])) {
            //otherwise, we need to find it by slug
            //slug example envato-market

            foreach ($all_plugins as $file => $plugin) {
                $file_temp = explode("/", $file);
                if ($file_temp[0] === $_POST['plugin']) {
                    $filename = $file;
                    break;
                }
            }
        }

        return $filename;
    }

    function activatePlugin() {
        if (isset($_POST['plugin'])) {
            $filename = $this->getPluginFileName($_POST['plugin']);
            $result = activate_plugin($filename);
            if (is_wp_error($result)) {
                wp_send_json_error(array('success' => false, 'message' => $result->get_error_message()));
            } else {
                $deactivateURL = wp_nonce_url('plugins.php?action=deactivate&plugin=' . ($filename) . '&plugin_status=' . 'all' . '&paged=' . 1 . '&s=' . '', 'deactivate-plugin_' . $filename);
                wp_send_json(array('success' => true, 'deactivate_url'=>$deactivateURL, 'plugin_file'=>$filename, 'message' => 'Activated'));
            }
        }

        wp_send_json_error(array('success' => false, 'message' => 'Error Activating'));
    }


    function getPluginInstallationURL() {
        if (isset($_POST['slug'])) {
            $json = array(
                'url' => 'themes.php?page=tgmpa-install-plugins',
                'plugin' => array($_POST['slug']),
                'tgmpa-page' => 'tgmpa-install-plugins',
                'plugin_status' => 'all',
                '_wpnonce' => wp_create_nonce('bulk-plugins'),
                'action' => 'tgmpa-bulk-install',
                'action2' => - 1,
                'message' => esc_html__('Installing Plugin', 'bellevue'),
            );
            wp_send_json($json);
        }
        wp_send_json_error(array('success' => false, 'message' => 'Error Activating'));
    }

    /** see if any plugin needs updating * */
    function doPluginsNeedUpdate() {
        $plugins = $this->getAllPlugins();
        foreach ($plugins as $plug) {
            //if a plugin is active and installed, and has an update
            if (!$plug['install'] && !$plug['activate'] && isset($plug['update']) && $plug['update'] ) {
                return true;
            }
        }
        return false;
    }

    private function registerPluginList() {
        if (is_themovation_template()) {
            $TGMPA_ID = apply_filters('aloha_tgmpa_id', '');
            if (empty($TGMPA_ID)) {
                error_log('TGMPA ID not defined');
                return false;
            }
        } else {
            $TGMPA_ID = ALOHA_DOMAIN;
        }

        /**
         * Array of plugin arrays. Required keys are name and slug.
         * If the source is NOT from the .org repo, then source is also required.
         */
        $pluginsAloha = array(
            // This is an example of how to include a plugin pre-packaged with a theme.
            array(
                'name' => 'Elementor Page Builder', // The plugin name.
                'slug' => 'elementor', // The plugin slug (typically the folder name).
                'required' => true,
                'plugin_doc_link' => "https://elementor.com/help/",
            ),
        );

        $plugins = apply_filters('aloha_plugins_list', $pluginsAloha);
        if (count($plugins)) {
            /*
             * Array of configuration settings. Amend each line as needed.
             *
             * TGMPA will start providing localized text strings soon. If you already have translations of our standard
             * strings available, please help us make TGMPA even better by giving us access to these translations or by
             * sending in a pull-request with .po file(s) with the translations.
             *
             * Only uncomment the strings in the config array if you want to customize the strings.
             */
            $config = array(
                'id' => $TGMPA_ID, // Unique ID for hashing notices for multiple instances of TGMPA.
                'default_path' => '', // Default absolute path to bundled plugins.
                'menu' => 'tgmpa-install-plugins', // Menu slug.
                'has_notices' => false, // Show admin notices or not.
                'dismissable' => true, // If false, a user cannot dismiss the nag message.
                'dismiss_msg' => '', // If 'dismissable' is false, this message will be output at top of nag.
                'is_automatic' => false, // Automatically activate plugins after installation or not.
                'message' => '', // Message to output right before the plugins table.
            );

            tgmpa($plugins, $config);
        }
        return true;
    }

    function fetchPluginList() {
        //save the plugin list in session for 5 minutes
        $list = AlohaSession::get(ALOHA_PLUGIN_LIST_KEY);
        
        if($list && count($list)){
            return $list;
        }
        
        $options = array(
            'timeout' => 10, //seconds
            'headers' => array(
                'Accept' => 'text/json',
            ),
        );

        $result = wp_remote_get(ALOHA_PLUGIN_LIST_URL, $options);
        //if error then return false
        if (is_wp_error($result)) {
            return false;
        } else {
            $resultBody = wp_remote_retrieve_body($result);
            if (!empty($resultBody)) {
                $final_list = [];
                $list = json_decode($resultBody, true);

                if (count($list)) {
                    foreach ($list as $plugin) {
                        $final_list[$plugin['plugin']] = $plugin['plugin_json'];
                    }
                    //also save it in the db to save time
                   AlohaSession::set(ALOHA_PLUGIN_LIST_KEY, $final_list, "+5 minutes");
                }
                return $final_list;
            }

        }
        
        return [];
        
    }

    function getPluginFile(){
        $plugins = $this->getAllPlugins();
        print_r($plugins);exit;
    }
    function getAllPlugins() {
        //this filter wil also pull the plugin list file
        if (!apply_filters('aloha_plugin_server_test', false)) {
            return [];
        }

        $plugin_list = $this->fetchPluginList();
        
        if (!is_array(self::$plugins) && $plugin_list && count($plugin_list)) {
            $status = $this->registerPluginList();

            if (!$status) {
                return [];
            }


            $instance = call_user_func(array(get_class($GLOBALS['tgmpa']), 'get_instance'));

            $plugins = array();
            $isRegistered = aloha_is_theme_registered();
            $uninstall_prompt = ['elementor', 'envato-market', 'kirki', 'motopress-hotel-booking'];
            foreach ($instance->plugins as $slug => $plugin) {

                $plugin['install'] = $instance->is_plugin_installed($slug) === true ? false : true;
                $plugin['activate'] = empty($instance->can_plugin_activate($slug)) ? false : true;

                if (isset($plugin_list[$slug]['sections']['description'])) {
                    $plugin['plugin_decription'] = $plugin_list[$slug]['sections']['description'];
                }

                if (in_array($slug, $uninstall_prompt)) {
                    $plugin['uninstall-prompt'] = true;
                }

                if (!$isRegistered && $slug !== 'motopress-hotel-booking' && $slug !== 'elementor' && $slug !== 'kirki' && $slug !== 'formidable' && $slug !== 'envato-market' && $slug !== 'woocommerce' && $slug !== 'simple-page-ordering') {
                    $plugin['cant_install'] = 1;
                    self::$cantInstallSomePlugins = true;
                }
                
                //only check for updates if can install the plugins and it's already installed (install property will be empty)
                if (isset($plugin_list[$slug]['version']) && !isset($plugin['cant_install']) && !$plugin['install']) {
                    //check for update - set the version in the instance as the update function works on the instance
                    $instance->plugins[$slug]['version'] = $plugin['version'] = $plugin_list[$slug]['version'];
                    $plugin['update'] = empty($instance->does_plugin_have_update($slug)) ? false : true;
                }

                $plugins[$slug] = $plugin;
            }
//            sort($plugins);
            //unset powerpack as it should not be updated from the backend or the Aloha plugins page will break
            unset($plugins['aloha-powerpack']);
            self::$plugins = $plugins;
        }

        return self::$plugins;
    }

    function cantInstallSomePlugins() {
        return self::$cantInstallSomePlugins;
    }

    function getMotoPressCookieOptionValue() {
        return (bool) get_option(ALOHA_MOTOPRESS_COOKIE_OPTION, false);
    }
    
    function getMotopressReservationPageCacheOptionValue() {
        return (bool) get_option(ALOHA_MOTOPRESS_RESERVATION_CACHE_OPTION, false);
    }
    
    function showMotoPressSettings() {
        //if motopress is installed, show the cookie enable/disable option
        $plugins = $this->getAllPlugins();
        if (isset($plugins['motopress-hotel-booking'])) {
            return true;
        }
        return false;
    }
    function showMotoPressReservationCacheOption() {
        //if motopress is installed, show the cookie enable/disable option
        $plugins = $this->getAllPlugins();
        if (isset($plugins['motopress-hotel-booking'])) {
            return true;
        }
        return false;
    }
}

//keep this on, so we load the scripts on the plugins page
$instance = AlohaPlugins::getInstance();
