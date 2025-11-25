<?php

/**
 * @return \MPHB\Addons\MailChimp\Plugin
 */
function mphbmc()
{
    return \MPHB\Addons\MailChimp\Plugin::getInstance();
}

function mphb_mc_array_absint(&$array, $columnKey)
{
    array_walk($array, function (&$item) use ($columnKey) {
        $item[$columnKey] = absint($item[$columnKey]);
    });
}

/**
 * @param \MPHB\Entities\Booking $booking
 * @return bool
 */
function mphb_mc_booking_paid($booking)
{
    return mphb()->settings()->main()->getConfirmationMode() == 'payment'
        && \MPHB\Addons\MailChimp\Utils\BookingUtils::getToPayPrice($booking) == 0;
}

/**
 * @return bool
 */
function mphb_mc_carts_available()
{
    // No need to create carts in MailChimp when admin does all the work manually
    return mphb()->settings()->main()->getConfirmationMode() !== 'manual';
}

/**
 * @global \wpdb $wpdb
 */
function mphb_mc_create_tables()
{
    global $wpdb;

    $wpdb->query("CREATE TABLE IF NOT EXISTS {$wpdb->prefix}mphb_mailchimp_lists ("
        . "list_id INT NOT NULL AUTO_INCREMENT, "
        . "remote_id VARCHAR(32) NOT NULL, "
        . "list_name VARCHAR(200) NOT NULL, "
        . "sync_status VARCHAR(16) NOT NULL DEFAULT 'synced', "
        . "PRIMARY KEY (list_id)"
        . ") CHARSET=utf8 AUTO_INCREMENT=1");

    $wpdb->query("CREATE TABLE IF NOT EXISTS {$wpdb->prefix}mphb_mailchimp_categories ("
        . "category_id INT NOT NULL AUTO_INCREMENT, "
        . "list_id INT NOT NULL, "
        . "remote_id VARCHAR(32) NOT NULL, "
        . "category_name VARCHAR(200) NOT NULL, "
        . "PRIMARY KEY (category_id)"
        . ") CHARSET=utf8 AUTO_INCREMENT=1");

    $wpdb->query("CREATE TABLE IF NOT EXISTS {$wpdb->prefix}mphb_mailchimp_groups ("
        . "group_id INT NOT NULL AUTO_INCREMENT, "
        . "category_id INT NOT NULL, "
        . "remote_id VARCHAR(32) NOT NULL, "
        . "group_name VARCHAR(200) NOT NULL, "
        . "PRIMARY KEY (group_id)"
        . ") CHARSET=utf8 AUTO_INCREMENT=1");
}

/**
 * @return string "Region/City" or "UTC+2".
 */
function mphb_mc_get_wp_timezone()
{
    $timezone = get_option('timezone_string', '');

    if (empty($timezone)) {
        $gmtOffset = (float)get_option('gmt_offset', 0); // -2.5

        $hours = abs((int)$gmtOffset); // 2

        $minutes = abs($gmtOffset) - $hours; // 0.5
        $minutes = round($minutes * 4) / 4; // Only 0, 0.25, 0.5, 0.75 or 1
        $minutes = (int)($minutes * 60); // Only 0, 15, 30, 45 or 60

        if ($minutes == 60) {
            $hours++;
            $minutes = 0;
        }

        $timezone = $gmtOffset >= 0 ? 'UTC+' : 'UTC-';
        $timezone .= $hours;

        if ($minutes > 0) {
            $timezone .= ':' . $minutes;
        }
    }

    return $timezone;
}

/**
 * @param string $file
 * @return string
 */
function mphb_mc_url_to($file)
{
    return \MPHB\Addons\MailChimp\PLUGIN_URL . $file;
}

function mphb_mc_use_edd_license()
{
    return (bool)apply_filters('mphb_mc_use_edd_license', true);
}
