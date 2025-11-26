<?php
/*
  Plugin Name: FR
  Description: FR
  Version: 1.00
  Author: MaxxMarketing
  Text Domain: fr
*/

use Elementor\Plugin;

final class FR {
    public function __construct() {
        add_filter( 'elementor/widgets/register', function() {
            Plugin::instance()->widgets_manager->unregister('themo-accommodation-listing');
            require_once FR_THEME_PATH . 'aloha-powerpack/elements/accommodation_listing.php';
        }, 11);
    }
}

new FR();