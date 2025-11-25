<?php

namespace MPHB\Addons\MPHB_Multi_Currency;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Menu_Currency_Switcher_Handler {

	public function __construct() {

		$mphbmc_menu_currency_switcher_settings = MPHB_Multi_Currency::get_menu_currency_switcher_settings();

		if ( ! MPHB_Multi_Currency::is_currency_exchange_rates_on() ||
			is_admin() || empty( $mphbmc_menu_currency_switcher_settings ) ) {
			return;
		}

		add_filter(
			'wp_get_nav_menu_items',
			/**
			 * @param array   $items
			 * @param WP_Term $menu
			 *
			 * @return array
			 */
			function( $items, $menu ) {

				$mphbmc_menu_currency_switcher_settings = MPHB_Multi_Currency::get_menu_currency_switcher_settings();

				$defaultLanguage = apply_filters( 'wpml_default_language', null );
				$current_menu_id = apply_filters( 'wpml_object_id', $menu->term_id, 'nav_menu', true, $defaultLanguage );

				foreach ( $mphbmc_menu_currency_switcher_settings as $currency_switcher_data ) {

					if ( $current_menu_id == $currency_switcher_data['mphbmc_currency_switcher_menu'] ) {

						$currency_switcher_menu_items = $this->get_currency_switcher_menu_items( $currency_switcher_data );

						if ( 'first' == $currency_switcher_data['mphbmc_currency_switcher_position_in_menu'] ) {

							foreach ( $items as $old_item ) {

								$currency_switcher_menu_items[] = $old_item;
							}
							$items = $currency_switcher_menu_items;

							// reorder all menu items
							for ( $i = 0; $i < count( $items ); $i++ ) {

								$items[ $i ]->menu_order = $i + 1;
							}
						} else {

							$menu_order = count( $items );

							foreach ( $currency_switcher_menu_items as $added_item ) {

								$menu_order++;
								$added_item->menu_order = $menu_order;
								$items[]                = $added_item;
							}
						}

						break;
					}
				}

				return $items;
			},
			10,
			2
		);

		add_action(
			'wp_enqueue_scripts',
			function() {

				wp_enqueue_script(
					'mphbmc-menu-currency-switcher',
					MPHB_Multi_Currency::get_plugin_url() . 'js/menu-currency-switcher.min.js',
					array( 'jquery' ),
					MPHB_Multi_Currency::get_plugin_version(),
					true
				);

				wp_localize_script(
					'mphbmc-menu-currency-switcher',
					'MPHBMCMenuData',
					array(
						'baseUrlPath' => get_site_url( null, '/', 'relative' ),
					)
				);
			}
		);
	}

	private function get_currency_switcher_menu_items( array $switcher_settings ): array {

		$currency_switcher_menu_items = array();

		$currency_exchange_rates = MPHB_Multi_Currency::get_currency_exchange_rates();
		$selected_currency_code  = Convert_Price_Handler::get_current_selected_currency_code();

		if ( 'dropdown' == $switcher_settings['mphbmc_menu_currency_switcher_type'] ) {

			$currency_switcher_menu_items[] = new Currency_Menu_Item(
				$selected_currency_code,
				array( 'mphbmc-menu-currency-switcher-item-selected' )
			);
		}

		foreach ( $currency_exchange_rates as $exchange_rate_data ) {

			if ( 'dropdown' == $switcher_settings['mphbmc_menu_currency_switcher_type'] &&
				$selected_currency_code != $exchange_rate_data['mphbmc_currency_code'] ) {

				$currency_switcher_menu_items[] = new Currency_Menu_Item(
					$exchange_rate_data['mphbmc_currency_code'],
					array(),
					$currency_switcher_menu_items[0]->ID
				);

			} elseif ( 'dropdown' != $switcher_settings['mphbmc_menu_currency_switcher_type'] ) {

				$currency_switcher_menu_items[] = new Currency_Menu_Item(
					$exchange_rate_data['mphbmc_currency_code']
				);
			}
		}

		return $currency_switcher_menu_items;
	}
}
