<?php
/**
 * Plugin Name: Hotel Booking Multi-Currency
 * Plugin URI: https://motopress.com/products/hotel-booking-multi-currency/
 * Description: Enable travelers to switch currencies on your rental property site.
 * Version: 1.2.6
 * Requires at least: 5.1
 * Requires PHP: 7.1
 * Author: MotoPress
 * Author URI: https://motopress.com/
 * License: GPLv2 or later
 * Text Domain: mphb-multi-currency
 * Domain Path: /languages
 */

namespace MPHB\Addons\MPHB_Multi_Currency;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class MPHB_Multi_Currency {

	private static $plugin_classes = array(
		'Plugin_Lifecycle_Handler'       => 'admin/plugin-lifecycle-handler.php',
		'EDD_Plugin_Updater'             => 'admin/edd-plugin-updater.php',
		'Plugin_Settings_Handler'        => 'admin/plugin-settings-handler.php',
		'Main_Settings_Group'            => 'admin/main-settings-group.php',
		'License_Settings_Group'         => 'admin/license-settings-group.php',
		'Menu_Currency_Switcher_Handler' => 'includes/menu-currency-switcher-handler.php',
		'Currency_Switcher_Widget'       => 'includes/currency-switcher-widget.php',
		'Convert_Price_Handler'          => 'includes/convert-price-handler.php',
		'Currency_Menu_Item'             => 'includes/currency-menu-item.php',
	);

	private static $instance;

	private $plugin_dir;
	private $plugin_url;

	private $plugin_lifecycle_handler;

	// prevent cloning of singleton
	public function __clone() {}
	public function __wakeup() {}

	public static function get_instance() {

		if ( ! isset( self::$instance ) ) {

			self::$instance = new MPHB_Multi_Currency();
		}

		return self::$instance;
	}

	private function __construct() {

		$this->plugin_dir = plugin_dir_path( __FILE__ );
		$this->plugin_url = plugin_dir_url( __FILE__ );

		spl_autoload_register(
			function( $class ) {

				$this->load_plugin_class( $class );
			}
		);

		load_plugin_textdomain(
			'mphb-multi-currency',
			false,
			$this->plugin_dir . 'languages/'
		);

		$this->plugin_lifecycle_handler = new Plugin_Lifecycle_Handler( __FILE__ );

		add_action(
			'plugins_loaded',
			function() {

				// do not initialize plugin if environment does not suite it
				if ( ! $this->plugin_lifecycle_handler->is_wp_environment_suited_for_plugin() ) {
					return;
				}

				new Plugin_Settings_Handler();
				new Convert_Price_Handler();

				$this->change_super_cache_settings();
			},
			11 // Hotel Booking uses "plugins_loaded" with priority 10 so we want to be loaded after it
		);

		add_action(
			'admin_enqueue_scripts',
			function() {

				wp_enqueue_style(
					'mphb_multi_currency_admin_styles',
					$this->plugin_url . 'css/admin.css',
					array(),
					'1.0'
				);
			}
		);
	}

	private function change_super_cache_settings(): void {

		if ( ! function_exists('wp_cache_replace_line') ) {
			return;
		}

		// Overwrite SuperCache advanced setting wp_super_cache_late_init
		// to be able to turn off cache when we need
		if ( isset( $_POST['wp_super_cache_late_init'] ) ) {

			$_POST['wp_super_cache_late_init'] = 1;
		}

		global $wp_cache_config_file, $wp_super_cache_late_init;

		if ( ! $wp_super_cache_late_init ) {

			wp_cache_replace_line(
				'^ *\$wp_super_cache_late_init',
				'$wp_super_cache_late_init = 1;',
				$wp_cache_config_file
			);
		}

		// turn off Super Cache for not default currencies
		if ( Convert_Price_Handler::get_current_selected_currency_code() != self::get_default_currency_code() ) {

			define( 'DONOTCACHEPAGE', 1 );
			define( 'WPSC_SERVE_DISABLED', 1 );
		}
	}

	/**
	 * @return false or string with class file path
	 */
	private function load_plugin_class( string $class_name_with_namespace ) {

		$class_name_with_namespace = ltrim( $class_name_with_namespace, '\\' );

		if ( false === strpos( $class_name_with_namespace, __NAMESPACE__ ) ) {
			return false;
		}

		$class_name_without_namespace = str_replace( __NAMESPACE__ . '\\', '', $class_name_with_namespace );

		if ( ! empty( static::$plugin_classes[ $class_name_without_namespace ] ) ) {

			$class_file_path = $this->plugin_dir . static::$plugin_classes[ $class_name_without_namespace ];

			if ( file_exists( $class_file_path ) ) {

				require_once $class_file_path;

				return $class_file_path;
			}
		}
		
		return false;
	}

	public static function get_plugin_dir(): string {
		return static::get_instance()->plugin_dir;
	}

	public static function get_plugin_url(): string {
		return static::get_instance()->plugin_url;
	}

	public static function get_product_id(): string {
		return '1195389';
	}

	public static function get_license_key(): string {
		return get_option( 'mphbmc_license_key', '' );
	}

	public static function set_license_key( string $license_key ): void {

		$old_license_key = static::get_license_key();

		if ( $old_license_key && $old_license_key !== $license_key ) {

			// new license has been entered, so must reactivate
			delete_option( 'mphbmc_license_status' );
		}

		if ( ! empty( $license_key ) ) {

			update_option( 'mphbmc_license_key', $license_key );

		} else {

			delete_option( 'mphbmc_license_key' );
		}
	}

	/**
	 * @param $license_status can be active, inactive or empty if plugin license is deactivated
	 */
	public static function set_license_status( string $license_status ): void {
		update_option( 'mphbmc_license_status', $license_status );
	}

	public static function is_edd_license_enabled(): bool {
		return (bool) apply_filters( 'mphbmc_use_edd_license', true );
	}

	public static function get_plugin_author_name(): string {
		$plugin_data = get_plugin_data( __FILE__, false, false );
		return isset( $plugin_data['Author'] ) ? $plugin_data['Author'] : '';
	}

	public static function get_plugin_version(): string {
		$plugin_data = get_plugin_data( __FILE__, false, false );
		return isset( $plugin_data['Version'] ) ? $plugin_data['Version'] : '';
	}

	public static function get_plugin_source_server_url(): string {
		$plugin_data = get_plugin_data( __FILE__, false, false );
		return isset( $plugin_data['PluginURI'] ) ? $plugin_data['PluginURI'] : '';
	}

	/**
	 * @return stdClass|null
	 */
	public static function get_license_data_from_remote_server() {

		$api_params = array(
			'edd_action' => 'check_license',
			'license'    => static::get_license_key(),
			'item_id'    => static::get_product_id(),
			'url'        => home_url(),
		);

		$check_license_url = add_query_arg( $api_params, static::get_plugin_source_server_url() );

		// Call the custom API.
		$response = wp_remote_get(
			$check_license_url,
			array(
				'timeout'   => 15,
				'sslverify' => false,
			)
		);

		if ( is_wp_error( $response ) ) {
			return null;
		}

		return json_decode( wp_remote_retrieve_body( $response ) );
	}

	public static function get_default_currency_code(): string {

		$currency_exchange_rates = static::get_currency_exchange_rates();

		$default_currency_code = '';

		if ( empty( $currency_exchange_rates[0] ) ) {

			$default_currency_code = MPHB()->settings()->currency()->getCurrencyCode();

			if ( empty( $default_currency_code ) ) {

				$default_currency_code = MPHB()->settings()->currency()->getDefaultCurrency();
			}

		} else {

			$default_currency_code = $currency_exchange_rates[0]['mphbmc_currency_code'];
		}

		return $default_currency_code;
	}

	public static function get_currency_exchange_rates(): array {
		return get_option( 'mphbmc_exchange_rates', array() );
	}

	public static function is_currency_exchange_rates_on(): bool {
		return 1 < count( static::get_currency_exchange_rates() );
	}

	public static function get_menu_currency_switcher_settings(): array {
		return get_option( 'mphbmc_menu_currency_switcher', array() );
	}
}

// init plugin
MPHB_Multi_Currency::get_instance();
