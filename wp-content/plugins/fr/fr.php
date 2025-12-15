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
    }
}

new FR();