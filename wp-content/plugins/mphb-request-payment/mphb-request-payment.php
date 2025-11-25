<?php

/*
 * Plugin Name: Hotel Booking Payment Request
 * Plugin URI: https://motopress.com/products/hotel-booking-payment-request/
 * Description: Send your clients a payment link, which redirects them to an online checkout page to pay the booking balance or the full balance in advance.
 * Version: 2.0.0
 * Author: MotoPress
 * Author URI: https://motopress.com/
 * License: GPLv2 or later
 * Text Domain: mphb-request-payment
 * Domain Path: /languages
 */

namespace MPHB\Addons\RequestPayment;

if (!class_exists('MPHB\Addons\RequestPayment\Plugin') ) {
    define(__NAMESPACE__ . '\ROOT', plugin_dir_path(__FILE__));
    define(__NAMESPACE__ . '\ROOT_URL', plugin_dir_url(__FILE__));
    define(__NAMESPACE__ . '\SLUG', wp_basename(__FILE__, '.php')); // "mphb-request-payment"

    include ROOT . 'includes/loader.php';

    global $mphbrp;
    $mphbrp = Plugin::getInstance();

    register_activation_hook(__FILE__, array($mphbrp, 'onActivate'));
    register_deactivation_hook(__FILE__, array($mphbrp, 'onDeactivate'));
}
