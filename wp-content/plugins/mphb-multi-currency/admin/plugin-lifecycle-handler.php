<?php

namespace MPHB\Addons\MPHB_Multi_Currency;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class Plugin_Lifecycle_Handler {

	private const REQUIRED_HOTEL_BOOKING_PLUGIN_VERSION = '3.9.14';

	private $is_wp_environment_suited_for_plugin = true;

	public function __construct( string $plugin_main_file_path ) {

		add_action(
			'plugins_loaded',
			function() {
				$this->checkIsWPEnvironmentSuitedForPlugin();
			},
			-1
		);

		register_activation_hook(
			$plugin_main_file_path,
			function( $isNetworkWide = false ) {
				$this->activate_plugin( $isNetworkWide );
			}
		);

        add_action(
			'mphb_activated',
			function() {
				$this->activate_plugin();
			}
		);

		// add installation for a new site in multisite WordPress
		add_action(
			version_compare( get_bloginfo( 'version' ), '5.1', '>=' ) ? 'wp_initialize_site' : 'wpmu_new_blog',
			/**
			 * @param $blog in case of wp_initialize_site action is WP_Site otherwise int (site id)
			 */
			function( $blog ) {
				$blogId = is_int( $blog ) ? $blog : $blog->blog_id;
				$this->activate_plugin( false, $blogId );
			}
		);

		register_deactivation_hook(
			$plugin_main_file_path,
			function() {
				$this->deactivate_plugin();
			}
		);

		register_uninstall_hook( $plugin_main_file_path, array( __CLASS__, 'uninstall_plugin' ) );

		// initialize EDD updater
		if ( ! wp_doing_ajax() ) {

			add_action(
				'admin_init',
				function() use ( $plugin_main_file_path ) {

					if ( MPHB_Multi_Currency::is_edd_license_enabled() ) {

						new EDD_Plugin_Updater(
							MPHB_Multi_Currency::get_plugin_source_server_url(),
							$plugin_main_file_path,
							array(
								'version' => MPHB_Multi_Currency::get_plugin_version(),
								'license' => MPHB_Multi_Currency::get_license_key(),
								'item_id' => MPHB_Multi_Currency::get_product_id(),
								'author'  => MPHB_Multi_Currency::get_plugin_author_name(),
							)
						);
					}
				}
			);
		}
	}

	private function checkIsWPEnvironmentSuitedForPlugin(): void {

		$is_required_version_of_hotel_booking_plugin_active = false;

		if ( class_exists( 'HotelBookingPlugin' ) ) {

			$is_required_version_of_hotel_booking_plugin_active = function_exists( 'mphb_version_at_least' ) &&
				mphb_version_at_least( static::REQUIRED_HOTEL_BOOKING_PLUGIN_VERSION );
		}

		if ( ! $is_required_version_of_hotel_booking_plugin_active ) {

			$this->is_wp_environment_suited_for_plugin = false;

			$this->addErrorAdminNotice(
				sprintf(
					esc_html__( 'The Hotel Booking Currency Switcher addon requires the Hotel Booking plugin %s version or higher. Install and activate the core plugin for proper work.', 'mphb-multi-currency' ),
					static::REQUIRED_HOTEL_BOOKING_PLUGIN_VERSION
				)
			);
		}
	}

	private function addErrorAdminNotice( string $errorText ): void {

		add_action(
			'admin_notices',
			function() use ( $errorText ) {
				echo '<div class="notice notice-error">
					<div style="display: flex; align-items: center; gap: 10px; margin: 10px 10px 10px 0;">
						<svg style="overflow: visible;" width="40" height="40" xmlns="http://www.w3.org/2000/svg"><path fill="#d63638" d="M39.375 20c0 10.703-8.675 19.375-19.375 19.375S.625 30.703.625 20C.625 9.303 9.3.625 20 .625S39.375 9.303 39.375 20ZM20 23.906a3.594 3.594 0 1 0 0 7.188 3.594 3.594 0 0 0 0-7.188ZM16.588 10.99l.58 10.625a.937.937 0 0 0 .936.886h3.792c.498 0 .91-.39.936-.886l.58-10.625a.938.938 0 0 0-.936-.989h-4.952a.937.937 0 0 0-.936.989Z"/></svg>
						<p>' . esc_html( $errorText ) . '</p>
					</div></div>';
			}
		);
	}

	public function is_wp_environment_suited_for_plugin(): bool {
		return $this->is_wp_environment_suited_for_plugin;
	}

	private function activate_plugin( $isNetworkWide = false, $multisiteBlogId = 0 ) {}

	private function deactivate_plugin() {}

	public static function uninstall_plugin() {}
}
