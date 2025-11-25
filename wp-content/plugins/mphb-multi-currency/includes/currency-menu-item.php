<?php

namespace MPHB\Addons\MPHB_Multi_Currency;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Currency_Menu_Item {

	/**
	 * @see wp_setup_nav_menu_item() to decorate the object
	 */
	public $ID;                           // The term_id if the menu item represents a taxonomy term.
	public $attr_title;                   // The title attribute of the link element for this menu item.
	public $classes = array();            // The array of class attribute values for the link element of this menu item.
	public $db_id;                        // The DB ID of this item as a nav_menu_item object, if it exists (0 if it doesn't exist).
	public $description;                  // The description of this menu item.
	public $menu_item_parent;             // The DB ID of the nav_menu_item that is this item's menu parent, if any. 0 otherwise.
	public $object = 'mphb_currency_menu_item'; // The type of object originally represented, such as "category," "post", or "attachment."
	public $object_id;                    // The DB ID of the original object this menu item represents, e.g. ID for posts and term_id for categories.
	public $post_parent;                  // The DB ID of the original object's parent object, if any (0 otherwise).
	public $post_title;                   // A "no title" label if menu item represents a post that lacks a title.
	public $target;                       // The target attribute of the link element for this menu item.
	public $title;                        // The title of this menu item.
	public $type = 'mphb_currency_menu_item';   // The family of objects originally represented, such as "post_type" or "taxonomy."
	public $type_label;                   // The singular label used to describe this type of menu item.
	public $url;                          // The URL to which this menu item points.
	public $xfn;                          // The XFN relationship expressed in the link of this menu item.
	public $_invalid = false;             // Whether the menu item represents an object that no longer exists.
	public $menu_order;
	public $post_type = 'nav_menu_item';  // * Extra property => see [wpmlcore-3855]
	public $status    = 'publish';

	/**
	 * @param $css_classes array of strings (css classes names)
	 */
	public function __construct( string $currency_code, array $css_classes = array(), $parent_menu_item_ID = 0 ) {

		$this->decorate_object( $currency_code, $css_classes, $parent_menu_item_ID );
	}

	private function decorate_object( string $currency_code, array $css_classes = array(), $parent_menu_item_ID = 0 ) {

		$currencyCodeAsInt = '';
		$chars             = str_split( $currency_code );
		foreach ( $chars as $char ) {
			$currencyCodeAsInt .= ord( $char );
		}

		$this->ID               = $currencyCodeAsInt;
		$this->object_id        = $currencyCodeAsInt;
		$this->db_id            = $currencyCodeAsInt;
		$this->menu_item_parent = $parent_menu_item_ID;
		$this->attr_title       = $currency_code;
		$this->title            = $currency_code;
		$this->post_title       = $currency_code;

		global $wp;
		$this->url = add_query_arg( $wp->query_vars, get_site_url( null, $wp->request ) );

		$this->classes[] = 'menu-item';
		$this->classes[] = 'mphbmc-menu-currency-switcher-item';

		if ( isset( $css_classes ) ) {

			foreach ( $css_classes as $class ) {

				$this->classes[] = $class;
			}
		}
	}

	/**
	 * @param string $property
	 * @return mixed
	 */
	public function __get( $property ) {

		return isset( $this->{$property} ) ? $this->{$property} : null;
	}
}
