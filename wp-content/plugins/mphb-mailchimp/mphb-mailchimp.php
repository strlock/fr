<?php

/*
 * Plugin Name: Hotel Booking & Mailchimp Integration
 * Plugin URI: https://motopress.com/products/hotel-booking-mailchimp/
 * Description: Instantly connect Mailchimp with your hotel website to send stylish emails with significant booking data, helpful automated messages and targeted marketing campaigns.
 * Version: 1.0.2
 * Author: MotoPress
 * Author URI: https://motopress.com/
 * License: GPLv2 or later
 * Text Domain: mphb-mailchimp
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) {
    exit;
}

if (version_compare(PHP_VERSION, '5.4', '<')) {
    // Don't show multiple duplicate notices when multiple instances of the
    // plugin are active
    if (!function_exists('mphb_mc_php_version_error_notice')) {
        add_action('init', 'mphb_mc_load_translations');
        add_action('admin_notices', 'mphb_mc_php_version_error_notice');

        function mphb_mc_load_translations()
        {
            $pluginDir = plugin_dir_path(__FILE__);
            $pluginDir = plugin_basename($pluginDir); // "mphb-mailchimp" or renamed name

            load_plugin_textdomain('mphb-mailchimp', false, $pluginDir . '/languages');
        }

        function mphb_mc_php_version_error_notice()
        {
            echo '<div class="error"><p>' . esc_html__( 'Your version of PHP is below the minimum version of PHP required by Hotel Booking Mailchimp Addon. Please contact your host and request that your version be upgraded to 5.4 or later.', 'mphb-mailchimp') . '</p></div>';
        }
    }

} else if (!class_exists('\MPHB\Addons\MailChimp\Plugin')) {
    define('MPHB\Addons\MailChimp\PLUGIN_FILE', __FILE__);
    define('MPHB\Addons\MailChimp\PLUGIN_DIR', plugin_dir_path(__FILE__)); // With trailing slash
    define('MPHB\Addons\MailChimp\PLUGIN_URL', plugin_dir_url(__FILE__)); // With trailing slash

    require __DIR__ . '/includes/functions.php';
    require __DIR__ . '/includes/template-functions.php';
    require __DIR__ . '/includes/autoloader.php';

    register_activation_hook(__FILE__, 'mphb_mc_create_tables');

    mphbmc();

    require __DIR__ . '/includes/actions-and-filters.php';
}
