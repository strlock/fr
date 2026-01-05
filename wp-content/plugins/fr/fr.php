<?php
/*
  Plugin Name: FR
  Description: FR
  Version: 1.00
  Author: MaxxMarketing
  Text Domain: fr
*/

use Elementor\Plugin;

define('FR_THEME_PATH', ABSPATH . "wp-content/themes/bellevuex-child/");

final class FR {
    public function __construct() {
		if (!defined('FR_THEME_PATH')) {
			return;
		}

        add_action( "elementor/widget/posts/skins_init", function($widget) {
            $widget->add_skin( new ElementorPro\Modules\Posts\Skins\Skin_FR( $widget ) );
        }, 11);

        add_filter( 'elementor/widgets/register', function() {
            Plugin::instance()->widgets_manager->unregister('themo-accommodation-listing');
            require_once FR_THEME_PATH . 'aloha-powerpack/elements/accommodation_listing.php';
            Plugin::instance()->widgets_manager->unregister('themo-blog');
            require_once FR_THEME_PATH . 'aloha-powerpack/elements/blog_2.php';
        }, 11);

        add_filter( 'user_has_cap', function( $allcaps, $caps, $args ) {
            // Check if the capability being asked for is Query Monitor's
            if ( 'view_query_monitor' === $args[0] ) {
                // OPTIONAL: Only allow if a secret param is present (?debug=true)
                // if ( isset( $_GET['debug'] ) && $_GET['debug'] === 'true' ) {
                $allcaps['view_query_monitor'] = true;
                // }
            }
            return $allcaps;
        }, 10, 3 );
    }
}

new FR();