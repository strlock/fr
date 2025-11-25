<?php

namespace MPHB\Addons\MPHB_Multi_Currency;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Convert_Price_Handler {

	const COOKIES_AND_SESSION_SELECTED_CURRENCY = 'mphbmc_selected_currency';

	private $is_price_convertor_on = true;


	public function __construct() {

		// we use session for select currency widget
		add_action(
			'init',
			function() {
				if ( MPHB_Multi_Currency::is_currency_exchange_rates_on() ) {

					\MPHB\Libraries\WP_SessionManager\wp_session_start();
				}
			},
			1
		);

		add_action(
			'widgets_init',
			function() {

				register_widget( 'MPHB\Addons\MPHB_Multi_Currency\Currency_Switcher_Widget' );
			}
		);

		new Menu_Currency_Switcher_Handler();

		// switch to default currency if we at new booking creation in admin area
		if ( is_admin() && ! wp_doing_ajax() &&
			! empty( $_REQUEST['page'] ) && 'mphb_add_new_booking' === $_REQUEST['page']
		) {

			$default_currency_code                                     = MPHB_Multi_Currency::get_default_currency_code();
			$_SESSION[ static::COOKIES_AND_SESSION_SELECTED_CURRENCY ] = $default_currency_code;
			$_COOKIE[ static::COOKIES_AND_SESSION_SELECTED_CURRENCY ]  = $default_currency_code;
	
			add_action(
				'init', // send_headers hook does not work in admin area
				function() use ( $default_currency_code ) {
	
					$this->set_cookies( $default_currency_code );
				}
			);

		} else {

			// set selected currency to cookies of subsite to overwrite main site cookie on multisite environment with path "/"
			add_action(
				'send_headers',
				function() {

					$base_url_path = get_site_url( null, '/', 'relative' );

					if ( is_multisite() &&
						empty( $_COOKIE[ static::COOKIES_AND_SESSION_SELECTED_CURRENCY ] ) &&
						'/' !== $base_url_path ) {

						$this->set_cookies( static::get_current_selected_currency_code() );
					}
				}
			);
		}

		// add script for checkout update info
		add_action(
			'wp_enqueue_scripts',
			function() {

				if ( MPHB()->settings()->pages()->getCheckoutPageId() == get_the_ID() ) {

					wp_enqueue_script(
						'mphbmc-checkout-page',
						MPHB_Multi_Currency::get_plugin_url() . 'js/checkout-page.min.js',
						array( 'jquery' ),
						'1',
						true
					);

					wp_localize_script(
						'mphbmc-checkout-page',
						'MPHBMCData',
						array(
							'ajaxUrl' => admin_url( 'admin-ajax.php' ),
						)
					);
				}
			}
		);

		add_action(
			'wp_ajax_mphbmc_convert_sum_from_selected_to_default_currency',
			function() {
				$this->ajax_convert_sum_from_selected_to_default_currency();
			}
		);

		add_action(
			'wp_ajax_nopriv_mphbmc_convert_sum_from_selected_to_default_currency',
			function() {
				$this->ajax_convert_sum_from_selected_to_default_currency();
			}
		);

		$is_ajax_request_with_price_conversion = wp_doing_ajax() && ! empty( $_REQUEST['action'] ) && (
			'mphb_get_free_accommodations_amount' == $_REQUEST['action'] ||
			'mphb_update_checkout_info' == $_REQUEST['action'] ||
			'mphb_update_rate_prices' == $_REQUEST['action'] ||
			'mphb_apply_coupon' == $_REQUEST['action'] ||
			'mphb_get_room_type_calendar_data' == $_REQUEST['action'] ||
			'mphb_get_room_type_availability_data' == $_REQUEST['action']
		);

		if ( MPHB_Multi_Currency::is_currency_exchange_rates_on() &&
			( ! is_admin() || ( wp_doing_ajax() && $is_ajax_request_with_price_conversion ) ) ) {

			$this->add_hotel_booking_hooks();
			$this->add_hotel_booking_payment_request_hooks();
			$this->add_woocommerce_hooks();
		}
	}

	private function set_cookies( string $currency_code ) {

		if ( is_multisite() ) {

			setcookie(
				static::COOKIES_AND_SESSION_SELECTED_CURRENCY,
				$currency_code,
				time() + 60 * 60 * 24 * 10, // 10 days
				get_site_url( null, '/', 'relative' ),
				COOKIE_DOMAIN
			);
		} else {

			mphb_set_cookie(
				static::COOKIES_AND_SESSION_SELECTED_CURRENCY,
				$currency_code,
				time() + 60 * 60 * 24 * 10 // 10 days
			);
		}
	}


	private function add_hotel_booking_hooks(): void {

		add_filter(
			'mphb_format_price_parameters',
			function( $price_and_atts ) {

				if ( $this->is_convertion_have_to_be_done() ) {

					$selected_currency_exchange_rate_data = static::get_current_selected_currency_exchange_rate_data();

					// change decimals in price only when it is set to value > 0 for another currency
					if ( ! isset( $price_and_atts['attributes']['decimals'] ) || 0 != $price_and_atts['attributes']['decimals'] ) {

						$price_and_atts['attributes']['decimals'] = $selected_currency_exchange_rate_data['mphbmc_number_of_decimals'];
					}

					$price_and_atts['attributes']['currency_position'] = $selected_currency_exchange_rate_data['mphbmc_currency_position'];

					// overwrite currency simbol only when it is not empty
					// because we do not want to show it at all otherwise
					if ( ! empty( $price_and_atts['attributes']['currency_symbol'] ) ) {

						$price_and_atts['attributes']['currency_symbol'] = MPHB()->settings()->currency()->
						getBundle()->getSymbol( $selected_currency_exchange_rate_data['mphbmc_currency_code'] );
					}

					$price_and_atts['price'] = $price_and_atts['price'] * $selected_currency_exchange_rate_data['mphbmc_currency_rate'];
				}

				return $price_and_atts;

			},
			9999,
			1
		);

		// use 51 priority to make sure we show this message right after the total
		// see MPHB\Shortcodes\CheckoutShortcode\StepCheckout
		// line with: add_action( 'mphb_sc_checkout_form', array( '\MPHB\Views\Shortcodes\CheckoutView', 'renderTotalPrice' ), 50 );
		add_action(
			'mphb_sc_checkout_form',
			function( $booking, $roomDetails ) {

				if ( ! $this->is_convertion_have_to_be_done() ) {
					return;
				}

				$deposit_sum     = $booking->calcDepositAmount();
				$total_price     = $booking->getTotalPrice();
				$is_show_deposit = MPHB()->settings()->main()->getConfirmationMode() === 'payment'
				&& MPHB()->settings()->payment()->getAmountType() === 'deposit'
				&& ! mphb_is_create_booking_page()
				&& $deposit_sum < $total_price; // If not in the time frame, then they both will be equal

				$sum_for_payment = $is_show_deposit ? $deposit_sum : $total_price;

				$this->echo_checkout_form_conversion_message( $sum_for_payment );

			},
			51,
			2
		);

		// we do not want to convert prices in emails
		add_action(
			'mphb_before_send_mail',
			function() {
				$this->is_price_convertor_on = false;
			}
		);

		add_action(
			'mphb_after_send_mail',
			function() {
				$this->is_price_convertor_on = true;
			}
		);

		add_filter(
			'mphb_sc_search_results_data_room_price',
			function( $data_room_price_for_book_button ) {

				if ( $this->is_convertion_have_to_be_done() ) {

					$selected_currency_exchange_rate_data = static::get_current_selected_currency_exchange_rate_data();
					$data_room_price_for_book_button      = $data_room_price_for_book_button * $selected_currency_exchange_rate_data['mphbmc_currency_rate'];
				}

				return $data_room_price_for_book_button;
			},
			9999,
			1
		);

		add_filter(
			'mphb_public_js_data',
			function( $mphb_public_js_data ) {

				if ( $this->is_convertion_have_to_be_done() ) {

					$selected_currency_exchange_rate_data = static::get_current_selected_currency_exchange_rate_data();

					$mphb_public_js_data['_data']['settings']['currency']['code'] = $selected_currency_exchange_rate_data['mphbmc_currency_code'];

					$price_format_for_js = MPHB()->settings()->currency()->getPriceFormat(
						MPHB()->settings()->currency()->getBundle()->getSymbol(
							$selected_currency_exchange_rate_data['mphbmc_currency_code']
						),
						$selected_currency_exchange_rate_data['mphbmc_currency_position']
					);

					$mphb_public_js_data['_data']['settings']['currency']['price_format'] = $price_format_for_js;

					$mphb_public_js_data['_data']['settings']['currency']['decimals'] = $selected_currency_exchange_rate_data['mphbmc_number_of_decimals'];
				}

				return $mphb_public_js_data;
			},
			9999,
			1
		);
	}

	private function add_hotel_booking_payment_request_hooks(): void {

		add_action(
			'mphb_sc_payment_request_checkout-before_submit_button',
			function( $booking, $sum_for_payment ) {

				if ( ! $this->is_convertion_have_to_be_done() ) {
					return;
				}

				$this->echo_checkout_form_conversion_message( $sum_for_payment );

			},
			9999,
			2
		);
	}

	private function is_woocommerce_hooks_must_be_on(): bool {

		if ( ! class_exists( '\MPHBW\Plugin' ) ) {
			return false;
		}

		$is_woocommerce_checkout_page = false;

		if ( function_exists( 'is_checkout' ) ) {

			$is_woocommerce_checkout_page = is_checkout();
		}

		return $is_woocommerce_checkout_page;
	}

	private function add_woocommerce_hooks(): void {

		// we do not want to convert prices in woocommerce emails
		$woo_email_ids_without_conversion = array(
			'new_order',
			'cancelled_order',
			'customer_completed_order',
			'customer_invoice',
			'customer_new_account',
			'customer_note',
			'customer_on_hold_order',
			'customer_processing_order',
			'customer_refunded_order',
			'failed_order',
			'merchant_notification',
		);
		foreach ( $woo_email_ids_without_conversion as $email_id ) {
			add_filter(
				'woocommerce_email_recipient_' . $email_id,
				function( $recipient ) {
					$this->is_price_convertor_on = false;
					return $recipient;
				},
				9999,
				1
			);
		}

		add_action(
			'woocommerce_email_sent',
			function() {
				$this->is_price_convertor_on = true;
			}
		);

		add_filter(
			'wc_price_args',
			function( $price_format_args ) {

				if ( ! $this->is_convertion_have_to_be_done() ||
				! $this->is_woocommerce_hooks_must_be_on() ) {
					return $price_format_args;
				}

				$selected_currency_exchange_rate_data = static::get_current_selected_currency_exchange_rate_data();

				$price_format_args['currency'] = $selected_currency_exchange_rate_data['mphbmc_currency_code'];
				$price_format_args['decimals'] = $selected_currency_exchange_rate_data['mphbmc_number_of_decimals'];

				$price_format_args['price_format'] = '%1$s%2$s';

				switch ( $selected_currency_exchange_rate_data['mphbmc_currency_position'] ) {
					case 'before':
						$price_format_args['price_format'] = '%1$s%2$s';
						break;
					case 'after':
						$price_format_args['price_format'] = '%2$s%1$s';
						break;
					case 'before_space':
						$price_format_args['price_format'] = '%1$s&nbsp;%2$s';
						break;
					case 'after_space':
						$price_format_args['price_format'] = '%2$s&nbsp;%1$s';
						break;
				}

				return $price_format_args;

			},
			9999,
			1
		);

		add_filter(
			'raw_woocommerce_price',
			function( $price, $original_price ) {

				if ( ! $this->is_convertion_have_to_be_done() || ! $this->is_woocommerce_hooks_must_be_on() ) {
					return $price;
				}

				// Convert to float to avoid issues on PHP 8.
				$price    = (float) $original_price;
				$negative = $price < 0;

				$selected_currency_exchange_rate_data = static::get_current_selected_currency_exchange_rate_data();

				$price = $price * $selected_currency_exchange_rate_data['mphbmc_currency_rate'];

				return $negative ? -1 * $price : $price;

			},
			9999,
			2
		);

		// it has to have prority 11 to make sure payment warning shown after price details table
		// see woocommerce/templates/checkout/form-checkout.php
		add_action(
			'woocommerce_checkout_order_review',
			function() {

				if ( ! $this->is_convertion_have_to_be_done() || ! $this->is_woocommerce_hooks_must_be_on() ) {
					return;
				}

				$this->echo_checkout_form_conversion_message( WC()->cart->get_total( false ) );
			},
			11
		);
	}

	private function is_convertion_have_to_be_done(): bool {

		return $this->is_price_convertor_on &&
			static::get_current_selected_currency_code() != MPHB()->settings()->currency()->getCurrencyCode() &&
			! empty( static::get_current_selected_currency_exchange_rate_data() );
	}

	private function echo_checkout_form_conversion_message( $sum_for_payment ): void {

		$this->is_price_convertor_on = false;

		$message = sprintf(
			__( "This price is converted to show you the approximate cost in %1\$s. <strong>You'll pay in %2\$s ( %3\$s )</strong>. The exchange rate might change before you pay. Keep in mind that your card issuer may charge you a foreign transaction fee.", 'mphb-multi-currency' ),
			static::get_current_selected_currency_code(),
			MPHB()->settings()->currency()->getCurrencyCode(),
			mphb_format_price( $sum_for_payment )
		);

		echo '<p id="mphbmc-payment-warning">' . wp_kses_post( $message ) . '</p>';

		$this->is_price_convertor_on = true;
	}

	private function ajax_convert_sum_from_selected_to_default_currency(): void {

		$sum_for_conversion = ! empty( $_GET['sum_for_conversion'] ) ? trim( sanitize_text_field( wp_unslash( $_GET['sum_for_conversion'] ) ) ) : 0;

		$sum_for_conversion = str_replace( MPHB()->settings()->currency()->getPriceThousandSeparator(), '', $sum_for_conversion );
		$sum_for_conversion = str_replace( MPHB()->settings()->currency()->getPriceDecimalsSeparator(), '.', $sum_for_conversion );

		$float_value = (float) $sum_for_conversion;
		strval( $float_value ) == $sum_for_conversion ? $sum_for_conversion = $float_value : 0;

		$selected_currency_exchange_rate_data = static::get_current_selected_currency_exchange_rate_data();

		$converted_sum = number_format(
			$sum_for_conversion / $selected_currency_exchange_rate_data['mphbmc_currency_rate'],
			MPHB()->settings()->currency()->getPriceDecimalsCount(),
			MPHB()->settings()->currency()->getPriceDecimalsSeparator(),
			MPHB()->settings()->currency()->getPriceThousandSeparator()
		);
		$converted_sum = mphb_trim_zeros( $converted_sum );

		if ( 'before_space' == MPHB()->settings()->currency()->getCurrencyPosition() ) {

			$converted_sum = '&nbsp;' . $converted_sum;

		} elseif ( 'after_space' == MPHB()->settings()->currency()->getCurrencyPosition() ) {

			$converted_sum .= '&nbsp;';
		}

		wp_send_json_success( array( 'converted_sum' => $converted_sum ) );
	}


	public static function get_current_selected_currency_code(): string {

		$selected_currency_code = '';

		if ( ! empty( $_SESSION[ static::COOKIES_AND_SESSION_SELECTED_CURRENCY ] ) ) {

			$selected_currency_code = $_SESSION[ static::COOKIES_AND_SESSION_SELECTED_CURRENCY ];
		}

		if ( ! empty( $_COOKIE[ static::COOKIES_AND_SESSION_SELECTED_CURRENCY ] ) &&
			$selected_currency_code != $_COOKIE[ static::COOKIES_AND_SESSION_SELECTED_CURRENCY ] ) {

			$selected_currency_code = trim( sanitize_text_field( wp_unslash( $_COOKIE[ static::COOKIES_AND_SESSION_SELECTED_CURRENCY ] ) ) );

			$_SESSION[ static::COOKIES_AND_SESSION_SELECTED_CURRENCY ] = $selected_currency_code;
		}

		if ( empty( $selected_currency_code ) ||
			'' == MPHB()->settings()->currency()->getBundle()->getLabel( $selected_currency_code ) ) {

			$selected_currency_code                                    = MPHB_Multi_Currency::get_default_currency_code();
			$_SESSION[ static::COOKIES_AND_SESSION_SELECTED_CURRENCY ] = $selected_currency_code;
		}

		return $selected_currency_code;
	}

	public static function get_current_selected_currency_exchange_rate_data():?array {

		$exchange_rates         = MPHB_Multi_Currency::get_currency_exchange_rates();
		$selected_currency_code = static::get_current_selected_currency_code();

		foreach ( $exchange_rates as $exchange_rates_data ) {

			if ( $selected_currency_code == $exchange_rates_data['mphbmc_currency_code'] ) {
				return $exchange_rates_data;
			}
		}

		return array();
	}
}
